<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WC_Autoship_Admin_LogListTable extends WP_List_Table {
	public function __construct() {
		global $page;
	
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'log',
			'plural'    => 'logs',
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

	function column_action_type( $item ) {
		$log_item_title = $item->action_type;
		switch ( $item->action_type ) {
			case 'schedule_delete': {
				$log_item_title = 'Delete Schedule';
				break;
			}
			case 'schedule_set_next_order_date': {
				$log_item_title = 'Set Next Order Date';
				break;
			}
			case 'schedule_set_autoship_status': {
				$log_item_title = 'Set Status';
				break;
			}
			case 'schedule_item_set_quantity': {
				$log_item_title = 'Set Item Quantity';
				break;
			}
			case 'schedule_item_add': {
				$log_item_title = 'Add Item';
				break;
			}
			case 'schedule_item_delete': {
				$log_item_title = 'Delete Item';
				break;
			}
			case 'schedule_set_payment_token_id': {
				$log_item_title = 'Set Payment Method';
				break;
			}
			case 'schedule_set_shipping_method_id': {
				$log_item_title = 'Set Shipping Method';
				break;
			}
			case 'schedule_set_coupon': {
				$log_item_title = 'Set Coupon';
				break;
			}
			case 'autoship_checkout': {
				$log_item_title = 'Autoship Checkout';
				break;
			}
			case 'autoship_order_failed': {
				$log_item_title = 'Order Failed';
				break;
			}
			case 'set_license_key': {
				$log_item_title = 'Set License Key';
				break;
			}
			case 'license_key_valid': {
				$log_item_title = 'License Key Activated';
				break;
			}
			case 'license_key_invalid': {
				$log_item_title = 'License Key Invalid';
				break;
			}
		}

		$log_item_class = preg_replace( '/[^A-Za-z0-9]/', '-', $item->action_type );
		if ( ! is_null( $item->action_value ) ) {
			$log_item_class .= ' ' . $log_item_class . '-' . preg_replace( '/[^A-Za-z0-9]/', '-', $item->action_value );
		}

		$title = sprintf( '<div class="wc-autoship-log-item %s">%s</div>', $log_item_class, esc_html( $log_item_title ) );
		return $title;
	}

	function column_action_value ( $item ) {
		switch ( $item->action_type ) {
			case 'schedule_set_autoship_status': {
				return ( $item->action_value == WC_AUTOSHIP_STATUS_ACTIVE ) ? 'Active' : 'Paused';
			}
		}
		// Return default
		return $item->action_value;
	}

	function column_user_id( $item ) {
		if ( empty( $item->user_id ) ) {
			return '';
		}

		//Build row actions
		$actions = array(
			'user' => sprintf( '<a href="%s?user_id=%s&amp;wp_http_referer=%s">%s</a>', admin_url( '/user-edit.php' ), $item->user_id, $_SERVER['REQUEST_URI'], __( 'User', 'wc-autoship' ) )
		);

		//Return the title contents
		return sprintf( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
			$item->user_user_login,
			$item->user_id,
			$this->row_actions( $actions )
		);
	}

	function column_action_customer_id( $item ) {
		if ( empty( $item->action_customer_id ) ) {
			return '';
		}

		//Build row actions
		$actions = array(
			'user' => sprintf( '<a href="%s?user_id=%s&amp;wp_http_referer=%s">%s</a>', admin_url( '/user-edit.php' ), $item->action_customer_id, $_SERVER['REQUEST_URI'], __( 'User', 'wc-autoship' ) ),
			'autoship' => sprintf( '<a href="%s">%s</a>', add_query_arg( array( 'customer_id' => $item->action_customer_id ), wc_get_account_endpoint_url( 'autoship-schedules' ) ), __( 'Autoship', 'wc-autoship' ) )
		);

		//Return the title contents
		return sprintf( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
			$item->action_customer_user_login,
			$item->action_customer_id,
			$this->row_actions( $actions )
		);
	}
	
	function column_action_time( $item ) {
		if ( empty( $item->action_time ) || $item->action_time == '0000-00-00 00:00:00' ) {
			return '';
		}
		return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $item->action_time ) );
	}

	function column_action_description( $item ) {
		return sprintf( '<small><em>%s</em></small><br />%s', esc_html( $item->action_url), esc_html( $item->action_description ) );
	}
		
	function get_columns(){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'action_time' => __( 'Action Time', 'wc-autoship' ),
			'action_type' => __( 'Action Type', 'wc-autoship' ),
			'action_description' => __( 'Description', 'wc-autoship' ),
			'user_id' => __( 'User', 'wc-autoship' ),
			'action_customer_id'  => __( 'Customer', 'wc-autoship' ),
			'action_schedule_id' => __( 'Schedule ID', 'wc-autoship' ),
			'action_schedule_item_id' => __( 'Item ID', 'wc-autoship' ),
			'action_value' => __( 'Value', 'wc-autoship' )
		);
		return $columns;
	}
	
	function get_sortable_columns() {
		$sortable_columns = array(
			'action_time' => array( 'action_time', false ),
			'action_type' => array( 'action_type', false ),
			'user_id' => array( 'user_id', false ),
			'action_customer_id' => array( 'action_customer_id', false ),
			'action_schedule_id' => array( 'action_schedule_id', false ),
			'action_schedule_item_id' => array( 'action_schedule_item_id', false ),
			'action_value' => array( 'action_value', false )
		);
		return $sortable_columns;
	}
	
	function prepare_items() {
		global $wpdb;
	
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
		

		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? preg_replace( '/[^a-z_]/', '', $_REQUEST['orderby'] ) : 'action_time';
		$order = ( ! empty( $_REQUEST['order'] ) ) ? preg_replace( '/[^a-z_]/', '', $_REQUEST['order'] ) : 'DESC';
		
		$search_clause = '1';
		if ( ! empty( $_REQUEST['s'] ) ) {
			$safe_search = addslashes( stripslashes( $_REQUEST['s'] ) );
			$conditions = array(
				sprintf( 'log.id = \'%s\'', $safe_search ),
				sprintf( 'log.user_id = \'%s\'', $safe_search ),
				sprintf( 'log.action_customer_id = \'%s\'', $safe_search )
			);
			$columns = array(
				'user_users.user_login',
				'user_users.user_email',
				'customer_users.user_login',
				'customer_users.user_email'
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
				FROM {$wpdb->prefix}wc_autoship_log AS log
				LEFT JOIN {$wpdb->prefix}users AS user_users ON(log.user_id = user_users.id)
				LEFT JOIN {$wpdb->prefix}users AS customer_users ON(log.action_customer_id = customer_users.id)
				WHERE $search_clause"
			);
		} else {
			/*
			 * Total items
			 */
			$total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wc_autoship_log" );
		}
		
		$wpdb->query( "SET SQL_BIG_SELECTS=1" );
		$log = $wpdb->get_results( $wpdb->prepare(
			"SELECT log.id AS `id`,
			log.user_id AS `user_id`,
			log.action_time AS `action_time`,
			log.action_type AS `action_type`,
			log.action_customer_id AS `action_customer_id`,
			log.action_schedule_id AS `action_schedule_id`,
			log.action_schedule_item_id AS `action_schedule_item_id`,
			log.action_value AS `action_value`,
			log.action_description AS `action_description`,
			log.action_url AS `action_url`,
			user_users.user_login AS `user_user_login`,
			customer_users.user_login AS `action_customer_user_login`
			FROM {$wpdb->prefix}wc_autoship_log AS log
			LEFT JOIN {$wpdb->prefix}users AS user_users ON(log.user_id = user_users.id)
			LEFT JOIN {$wpdb->prefix}users AS customer_users ON(log.action_customer_id = customer_users.id)
			WHERE $search_clause
			GROUP BY log.id
			ORDER BY `{$orderby}` {$order}
			LIMIT %d, %d",
			( $current_page - 1 ) * $per_page, $per_page
		) );
	
	
		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		*/
		$this->items = $log;
	
	
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
