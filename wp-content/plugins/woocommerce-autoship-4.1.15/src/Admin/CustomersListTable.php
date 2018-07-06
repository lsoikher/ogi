<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WC_Autoship_Admin_CustomersListTable extends WP_List_Table {
	public function __construct() {
		global $status, $page;
	
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'customer',
			'plural'    => 'customers',
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
	
	function column_user_login( $item ) {
		//Build row actions
		$actions = array(
			'user' => sprintf( '<a href="%s?user_id=%s&amp;wp_http_referer=%s">%s</a>', admin_url( '/user-edit.php' ), $item->id, $_SERVER['REQUEST_URI'], __( 'User', 'wc-autoship' ) ),
			'autoship' => sprintf( '<a href="%s">%s</a>', add_query_arg( array( 'customer_id' => $item->id ), wc_get_account_endpoint_url( 'autoship-schedules' ) ), __( 'Autoship', 'wc-autoship' ) )
		);
	
		//Return the title contents
		return sprintf( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
			$item->user_login,
			$item->id,
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
	
	function column_shipping_method( $item ) {
		$shipping = WC()->shipping();
		$methods = $shipping->get_shipping_methods();
		$method = explode( ':', $item->shipping_method_id );
		$actions = array(
			'edit' => sprintf( '<a href="%s">%s</a>', add_query_arg( array( 'customer_id' => $item->id ), get_permalink( get_option( 'wc_autoship_shipping_page_id' ) ) ), __( 'Edit', 'wc-autoship' ) )
		);
		if ( isset( $methods[ $method[0] ] ) ) {
			if ( isset( $methods[ $method[0] ]->title ) ) {
				return sprintf( '%s%s', $methods[ $method[0] ]->title, $this->row_actions( $actions ) );
			}
		}
		return sprintf( '%s%s', $item->shipping_method_id, $this->row_actions( $actions ) );
	}
	
	function column_payment_gateway( $item ) {
		$gateways = WC()->payment_gateways()->payment_gateways();
		$actions = array(
			'edit' => sprintf( '<a href="%s">%s</a>', add_query_arg( array( 'customer_id' => $item->id ), get_permalink( get_option( 'wc_autoship_billing_page_id' ) ) ), __( 'Edit', 'wc-autoship' ) )
		);
		if ( isset( $gateways[ $item->payment_gateway ] ) ) {
			return sprintf( '%s%s', $gateways[ $item->payment_gateway ]->title, $this->row_actions( $actions ) );
		}
		return sprintf( '%s%s', $item->payment_gateway, $this->row_actions( $actions ) );
	}
	
	function get_columns(){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'user_login'  => __( 'Username', 'wc-autoship' ),
			'user_email'  => __( 'Email', 'wc-autoship' ),
			'last_name' => __( 'Last Name', 'wc-autoship' ),
			'first_name' => __( 'First Name', 'wc-autoship' ),
			'last_order_date' => __( 'Last Autoship', 'wc-autoship' ),
			'next_order_date' => __( 'Next Autoship', 'wc-autoship' ),
			'schedules_active' => __( 'Active', 'wc-autoship' ),
			'schedules_paused' => __( 'Paused', 'wc-autoship' )
		);
		return $columns;
	}
	
	function get_sortable_columns() {
		$sortable_columns = array(
			'user_login' => array( 'user_login', false ),
			'user_email' => array( 'user_email', false ),
			'last_name' => array( 'last_name', false ),
			'first_name' => array( 'first_name', false ),
			'last_order_date' => array( 'last_order_date', false ),
			'next_order_date' => array( 'next_order_date', false ),
			'schedules_active' => array( 'schedules_active', false ),
			'schedules_paused' => array( 'schedules_paused', false )
		);
		return $sortable_columns;
	}
	
	function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete Autoship Data', 'wc-autoship' )
		);
		return $actions;
	}
	
	function process_bulk_action() {
		global $wpdb;
		if ( 'delete' === $this->current_action() ) {
			if ( isset( $_REQUEST['customer'] ) ) {
				foreach ( $_REQUEST['customer'] as $id ) {
					$wpdb->query( $wpdb->prepare( 
						"DELETE FROM {$wpdb->prefix}wc_autoship_schedule_items
						WHERE schedule_id IN(SELECT id FROM {$wpdb->prefix}wc_autoship_schedules
											WHERE customer_id = %d)",
						$id
					) );
					$wpdb->delete( "{$wpdb->prefix}wc_autoship_schedules", array( 'customer_id' => $id ) );
				}
			}
			wc_autoship_add_message( __( 'Autoship customers deleted', 'wc-autoship' ) );
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
			if ( is_numeric( $safe_search ) ) {
				$conditions = array(
					sprintf( 'users.ID = \'%s\'', $safe_search )
				);
			} else {
				$columns = array(
					'users.user_login',
					'users.user_email',
					'meta_last_name.meta_value',
					'meta_first_name.meta_value'
				);
				foreach ( $columns as $name ) {
					$conditions[] = sprintf( '%s LIKE \'%%%%%s%%%%\'', $name, $safe_search );
				}
			}
			$search_clause = '(' . implode( ' OR ', $conditions ) . ')';
			
			/*
			 * Include search conditions in total items
			 */
			$total_items = (int) $wpdb->get_var( 
				"SELECT COUNT(*)
				FROM {$wpdb->prefix}users AS users
				LEFT JOIN {$wpdb->prefix}usermeta AS meta_last_name ON(users.ID = meta_last_name.user_id AND meta_last_name.meta_key = 'billing_last_name')
				LEFT JOIN {$wpdb->prefix}usermeta AS meta_first_name ON(users.ID = meta_first_name.user_id AND meta_first_name.meta_key = 'billing_first_name')
				WHERE $search_clause"
			);
		} else {
			/*
			 * Total items
			 */
			$total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}users" );
		}
		
		$wpdb->query( "SET SQL_BIG_SELECTS=1" );
		$customers = $wpdb->get_results( $wpdb->prepare(
			"SELECT users.ID AS `id`,
			users.user_login AS `user_login`,
			users.user_email AS `user_email`,
			meta_last_name.meta_value AS `last_name`,
			meta_first_name.meta_value AS `first_name`,
			schedules_last_order_date.last_order_date AS `last_order_date`,
			schedules_next_order_date.next_order_date AS `next_order_date`,
			COUNT(schedules_active.id) AS `schedules_active`,
			COUNT(schedules_paused.id) AS `schedules_paused`
			FROM {$wpdb->prefix}users AS users
			LEFT JOIN {$wpdb->prefix}usermeta AS meta_last_name ON(users.ID = meta_last_name.user_id AND meta_last_name.meta_key = 'billing_last_name')
			LEFT JOIN {$wpdb->prefix}usermeta AS meta_first_name ON(users.ID = meta_first_name.user_id AND meta_first_name.meta_key = 'billing_first_name')
			LEFT JOIN ( SELECT customer_id, MAX(last_order_date) AS 'last_order_date'
						FROM {$wpdb->prefix}wc_autoship_schedules 
						GROUP BY customer_id ) AS schedules_last_order_date ON(users.ID = schedules_last_order_date.customer_id)
			LEFT JOIN ( SELECT customer_id, MIN(next_order_date) AS 'next_order_date'
						FROM {$wpdb->prefix}wc_autoship_schedules 
						WHERE autoship_status = 1 AND next_order_date >= CURDATE()
						GROUP BY customer_id ) AS schedules_next_order_date ON(users.ID = schedules_next_order_date.customer_id)
			LEFT JOIN {$wpdb->prefix}wc_autoship_schedules AS schedules_active ON(users.ID = schedules_active.customer_id AND schedules_active.autoship_status = 1)
			LEFT JOIN {$wpdb->prefix}wc_autoship_schedules AS schedules_paused ON(users.ID = schedules_paused.customer_id AND schedules_paused.autoship_status = 0)
			WHERE $search_clause
			GROUP BY users.ID
			ORDER BY `{$orderby}` {$order}
			LIMIT %d, %d",
			( $current_page - 1 ) * $per_page, $per_page
		) );
	
	
		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		*/
		$this->items = $customers;
	
	
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
