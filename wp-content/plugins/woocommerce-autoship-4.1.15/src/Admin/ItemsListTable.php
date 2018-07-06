<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WC_Autoship_Admin_ItemsListTable extends WP_List_Table {
	public function __construct() {
		global $status, $page;
	
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'item',
			'plural'    => 'items',
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
	
	function column_product_sku( $item ) {
		$actions = array(
			'edit' => sprintf( '<a href="%s?post=%d&action=edit">%s</a>', admin_url( '/post.php' ), $item->product_id, __( 'Edit', 'wc-autoship' ) ),
		);
		$id = ( ! empty( $item->variation_id ) ) ? $item->variation_id : $item->product_id;
		$sku = ( ! empty( $item->product_sku ) ) ? $item->product_sku : $item->product_id;
		$product = wc_get_product( $id );
		if ( $product ) {
			return sprintf( '#%1$s %2$s%3$s',
				$sku,
				wc_autoship_get_product_display_name( $product ),
				$this->row_actions( $actions )
			);
		}
		return sprintf( '#%1$s%2$s',
			$sku,
			$this->row_actions( $actions )
		);
	}
	
	function column_user_login( $item ) {
		//Build row actions
		$actions = array(
			'user' => sprintf( '<a href="%s?user_id=%s&amp;wp_http_referer=%s">%s</a>', admin_url( '/user-edit.php' ), $item->customer_id, $_SERVER['REQUEST_URI'], __( 'User', 'wc-autoship' ) ),
			'schedules' => sprintf( '<a href="%s">%s</a>', add_query_arg( array( 'customer_id' => $item->customer_id ), wc_get_account_endpoint_url( 'autoship-schedules' ) ), __( 'Schedules', 'wc-autoship' ) )
		);
	
		//Return the title contents
		return sprintf( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
			$item->user_login,
			$item->customer_id,
			$this->row_actions( $actions )
		);
	}
	
	function column_last_order_date( $item ) {
		if ( empty( $item->last_order_date ) || $item->last_order_date == '0000-00-00' ) {
			return '';
		}
		return date_i18n( get_option( 'date_format' ), strtotime( $item->last_order_date ) );
	}
	
	function column_next_order_date( $item ) {
		if ( empty( $item->next_order_date ) || $item->next_order_date == '0000-00-00' ) {
			return '';
		}
		return date_i18n( get_option( 'date_format' ), strtotime( $item->next_order_date ) );
	}
	
	function column_autoship_status( $item ) {
		$icon = ( $item->autoship_status > 0 ) ? 'dashicons-controls-play' : 'dashicons-controls-pause';
		$name = ( $item->autoship_status > 0 ) ? __( 'Active', 'wc-autoship' ) : __( 'Paused', 'wc-autoship' );
		return sprintf( '<span class="dashicons %s" title="%s"></span>%s', $icon, esc_attr( $name ), esc_html( $name ) );
	}
		
	function get_columns(){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'product_sku' => __( 'Product', 'wc-autoship' ),
			'quantity' => __( 'Quantity', 'wc-autoship' ),
			'user_login' => __( 'Customer', 'wc-autoship' ),
			'autoship_frequency' => __( 'Frequency', 'wc-autoship' ),
			'autoship_status' => __( 'Status', 'wc-autoship' ),
			'last_order_date' => __( 'Last Order Date', 'wc-autoship' ),
			'next_order_date' => __( 'Next Order Date', 'wc-autoship' ),
		);
		return $columns;
	}
	
	function get_sortable_columns() {
		$sortable_columns = array(
			'product_sku' => array( 'product_sku', false ),
			'user_login' => array( 'user_login', false ),
			'autoship_frequency' => array( 'autoship_frequency', false ),
			'autoship_status' => array( 'autoship_status', false ),
			'last_order_date' => array( 'last_order_date', false ),
			'next_order_date' => array( 'next_order_date', false ),
		);
		return $sortable_columns;
	}
	
	function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete Schedule Items', 'wc-autoship' )
		);
		return $actions;
	}
	
	function process_bulk_action() {
		global $wpdb;
		$current_user_id = get_current_user_id();
		if ( 'delete' === $this->current_action() ) {
			if ( isset( $_REQUEST['item'] ) ) {
				foreach ( $_REQUEST['item'] as $id ) {
					$schedule_id = $wpdb->get_var( $wpdb->prepare( "SELECT schedule_id FROM {$wpdb->prefix}wc_autoship_schedule_items WHERE id = %d LIMIT 0,1", $id ) );
					$customer_id = $wpdb->get_var( $wpdb->prepare( "SELECT customer_id FROM {$wpdb->prefix}wc_autoship_schedules WHERE id = %d LIMIT 0,1", $schedule_id ) );
					$product_data = $wpdb->get_row( $wpdb->prepare( "SELECT product_id, variation_id FROM {$wpdb->prefix}wc_autoship_schedule_items WHERE id = %d LIMIT 0,1", $id ) );
					$wpdb->delete( "{$wpdb->prefix}wc_autoship_schedule_items", array( 'id' => $id ) );
					$log_description = __( "User $current_user_id deleted Item $id from Schedule $schedule_id for Customer $customer_id", 'wc-autoship' );
					$action_value = $product_data->product_id . ':' . $product_data->variation_id;
					wc_autoship_log_action( $current_user_id, 'schedule_item_delete', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $schedule_id, $id, $action_value );
					wc_autoship_cache_delete( 'schedules_cart', 0, NULL, $schedule_id );
				}
			}
			wc_autoship_add_message( __( 'Autoship items deleted', 'wc-autoship' ) );
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
		

		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? preg_replace( '/[^a-z_]/', '', $_REQUEST['orderby'] ) : 'next_order_date';
		$order = ( ! empty( $_REQUEST['order'] ) ) ? preg_replace( '/[^a-z_]/', '', $_REQUEST['order'] ) : 'DESC';
		
		$search_clause = '1';
		if ( ! empty( $_REQUEST['s'] ) ) {
			$safe_search = addslashes( stripslashes( $_REQUEST['s'] ) );
			$conditions = array();
			if ( is_numeric( $safe_search ) ) {
				$conditions[] = sprintf( 'items.product_id = \'%s\'', (int) $safe_search );
				$conditions[] = sprintf( 'items.variation_id = \'%s\'', (int) $safe_search );
			}
			$columns = array(
				'products.post_title',
				'product_meta_sku.meta_value',
				'variation_meta_sku.meta_value',
				'users.user_login',
				'users.user_email'
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
				FROM {$wpdb->prefix}wc_autoship_schedule_items AS items
				LEFT JOIN {$wpdb->prefix}posts AS products ON(items.product_id = products.ID)
				LEFT JOIN {$wpdb->prefix}postmeta AS product_meta_sku ON(items.product_id = product_meta_sku.post_id AND product_meta_sku.meta_key = '_sku')
				LEFT JOIN {$wpdb->prefix}postmeta AS variation_meta_sku ON(items.variation_id = variation_meta_sku.post_id AND variation_meta_sku.meta_key = '_sku')
				LEFT JOIN {$wpdb->prefix}wc_autoship_schedules AS schedules ON(items.schedule_id = schedules.id)
				LEFT JOIN {$wpdb->prefix}users AS users ON(schedules.customer_id = users.ID)
				WHERE $search_clause"
			);
		} else {
			/*
			 * Total items
			 */
			$total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wc_autoship_schedule_items" );
		}
		
		$wpdb->query( "SET SQL_BIG_SELECTS=1" );
		$schedules = $wpdb->get_results( $wpdb->prepare(
			"SELECT items.id AS `id`,
			items.product_id AS `product_id`,
			items.variation_id AS `variation_id`,
			products.post_title,
			IF(variation_meta_sku.meta_value IS NOT NULL, variation_meta_sku.meta_value, product_meta_sku.meta_value) AS `product_sku`,
			items.qty AS `quantity`,
			schedules.customer_id AS `customer_id`,
			users.user_login AS `user_login`,
			schedules.autoship_frequency AS `autoship_frequency`,
			schedules.last_order_date AS `last_order_date`,
			schedules.next_order_date AS `next_order_date`,
			schedules.autoship_status AS `autoship_status`
			FROM {$wpdb->prefix}wc_autoship_schedule_items AS items
			LEFT JOIN {$wpdb->prefix}posts AS products ON(items.product_id = products.ID)
			LEFT JOIN {$wpdb->prefix}postmeta AS product_meta_sku ON(items.product_id = product_meta_sku.post_id AND product_meta_sku.meta_key = '_sku')
			LEFT JOIN {$wpdb->prefix}postmeta AS variation_meta_sku ON(items.variation_id = variation_meta_sku.post_id AND variation_meta_sku.meta_key = '_sku')
			LEFT JOIN {$wpdb->prefix}wc_autoship_schedules AS schedules ON(items.schedule_id = schedules.id)
			LEFT JOIN {$wpdb->prefix}users AS users ON(schedules.customer_id = users.ID)
			WHERE $search_clause
			GROUP BY items.id
			ORDER BY `{$orderby}` {$order}
			LIMIT %d, %d",
			( $current_page - 1 ) * $per_page, $per_page
		) );
	
	
		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		*/
		$this->items = $schedules;
	
	
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
