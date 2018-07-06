<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WC_Autoship_Admin_OrdersListTable extends WP_List_Table {
	public function __construct() {
		global $status, $page;
	
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'autoship_order',
			'plural'    => 'autoship_orders',
			'ajax'      => false
		) );
	}
	
	function column_default( $item, $column_name ) {
		return $item->{$column_name};
	}
	
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],
			$item->id
		);
	}
	
	function column_id( $item ) {
		//Build row actions
		$actions = array(
			'edit' => sprintf( '<a href="%s?post=%d&amp;action=edit">%s</a>', admin_url( '/post.php' ), $item->id, __( 'Edit', 'wc-autoship' ) )
		);
	
		//Return the title contents
		return sprintf( '#%1$s%2$s',
			$item->id,
			$this->row_actions( $actions )
		);
	}
	
	function column_user_login( $item ) {
		//Build row actions
		$actions = array(
			'user' => sprintf( '<a href="%s?user_id=%s&amp;wp_http_referer=%s">%s</a>', admin_url( '/user-edit.php' ), $item->customer_id, $_SERVER['REQUEST_URI'], __( 'User', 'wc-autoship' ) ),
			'autoship' => sprintf( '<a href="%s">%s</a>', add_query_arg( array( 'customer_id' => $item->customer_id ), wc_get_account_endpoint_url( 'autoship-schedules' ) ), __( 'Autoship', 'wc-autoship' ) )
		);
	
		//Return the title contents
		return sprintf( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
			$item->user_login,
			$item->customer_id,
			$this->row_actions( $actions )
		);
	}
	
	function column_order_date( $item ) {
		if ( empty( $item->order_date ) ) {
			return '';
		}
		return date_i18n( get_option( 'date_format' ), strtotime( $item->order_date ) );
	}
	
	function column_autoship_order_status( $item ) {
// 		return sprintf( '<mark class="%s processing tips" data-tip="%s">%s</mark>', sanitize_title( $item->order_status ), wc_get_order_status_name( $item->order_status ), wc_get_order_status_name( $item->order_status ) );
		return wc_get_order_status_name( $item->autoship_order_status );
	}
	
	function column_order_total( $item ) {
		return wc_price( $item->order_total, array( 'currency' => $item->order_currency ) );
	}
	
	function column_autoship_frequency( $item ) {
		$frequencies = explode( ', ', $item->autoship_frequency );
		$frequencies_map = array();
		foreach ( $frequencies as $frequency ) {
			$frequencies_map[ $frequency ] = true;
		}
		$unique_frequencies = array_keys( $frequencies_map );
		return implode( ', ', $unique_frequencies );
	}

	function column_order_type( $item ) {
		if ( ! empty( $item->autoship_order) ) {
			return __( 'Autoship Order', 'wc-autoship' );
		}
		return __( 'Autoship Checkout', 'wc-autoship' );
	}
		
	function get_columns(){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'id' => __( 'Order', 'wc-autoship' ),
			'user_login' => __( 'Customer', 'wc-autoship' ),
			'email'  => __( 'Email', 'wc-autoship' ),
			'last_name' => __( 'Last Name', 'wc-autoship' ),
			'first_name' => __( 'First Name', 'wc-autoship' ),
			'order_date' => __( 'Date', 'wc-autoship' ),
			'autoship_order_status' => __( 'Status', 'wc-autoship' ),
			'order_total' => __( 'Total', 'wc-autoship' ),
			'autoship_frequency' => __( 'Frequency', 'wc-autoship' ),
			'order_type' => __( 'Order Type', 'wc-autoship' )
		);
		return $columns;
	}
	
	function get_sortable_columns() {
		$sortable_columns = array(
			'id' => array( 'id', false ),
			'user_login' => array( 'user_login', false ),
			'email' => array( 'email', false ),
			'last_name' => array( 'last_name', false ),
			'first_name' => array( 'first_name', false ),
			'order_date' => array( 'order_date', false ),
			'autoship_order_status' => array( 'autoship_order_status', false ),
			'order_total' => array( 'order_total', false ),
			'autoship_frequency' => array( 'autoship_frequency', false )
		);
		return $sortable_columns;
	}
	
	function get_bulk_actions() {
		$actions = array(
			'trash'    => __( 'Trash', 'wc-autoship' )
		);
		return $actions;
	}
	
	function process_bulk_action() {
		if ( 'trash' === $this->current_action() ) {
			if ( isset( $_REQUEST['autoship_order'] ) ) {
				foreach ( $_REQUEST['autoship_order'] as $id ) {
					wp_trash_post( $id );
				}
			}
			wc_autoship_add_message( __( 'Autoship orders trashed', 'wc-autoship' ) );
		}
	}
	
	function prepare_items() {
		global $wpdb;
		
		$this->process_bulk_action();
	
		/*
		 * Pagination vars
		 */
		$per_page = isset( $_REQUEST['per_page'] ) ? $_REQUEST['per_page'] : 50;
		$total_items = 0;
	
		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
	
	
		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		*/
		$this->_column_headers = array( $columns, $hidden, $sortable );
		
		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();
		

		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? preg_replace( '/[^a-z_]/', '', $_REQUEST['orderby'] ) : 'id';
		$order = ( ! empty( $_REQUEST['order'] ) ) ? preg_replace( '/[^a-z_]/', '', $_REQUEST['order'] ) : 'DESC';
		
		$search_clause = '1';
		if ( ! empty( $_REQUEST['s'] ) ) {
			$safe_search = addslashes( stripslashes( $_REQUEST['s'] ) );
			$conditions = array(
				sprintf( 'orders.ID = \'%s\'', $safe_search ),
				sprintf( 'orders_total.meta_value = \'%s\'', $safe_search )
			);
			$columns = array(
				'users.user_login',
				'orders_email.meta_value',
				'orders_last_name.meta_value',
				'orders_first_name.meta_value'
			);
			foreach ( $columns as $name ) {
				$conditions[] = sprintf( '%s LIKE \'%%%%%s%%%%\'', $name, $safe_search );
			}
			$search_clause = '(' . implode( ' OR ', $conditions ) . ')';
			
			/*
			 * Include search conditions in total items
			 */
			$total_items = (int) $wpdb->get_var(
				"SELECT COUNT(*) 
				FROM {$wpdb->prefix}posts AS orders
				LEFT JOIN {$wpdb->prefix}postmeta AS orders_customer ON(orders.ID = orders_customer.post_id AND orders_customer.meta_key = '_customer_user')
				LEFT JOIN {$wpdb->prefix}users AS users ON(orders_customer.meta_value = users.ID)
				LEFT JOIN {$wpdb->prefix}postmeta AS orders_email ON(orders.ID = orders_email.post_id AND orders_email.meta_key = '_billing_email')
				LEFT JOIN {$wpdb->prefix}postmeta AS orders_last_name ON(orders.ID = orders_last_name.post_id AND orders_last_name.meta_key = '_billing_last_name')
				LEFT JOIN {$wpdb->prefix}postmeta AS orders_first_name ON(orders.ID = orders_first_name.post_id AND orders_first_name.meta_key = '_billing_first_name')
				LEFT JOIN {$wpdb->prefix}postmeta AS orders_total ON(orders.ID = orders_total.post_id AND orders_total.meta_key = '_order_total')
				WHERE orders.post_type = 'shop_order'
				AND orders.post_status IN('wc-completed', 'wc-processing', 'wc-pending', 'wc-failed', 'wc-on-hold', 'wc-cancelled')
				AND orders.ID IN(SELECT DISTINCT post_id 
								FROM {$wpdb->prefix}postmeta 
								WHERE meta_key = '_wc_autoship_order' OR meta_key = '_wc_autoship_init')
				AND $search_clause"
			);
		} else {
			/*
			 * Total items
			 */
			$total_items = (int) $wpdb->get_var(
				"SELECT COUNT(*) 
				FROM {$wpdb->prefix}posts
				WHERE post_type = 'shop_order'
				AND post_status IN('wc-completed', 'wc-processing', 'wc-pending', 'wc-failed', 'wc-on-hold', 'wc-cancelled')
				AND ID IN(SELECT DISTINCT post_id
						FROM {$wpdb->prefix}postmeta
						WHERE meta_key = '_wc_autoship_order' OR meta_key = '_wc_autoship_init')"
			);
		}
		
		$wpdb->query( "SET SQL_BIG_SELECTS=1" );
		$orders = $wpdb->get_results( $wpdb->prepare(
			"SELECT orders.ID AS `id`,
			orders_customer.meta_value AS `customer_id`,
			users.user_login AS `user_login`,
			orders_email.meta_value AS `email`,
			orders_last_name.meta_value AS `last_name`,
			orders_first_name.meta_value AS `first_name`,
			orders.post_date AS `order_date`,
			orders.post_status AS `autoship_order_status`,
			CAST(orders_total.meta_value AS DECIMAL(10,2)) AS `order_total`,
			orders_currency.meta_value AS `order_currency`,
			orders_autoship_order.meta_value AS `autoship_order`,
			GROUP_CONCAT(orders_frequencies.meta_value ORDER BY orders_frequencies.meta_value ASC SEPARATOR ', ') AS `autoship_frequency`
			FROM {$wpdb->prefix}posts AS orders
			LEFT JOIN {$wpdb->prefix}postmeta AS orders_customer ON(orders.ID = orders_customer.post_id AND orders_customer.meta_key = '_customer_user')
			LEFT JOIN {$wpdb->prefix}users AS users ON(orders_customer.meta_value = users.ID)
			LEFT JOIN {$wpdb->prefix}postmeta AS orders_email ON(orders.ID = orders_email.post_id AND orders_email.meta_key = '_billing_email')
			LEFT JOIN {$wpdb->prefix}postmeta AS orders_last_name ON(orders.ID = orders_last_name.post_id AND orders_last_name.meta_key = '_billing_last_name')
			LEFT JOIN {$wpdb->prefix}postmeta AS orders_first_name ON(orders.ID = orders_first_name.post_id AND orders_first_name.meta_key = '_billing_first_name')
			LEFT JOIN {$wpdb->prefix}postmeta AS orders_total ON(orders.ID = orders_total.post_id AND orders_total.meta_key = '_order_total')
			LEFT JOIN {$wpdb->prefix}postmeta AS orders_currency ON(orders.ID = orders_currency.post_id AND orders_currency.meta_key = '_order_currency')
			LEFT JOIN {$wpdb->prefix}postmeta AS orders_autoship_order ON(orders.ID = orders_autoship_order.post_id AND orders_autoship_order.meta_key = '_wc_autoship_order')
			LEFT JOIN (SELECT order_id, meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta
				LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS items ON(itemmeta.order_item_id = items.order_item_id)
				WHERE itemmeta.meta_key = '_wc_autoship_frequency') AS orders_frequencies ON(orders.ID = orders_frequencies.order_id)
			WHERE orders.post_type = 'shop_order'
			AND orders.post_status IN('wc-completed', 'wc-processing', 'wc-pending', 'wc-failed', 'wc-on-hold', 'wc-cancelled')
			AND orders.ID IN(SELECT DISTINCT post_id 
							FROM {$wpdb->prefix}postmeta 
							WHERE meta_key = '_wc_autoship_order' OR meta_key = '_wc_autoship_init')
			AND $search_clause
			GROUP BY orders.ID
			ORDER BY `{$orderby}` {$order}
			LIMIT %d, %d",
			( $current_page - 1 ) * $per_page, $per_page
		) );
	
	
		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		*/
		$this->items = $orders;
	
	
		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
		) );
	}
}
