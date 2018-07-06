<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WC_Autoship_Admin_SchedulesListTable extends WP_List_Table {
	public function __construct() {
		global $status, $page;
	
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'schedule',
			'plural'    => 'schedules',
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

	function column_payment_token_id( $item ) {
		if ( empty( $item->payment_token_id ) ) {
			return $item->payment_token_id;
		}
		$token = WC_Payment_Tokens::get( $item->payment_token_id );
		if ( empty( $token ) ) {
			return $item->payment_token_id;
		}
		$gateway_settings_url = admin_url( '/admin.php?page=wc-settings&tab=checkout&section=' . $token->get_gateway_id() );
		return sprintf( '%s<br /><a href="%s">%s</a>', $token->get_display_name(), esc_attr( $gateway_settings_url ), esc_html( $token->get_gateway_id() ) );
	}
		
	function get_columns(){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'user_login' => __( 'Customer', 'wc-autoship' ),
			'autoship_frequency' => __( 'Frequency', 'wc-autoship' ),
			'autoship_status' => __( 'Status', 'wc-autoship' ),
			'email'  => __( 'Email', 'wc-autoship' ),
			'last_name' => __( 'Last Name', 'wc-autoship' ),
			'first_name' => __( 'First Name', 'wc-autoship' ),
			'last_order_date' => __( 'Last Order', 'wc-autoship' ),
			'next_order_date' => __( 'Next Order', 'wc-autoship' ),
			'shipping_method_id' => __( 'Shipping', 'wc-autoship' ),
			'payment_token_id' => __( 'Payment', 'wc-autoship' ),
			'coupon' => __( 'Coupon', 'wc-autoship' ),
			'item_quantity' => __( 'Items', 'wc-autoship' )
		);
		return $columns;
	}
	
	function get_sortable_columns() {
		$sortable_columns = array(
			'user_login' => array( 'user_login', false ),
			'autoship_frequency' => array( 'autoship_frequency', false ),
			'autoship_status' => array( 'autoship_status', false ),
			'email' => array( 'email', false ),
			'last_name' => array( 'last_name', false ),
			'first_name' => array( 'first_name', false ),
			'last_order_date' => array( 'last_order_date', false ),
			'next_order_date' => array( 'next_order_date', false ),
			'shipping_method_id' => array( 'shipping_method_id', false ),
			'payment_token_id' => array( 'payment_token_id', false ),
			'coupon' => array( 'coupon', false ),
			'item_quantity' => array( 'item_quantity', false )
		);
		return $sortable_columns;
	}
	
	function get_bulk_actions() {
		$actions = array(
			'pause' => __( 'Pause Schedules', 'wc-autoship' ),
			'activate' => __( 'Activate Schedules', 'wc-autoship' ),
			'delete' => __( 'Delete Schedules', 'wc-autoship' ),
			'update_next_order_date' => __( 'Update Next Order Date', 'wc-autoship' ),
			'update_shipping_method_id' => __( 'Update Shipping Method', 'wc-autoship' ),
			'update_coupon' => __( 'Update Coupon', 'wc-autoship' ),
			'change_autoship_frequency' => __( 'Change Frequency', 'wc-autoship' ),
			'create_order' => __( 'Create Autoship Order', 'wc-autoship' ),
			'send_updated_email' => __( 'Send "Schedule Updated" email', 'wc-autoship' ),
			'delete_cache' => __( 'Delete Cache', 'wc-autoship' )
		);
		return $actions;
	}
	
	function process_bulk_action() {
		global $wpdb;
		if ( isset( $_REQUEST['schedule'] ) ) {
			$current_user_id = get_current_user_id();
			if ( 'delete' === $this->current_action() ) {
				foreach ( $_REQUEST['schedule'] as $id ) {
					$customer_id = $wpdb->get_var( $wpdb->prepare( "SELECT customer_id FROM {$wpdb->prefix}wc_autoship_schedules WHERE id = %d LIMIT 0,1", $id ) );
					$wpdb->delete( "{$wpdb->prefix}wc_autoship_schedule_items", array( 'schedule_id' => $id ) );
					$wpdb->delete( "{$wpdb->prefix}wc_autoship_schedules", array( 'id' => $id ) );
					$log_description = __( "User $current_user_id deleted Schedule $id for Customer $customer_id", 'wc-autoship' );
					wc_autoship_log_action( $current_user_id, 'schedule_delete', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $id, null, null );
					wc_autoship_cache_delete( 'schedules_cart', 0, NULL, $id );
				}
				wc_autoship_add_message( __( 'Autoship schedules deleted', 'wc-autoship' ) );
			} elseif ( 'pause' === $this->current_action() ) {
				foreach ( $_REQUEST['schedule'] as $id ) {
					$customer_id = $wpdb->get_var( $wpdb->prepare( "SELECT customer_id FROM {$wpdb->prefix}wc_autoship_schedules WHERE id = %d LIMIT 0,1", $id ) );
					$wpdb->update( "{$wpdb->prefix}wc_autoship_schedules", array( 'autoship_status' => WC_AUTOSHIP_STATUS_PAUSED ), array( 'id' => $id ) );
					$log_description = __( "User $current_user_id paused Schedule $id for Customer $customer_id", 'wc-autoship' );
					wc_autoship_log_action( $current_user_id, 'schedule_set_autoship_status', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $id, NULL, WC_AUTOSHIP_STATUS_PAUSED );
				}
				wc_autoship_add_message( __( 'Autoship schedules paused', 'wc-autoship' ) );
			} elseif ( 'activate' === $this->current_action() ) {
				foreach ( $_REQUEST['schedule'] as $id ) {
					$customer_id = $wpdb->get_var( $wpdb->prepare( "SELECT customer_id FROM {$wpdb->prefix}wc_autoship_schedules WHERE id = %d LIMIT 0,1", $id ) );
					$wpdb->update( "{$wpdb->prefix}wc_autoship_schedules", array( 'autoship_status' => WC_AUTOSHIP_STATUS_ACTIVE ), array( 'id' => $id ) );
					$log_description = __( "User $current_user_id activated Schedule $id for Customer $customer_id", 'wc-autoship' );
					wc_autoship_log_action( $current_user_id, 'schedule_set_autoship_status', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $id, NULL, WC_AUTOSHIP_STATUS_ACTIVE );
				}
				wc_autoship_add_message( __( 'Autoship schedules activated', 'wc-autoship' ) );
			} elseif ( 'update_next_order_date' === $this->current_action() && ! empty( $_REQUEST['next_order_date'] ) ) {
				foreach ( $_REQUEST['schedule'] as $id ) {
					$customer_id = $wpdb->get_var( $wpdb->prepare( "SELECT customer_id FROM {$wpdb->prefix}wc_autoship_schedules WHERE id = %d LIMIT 0,1", $id ) );
					$wpdb->update( "{$wpdb->prefix}wc_autoship_schedules", array( 'next_order_date' => $_REQUEST['next_order_date'] ), array( 'id' => $id ) );
					$log_description = __( "User $current_user_id set next order date to {$_REQUEST['next_order_date']} for Schedule $id for Customer $customer_id", 'wc-autoship' );
					wc_autoship_log_action( $current_user_id, 'schedule_set_next_order_date', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $id, NULL, $_REQUEST['next_order_date'] );
				}
				wc_autoship_add_message( __( 'Autoship schedules updated', 'wc-autoship' ) );
			} elseif ( 'update_shipping_method_id' === $this->current_action() && ! empty( $_REQUEST['shipping_method_id'] ) ) {
				foreach ( $_REQUEST['schedule'] as $id ) {
					$customer_id = $wpdb->get_var( $wpdb->prepare( "SELECT customer_id FROM {$wpdb->prefix}wc_autoship_schedules WHERE id = %d LIMIT 0,1", $id ) );
					$wpdb->update( "{$wpdb->prefix}wc_autoship_schedules", array( 'shipping_method_id' => $_REQUEST['shipping_method_id'] ), array( 'id' => $id ) );
					$log_description = __( "User $current_user_id set shipping method id to {$_REQUEST['shipping_method_id']} for Schedule $id for Customer $customer_id", 'wc-autoship' );
					wc_autoship_log_action( $current_user_id, 'schedule_set_shipping_method_id', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $id, NULL, $_REQUEST['shipping_method_id'] );
					wc_autoship_cache_delete( 'schedules_cart', 0, NULL, $id );
				}
				wc_autoship_add_message( __( 'Autoship schedules updated', 'wc-autoship' ) );
			} elseif ( 'update_coupon' === $this->current_action() ) {
				foreach ( $_REQUEST['schedule'] as $id ) {
					$customer_id = $wpdb->get_var( $wpdb->prepare( "SELECT customer_id FROM {$wpdb->prefix}wc_autoship_schedules WHERE id = %d LIMIT 0,1", $id ) );
					$wpdb->update( "{$wpdb->prefix}wc_autoship_schedules", array( 'coupon' => $_REQUEST['coupon'] ), array( 'id' => $id ) );
					$log_description = __( "User $current_user_id set coupon to {$_REQUEST['coupon']} for Schedule $id for Customer $customer_id", 'wc-autoship' );
					wc_autoship_log_action( $current_user_id, 'schedule_set_coupon', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $id, NULL, $_REQUEST['coupon'] );
					wc_autoship_cache_delete( 'schedules_cart', 0, NULL, $id );
				}
				wc_autoship_add_message( __( 'Autoship schedules updated', 'wc-autoship' ) );
			} elseif ( 'change_autoship_frequency' == $this->current_action() && ! empty( $_REQUEST['autoship_frequency'] ) ) {
				$autoship_frequency = (int) $_REQUEST['autoship_frequency'];
				if ( $autoship_frequency > 6 && $autoship_frequency < 366 ) {
					$wpdb->show_errors( false );
					foreach ( $_REQUEST['schedule'] as $id ) {
						$customer_id = $wpdb->get_var( $wpdb->prepare( "SELECT customer_id FROM {$wpdb->prefix}wc_autoship_schedules WHERE id = %d LIMIT 0,1", $id ) );
						$result = $wpdb->update( "{$wpdb->prefix}wc_autoship_schedules", array( 'autoship_frequency' => $autoship_frequency ), array( 'id' => $id ) );
						if ( false === $result ) {
							if ( false !== stripos( $wpdb->last_error, 'Duplicate entry' ) ) {
								wc_autoship_add_message( __( 'All autoship frequencies must be unique for a customer', 'wc-autoship' ), 'error' );
							} else {
								wc_autoship_add_message( esc_html( $wpdb->last_error ), 'error' );
							}
						}
						$log_description = __( "User $current_user_id changed Autoship Frequency to {$autoship_frequency} for Schedule $id for customer $customer_id", 'wc-autoship' );
						wc_autoship_log_action( $current_user_id, 'schedule_set_autoship_frequency', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $id, NULL, $autoship_frequency );
					}
					$wpdb->show_errors( true );
					wc_autoship_add_message( __( 'Autoship schedules updated', 'wc-autoship' ) );
				} else {
					wc_autoship_add_message( __( 'Invalid autoship frequency selected', 'wc-autoship' ), 'error' );
				}
			} elseif ( 'create_order' == $this->current_action() ) {
				$orders = array();
				foreach ( $_REQUEST['schedule'] as $id ) {
					try {
						$order_id = wc_autoship_create_autoship_order( $id );
						$orders[] = sprintf( '<a href="%s?action=edit&post=%d">#%d</a>', esc_attr( admin_url( '/post.php' ) ), $order_id, $order_id );
					} catch ( Exception $e ) {
						$orders[] = '<span style="color: red">Error for Schedule ' . $id . ': ' . $e->getMessage() . '</span>';
					}
				}
				wc_autoship_add_message( __( 'Autoship orders created: ', 'wc-autoship' ) . implode( ', ', $orders ) );
			} elseif ( 'send_updated_email' == $this->current_action() ) {
				foreach ( $_REQUEST['schedule'] as $id ) {
					wc_autoship_send_schedule_updated_email( $id );
				}
				wc_autoship_add_message( __( 'Emails sent.', 'wc-autoship' ) );
			} elseif ( 'delete_cache' == $this->current_action() ) {
				foreach ( $_REQUEST['schedule'] as $id ) {
					wc_autoship_cache_delete( 'schedules_cart', 0, NULL, $id );
				}
				wc_autoship_add_message( __( 'Schedules cache deleted', 'wc-autoship' ) );
			}
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
			$conditions = array(
				sprintf( 'schedules.ID = \'%s\'', $safe_search ),
				sprintf( 'schedules.customer_id = \'%s\'', $safe_search )
			);
			$columns = array(
				'users.user_login',
				'users.user_email',
				'meta_last_name.meta_value',
				'meta_first_name.meta_value',
				'schedules.coupon'
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
				FROM {$wpdb->prefix}wc_autoship_schedules AS schedules
				LEFT JOIN {$wpdb->prefix}users AS users ON(schedules.customer_id = users.ID)
				LEFT JOIN {$wpdb->prefix}usermeta AS meta_last_name ON(users.id = meta_last_name.user_id AND meta_last_name.meta_key = 'billing_last_name')
				LEFT JOIN {$wpdb->prefix}usermeta AS meta_first_name ON(users.id = meta_first_name.user_id AND meta_first_name.meta_key = 'billing_first_name')
				WHERE $search_clause"
			);
		} else {
			/*
			 * Total items
			 */
			$total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wc_autoship_schedules" );
		}
		
		$wpdb->query( "SET SQL_BIG_SELECTS=1" );
		$schedules = $wpdb->get_results( $wpdb->prepare(
			"SELECT schedules.id AS `id`,
			schedules.customer_id AS `customer_id`,
			schedules.autoship_frequency AS `autoship_frequency`,
			users.user_login AS `user_login`,
			users.user_email AS `email`,
			meta_last_name.meta_value AS `last_name`,
			meta_first_name.meta_value AS `first_name`,
			schedules.autoship_frequency AS `autoship_frequency`,
			schedules.last_order_date AS `last_order_date`,
			schedules.next_order_date AS `next_order_date`,
			schedules.shipping_method_id AS `shipping_method_id`,
			schedules.payment_token_id AS `payment_token_id`,
			schedules.coupon AS `coupon`,
			SUM(items.qty) AS `item_quantity`,
			schedules.autoship_status AS `autoship_status`
			FROM {$wpdb->prefix}wc_autoship_schedules AS schedules
			LEFT JOIN {$wpdb->prefix}users AS users ON(schedules.customer_id = users.ID)
			LEFT JOIN {$wpdb->prefix}usermeta AS meta_last_name ON(users.id = meta_last_name.user_id AND meta_last_name.meta_key = 'billing_last_name')
			LEFT JOIN {$wpdb->prefix}usermeta AS meta_first_name ON(users.id = meta_first_name.user_id AND meta_first_name.meta_key = 'billing_first_name')
			LEFT JOIN {$wpdb->prefix}wc_autoship_schedule_items AS items ON(items.schedule_id = schedules.id)
			WHERE $search_clause
			GROUP BY schedules.id
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
