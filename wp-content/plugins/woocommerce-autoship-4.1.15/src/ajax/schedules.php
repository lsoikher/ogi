<?php

function wc_autoship_ajax_schedules_get_schedules() {
	$current_user_id = get_current_user_id();
	if ( empty( $current_user_id ) ) {
		wc_autoship_ajax_result( 403 );
	}

	$customer_id = $_REQUEST['customer_id'];
	if ( empty( $customer_id ) ) {
		wc_autoship_ajax_result( 400 );
	}
	if ( ! wc_autoship_user_can_edit_customer( $current_user_id, $customer_id ) ) {
		wc_autoship_ajax_result( 403 );
	}
	
	require_once( WC_AUTOSHIP_SRC_DIR . '/Models/Schedule.php' );
	$schedules = WC_Autoship_Models_Schedule::get_schedules_for_customer( $customer_id );
	wc_autoship_ajax_result( 200, $schedules );
}
add_action( 'wp_ajax_wc_autoship_schedules_get_schedules', 'wc_autoship_ajax_schedules_get_schedules' );

function wc_autoship_ajax_schedules_get_payment_methods() {
	$current_user_id = get_current_user_id();
	if ( empty( $current_user_id ) ) {
		wc_autoship_ajax_result( 403 );
	}

	$customer_id = $_REQUEST['customer_id'];
	if ( empty( $customer_id ) ) {
		wc_autoship_ajax_result( 400 );
	}
	if ( ! wc_autoship_user_can_edit_customer( $current_user_id, $customer_id ) ) {
		wc_autoship_ajax_result( 403 );
	}

	$tokens = WC_Payment_Tokens::get_customer_tokens( $customer_id );
	//$t = new WC_Payment_Token_CC();
	$payment_methods = array();
	foreach ( $tokens as $token ) {
		$payment_methods[] = array(
			'id' => $token->get_id(),
			'display_name' => html_entity_decode( strip_tags( $token->get_display_name() ) )
		);
	}
	wc_autoship_ajax_result( 200, $payment_methods );
}
add_action( 'wp_ajax_wc_autoship_schedules_get_payment_methods', 'wc_autoship_ajax_schedules_get_payment_methods' );

function wc_autoship_ajax_schedules_get_cart() {
	$current_user_id = get_current_user_id();
	if ( empty( $current_user_id ) ) {
		wc_autoship_ajax_result( 403 );
	}

	$schedule_id = $_REQUEST['schedule_id'];
	if ( ! wc_autoship_user_can_edit_schedule( $current_user_id, $schedule_id ) ) {
		wc_autoship_ajax_result( 403 );
	}
	require_once( WC_AUTOSHIP_SRC_DIR . '/Models/Schedule.php' );
	if ( ! WC_Autoship_Models_Schedule::id_exists( $schedule_id ) ) {
		wc_autoship_ajax_result( 404 );
	}
	$schedule = new WC_Autoship_Models_Schedule( $schedule_id );

	// Try to get cached cart
	$cache_cart_enabled = apply_filters( 'wc_autoship_cache_cart_enabled', true );
	if ( $cache_cart_enabled ) {
		$cached_cart = wc_autoship_schedules_get_cached_cart( $schedule_id );
		if ( !empty( $cached_cart ) ) {
			wc_autoship_ajax_result( 200, $cached_cart );
		}
	}

	try {
		// Create pipey client
		require_once( WC_AUTOSHIP_SRC_DIR . '/Pipey/Client.php' );
		$pipey = new WC_Autoship_Pipey_Client();
		// Login
		$customer_id = $schedule->get_customer_id();
		$pipey->login( $customer_id );
		// Add items
		$items = $schedule->get_items();
		foreach ( $items as $item ) {
			$pipey->add_item( $item->get_product_id(), $item->get_variation_id(), $item->get_quantity(), $schedule->get_autoship_frequency() );
		}
		// Apply coupon
		$coupon_code = $schedule->get_coupon_code();
		if ( ! empty( $coupon_code ) ) {
			$pipey->apply_coupon( $coupon_code );
		}
		// Update shipping method
		$pipey->update_shipping_method( $schedule->get_shipping_method_id() );
		// Get cart
		$cart = $pipey->get_cart();
		// Empty cart
		$pipey->empty_cart();
		// Cache cart
		wc_autoship_schedules_cache_cart( $schedule_id, $cart );
		// Return cart
		wc_autoship_ajax_result( 200, $cart );
	} catch ( WC_Autoship_Pipey_Exception $e ) {
		wc_autoship_ajax_result( $e->getCode(), $e->getMessage() );
	} catch ( Exception $e ) {
		wc_autoship_ajax_result( 500, $e->getMessage() );
	}
}
add_action( 'wp_ajax_wc_autoship_schedules_get_cart', 'wc_autoship_ajax_schedules_get_cart' );

function wc_autoship_schedules_delete_cached_cart( $schedule_id ) {
	wc_autoship_cache_delete( 'schedules_cart', 0, NULL, $schedule_id );
}

function wc_autoship_schedules_get_cached_cart( $schedule_id ) {
	$cache_age = 259200; // 3 days
	wc_autoship_cache_delete( 'schedules_cart', $cache_age, NULL, $schedule_id );
	$cart = wc_autoship_cache_find( 'schedules_cart', $cache_age, NULL, $schedule_id );
	return $cart;
}

function wc_autoship_schedules_cache_cart( $schedule_id, $cart ) {
	wc_autoship_cache_add( 'schedules_cart', $cart, NULL, $schedule_id );
}

function wc_autoship_ajax_schedules_get_available_frequencies() {
	$current_user_id = get_current_user_id();
	if ( empty( $current_user_id ) ) {
		wc_autoship_ajax_result( 403 );
	}

	$schedule_id = $_REQUEST['schedule_id'];
	if ( ! wc_autoship_user_can_edit_schedule( $current_user_id, $schedule_id ) ) {
		wc_autoship_ajax_result( 403 );
	}
	require_once( WC_AUTOSHIP_SRC_DIR . '/Models/Schedule.php' );
	if ( ! WC_Autoship_Models_Schedule::id_exists( $schedule_id ) ) {
		wc_autoship_ajax_result( 404 );
	}
	$schedule = new WC_Autoship_Models_Schedule( $schedule_id );

	$items = $schedule->get_items();

	$frequencies = array();
	for ( $d = WC_AUTOSHIP_MIN_FREQUENCY; $d <= WC_AUTOSHIP_MAX_FREQUENCY; $d++ ) {
		foreach ( $items as $item ) {
			if ( $d < $item->get_min_frequency() || $d > $item->get_max_frequency() ) {
				continue 2;
			}
		}
		$frequency = array(
			'frequency' => $d,
			'title' => __( "Every $d days", 'wc-autoship' )
		);
		array_push( $frequencies, $frequency );
	}
	$frequencies = apply_filters( 'wc_autoship_schedule_available_frequencies', $frequencies, $schedule_id );
	wc_autoship_ajax_result( 200, $frequencies );
}
add_action( 'wp_ajax_wc_autoship_schedules_get_available_frequencies', 'wc_autoship_ajax_schedules_get_available_frequencies' );

function wc_autoship_ajax_schedules_get_available_products() {

	$current_user_id = get_current_user_id();
	if ( empty( $current_user_id ) ) {
		wc_autoship_ajax_result( 403 );
	}

	$schedule_id = $_REQUEST['schedule_id'];
	if ( ! wc_autoship_user_can_edit_schedule( $current_user_id, $schedule_id ) ) {
		wc_autoship_ajax_result( 403 );
	}
	require_once( WC_AUTOSHIP_SRC_DIR . '/Models/Schedule.php' );
	if ( ! WC_Autoship_Models_Schedule::id_exists( $schedule_id ) ) {
		wc_autoship_ajax_result( 404 );
	}
	$schedule = new WC_Autoship_Models_Schedule( $schedule_id );

	$products_result = $schedule->get_available_products();
	$products = array();
	foreach ( $products_result as $product_row ) {
		$product = wc_get_product( $product_row );
		if ( $product->is_type( 'variable' ) ) {
			$variations = $product->get_available_variations();
			foreach ( $variations as $variation_data ) {
				$variation = wc_get_product( $variation_data['variation_id'] );
				if ( ! empty ( $variation ) ) {
					$products[] = array(
						'product_id' => $product->get_id(),
						'variation_id' => property_exists( $product, 'variation_id' ) ? $variation->variation_id : $variation->get_id(),
						'price' => (float)$variation->get_price(),
						'price_formatted' => wc_autoship_format_currency( $variation->get_price() ),
						'sale_price' => (float) $variation->get_sale_price(),
						'sale_price_formatted' => wc_autoship_format_currency( $variation->get_sale_price() ),
						'title' => html_entity_decode( strip_tags( str_replace( '><', ' > <', wc_autoship_get_product_display_name( $variation ) . ' ' ) ) )
					);
				}
			}
		} else {
			$products[] = array(
				'product_id' => $product->get_id(),
				'variation_id' => 0,
				'price' => (float)$product->get_price(),
				'price_formatted' => wc_autoship_format_currency( $product->get_price() ),
				'sale_price' => (float) $product->get_sale_price(),
				'sale_price_formatted' => wc_autoship_format_currency( $product->get_sale_price() ),
				'title' => wc_autoship_get_product_display_name( $product )
			);
		}

	}
	wc_autoship_ajax_result( 200, $products );
}
add_action( 'wp_ajax_wc_autoship_schedules_get_available_products', 'wc_autoship_ajax_schedules_get_available_products' );

function wc_autoship_ajax_schedules_update_schedule() {
	$current_user_id = get_current_user_id();
	if ( empty( $current_user_id ) ) {
		wc_autoship_ajax_result( 403 );
	}

	$schedule_id = $_REQUEST['schedule_id'];
	if ( ! wc_autoship_user_can_edit_schedule( $current_user_id, $schedule_id ) ) {
		wc_autoship_ajax_result( 403 );
	}
	require_once( WC_AUTOSHIP_SRC_DIR . '/Models/Schedule.php' );
	if ( ! WC_Autoship_Models_Schedule::id_exists( $schedule_id ) ) {
		wc_autoship_ajax_result( 404 );
	}
	$schedule = new WC_Autoship_Models_Schedule( $schedule_id );

	$schedule_data = json_decode( file_get_contents( 'php://input' ), true );
	if ( ! empty( $schedule_data ) ) {
		$customer_id = $schedule->get_customer_id();
		if ( isset( $schedule_data['next_order_date'] ) ) {
			$next_order_date = $schedule_data['next_order_date'];
			$min_time = strtotime( '+1 day', (int) current_time( 'timestamp' ) );
			$next_order_time = strtotime( $next_order_date, (int) current_time( 'timestamp' ) );
			if ( $next_order_time < $min_time ) {
				$next_order_date = date( 'Y-m-d', $min_time );
			} else {
				$next_order_date = date( 'Y-m-d', $next_order_time );
			}
			$schedule->set_next_order_date( $next_order_date );
			$log_description = __( "User $current_user_id set next order date to $next_order_date for Schedule $schedule_id for Customer $customer_id", 'wc-autoship' );
			wc_autoship_log_action( $current_user_id, 'schedule_set_next_order_date', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $schedule_id, NULL, $next_order_date );
		}
		if ( isset( $schedule_data['autoship_status'] ) ) {
			$status = (int) $schedule_data['autoship_status'];
			switch ( $status ) {
				case WC_AUTOSHIP_STATUS_ACTIVE: {
					$schedule->set_autoship_status( WC_AUTOSHIP_STATUS_ACTIVE );
					$log_description = __( "User $current_user_id activated Schedule $schedule_id for Customer $customer_id", 'wc-autoship' );
					wc_autoship_log_action( $current_user_id, 'schedule_set_autoship_status', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $schedule_id, NULL, $status );
					$next_order_date = $schedule->get_next_order_date();
					$next_order_time = strtotime( $next_order_date );
					$min_time = strtotime( '+1 day' );
					if ( $next_order_time < $min_time ) {
						$schedule->set_next_order_date( date( 'Y-m-d', $min_time ) );
					}
					break;
				}
				case WC_AUTOSHIP_STATUS_PAUSED: {
					$schedule->set_autoship_status( WC_AUTOSHIP_STATUS_PAUSED );
					$log_description = __( "User $current_user_id paused Schedule $schedule_id for Customer $customer_id", 'wc-autoship' );
					wc_autoship_log_action( $current_user_id, 'schedule_set_autoship_status', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $schedule_id, NULL, $status );
					break;
				}
				default: {
					wc_autoship_ajax_result( 400, "Invalid autoship status" );
				}
			}
		}
		if ( isset( $schedule_data['payment_token_id'] ) ) {
			$schedule->set_payment_token_id( $schedule_data['payment_token_id'] );
			$log_description = __( "User $current_user_id set payment token id to {$schedule_data['payment_token_id']} for Schedule $schedule_id for Customer $customer_id", 'wc-autoship' );
			wc_autoship_log_action( $current_user_id, 'schedule_set_payment_token_id', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $schedule_id, NULL, $schedule_data['payment_token_id'] );
		}
		if ( isset( $schedule_data['shipping_method_id'] ) ) {
			$schedule->set_shipping_method_id( $schedule_data['shipping_method_id'] );
			$log_description = __( "User $current_user_id set shipping method id to {$schedule_data['shipping_method_id']} for Schedule $schedule_id for Customer $customer_id", 'wc-autoship' );
			wc_autoship_log_action( $current_user_id, 'schedule_set_shipping_method_id', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $schedule_id, NULL, $schedule_data['shipping_method_id'] );
		}
		if ( isset( $schedule_data['coupon'] ) ) {
			$schedule->set_coupon_code( $schedule_data['coupon'] );
			$log_description = __( "User $current_user_id set coupon to {$schedule_data['coupon']} for Schedule $schedule_id for Customer $customer_id", 'wc-autoship' );
			wc_autoship_log_action( $current_user_id, 'schedule_set_coupon', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $schedule_id, NULL, $schedule_data['coupon'] );
		}
		if ( isset( $schedule_data['autoship_frequency'] ) ) {
			$schedule->set_autoship_frequency( $schedule_data['autoship_frequency'] );
			$log_description = __( "User $current_user_id set autoship_frequency to {$schedule_data['autoship_frequency']} for Schedule $schedule_id for Customer $customer_id", 'wc-autoship' );
			wc_autoship_log_action( $current_user_id, 'schedule_set_frequency', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $schedule_id, NULL, $schedule_data['autoship_frequency'] );
		}
		if ( $schedule->save() ) {
			wp_schedule_single_event( time() + 1800, 'wc_autoship_schedule_updated_event', array ( $schedule_id ) ); // 1800 secs is 30 minutes
			wc_autoship_schedules_delete_cached_cart( $schedule_id );
			wc_autoship_ajax_result( 200, $schedule );
		}

	}

	wc_autoship_ajax_result( 400 );
}
add_action( 'wp_ajax_wc_autoship_schedules_update_schedule', 'wc_autoship_ajax_schedules_update_schedule' );

function wc_autoship_ajax_schedules_delete_schedule() {
	$current_user_id = get_current_user_id();
	if ( empty( $current_user_id ) ) {
		wc_autoship_ajax_result( 403 );
	}

	$schedule_id = $_REQUEST['schedule_id'];
	if ( ! wc_autoship_user_can_edit_schedule( $current_user_id, $schedule_id ) ) {
		wc_autoship_ajax_result( 403 );
	}
	require_once( WC_AUTOSHIP_SRC_DIR . '/Models/Schedule.php' );
	if ( ! WC_Autoship_Models_Schedule::id_exists( $schedule_id ) ) {
		wc_autoship_ajax_result( 404 );
	}
	$schedule = new WC_Autoship_Models_Schedule( $schedule_id );

	$customer_id = $schedule->get_customer_id();
	if ( $schedule->delete() ) {
		wc_autoship_schedules_delete_cached_cart( $schedule_id );
		$log_description = __( "User $current_user_id deleted Schedule $schedule_id for customer $customer_id", 'wc-autoship' );
		wc_autoship_log_action( $current_user_id, 'schedule_delete', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $schedule_id, NULL, NULL );
		wc_autoship_ajax_result( 200 );
	}

	wc_autoship_ajax_result( 400 );
}
add_action( 'wp_ajax_wc_autoship_schedules_delete_schedule', 'wc_autoship_ajax_schedules_delete_schedule' );

function wc_autoship_ajax_schedules_add_schedule_item() {
	$current_user_id = get_current_user_id();
	if ( empty( $current_user_id ) ) {
		wc_autoship_ajax_result( 403 );
	}

	$product_data = json_decode( file_get_contents( 'php://input' ), true );
	if ( empty( $product_data ) ) {
		wc_autoship_ajax_result(400);
	}

	$schedule_id = $_REQUEST['schedule_id'];
	if ( ! wc_autoship_user_can_edit_schedule( $current_user_id, $schedule_id ) ) {
		wc_autoship_ajax_result( 403 );
	}
	require_once( WC_AUTOSHIP_SRC_DIR . '/Models/Schedule.php' );
	if ( ! WC_Autoship_Models_Schedule::id_exists( $schedule_id ) ) {
		wc_autoship_ajax_result( 404 );
	}
	$schedule = new WC_Autoship_Models_Schedule( $schedule_id );

	require_once ( WC_AUTOSHIP_SRC_DIR . '/Models/ScheduleItem.php' );
	$schedule_item = new WC_Autoship_Models_ScheduleItem();
	$schedule_item->set_schedule_id( $schedule_id );
	$schedule_item->set_product_id( $product_data['product_id'] );
	$schedule_item->set_variation_id( $product_data['variation_id'] );
	$schedule_item->set_quantity( 1 );
	if ( $schedule_item->save() ) {
		wc_autoship_schedules_delete_cached_cart( $schedule_id );
		$customer_id = $schedule->get_customer_id();
		$log_description = __( "User $current_user_id added item {$schedule_item->get_id()} to Schedule $schedule_id for Customer $customer_id", 'wc-autoship' );
		$action_value = $product_data['product_id'] . ':' . $product_data['variation_id'];
		wc_autoship_log_action( $current_user_id, 'schedule_item_add', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $schedule_id, $schedule_item->get_id(), $action_value );
		wp_schedule_single_event( time() + 1800, 'wc_autoship_schedule_updated_event', array ( $schedule_id ) ); // 1800 secs is 30 minutes
		wc_autoship_ajax_result( 200, $schedule_item );
	}
}
add_action( 'wp_ajax_wc_autoship_schedules_add_schedule_item', 'wc_autoship_ajax_schedules_add_schedule_item' );

function wc_autoship_ajax_schedules_delete_schedule_item() {
	$current_user_id = get_current_user_id();
	if ( empty( $current_user_id ) ) {
		wc_autoship_ajax_result( 403 );
	}

	$schedule_item_id = $_REQUEST['schedule_item_id'];
	require_once ( WC_AUTOSHIP_SRC_DIR . '/Models/ScheduleItem.php' );
	if ( ! WC_Autoship_Models_ScheduleItem::id_exists( $schedule_item_id ) ) {
		wc_autoship_ajax_result( 404 );
	}
	$schedule_item = new WC_Autoship_Models_ScheduleItem( $schedule_item_id );
	if ( ! wc_autoship_user_can_edit_schedule( $current_user_id, $schedule_item->get_schedule_id() ) ) {
		wc_autoship_ajax_result( 403 );
	}

	$schedule = $schedule_item->get_schedule();
	$schedule_id = $schedule_item->get_schedule_id();
	$customer_id = $schedule->get_customer_id();
	$action_value = $schedule_item->get_product_id() . ':' . $schedule_item->get_variation_id();
	if ( $schedule_item->delete() ) {
		wc_autoship_schedules_delete_cached_cart( $schedule_id );
		$log_description = __( "User $current_user_id deleted Item $schedule_item_id from Schedule $schedule_id for Customer $customer_id", 'wc-autoship' );
		wc_autoship_log_action( $current_user_id, 'schedule_item_delete', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $schedule_id, $schedule_item_id, $action_value );
		wp_schedule_single_event( time() + 1800, 'wc_autoship_schedule_updated_event', array ( $schedule_id ) ); // 1800 secs is 30 minutes
		wc_autoship_ajax_result( 200 );
	}
	wc_autoship_ajax_result( 400 );
}
add_action( 'wp_ajax_wc_autoship_schedules_delete_schedule_item', 'wc_autoship_ajax_schedules_delete_schedule_item' );

function wc_autoship_ajax_schedules_update_schedule_item() {
	$current_user_id = get_current_user_id();
	if ( empty( $current_user_id ) ) {
		wc_autoship_ajax_result( 403 );
	}

	$schedule_item_id = $_REQUEST['schedule_item_id'];
	require_once ( WC_AUTOSHIP_SRC_DIR . '/Models/ScheduleItem.php' );
	if ( ! WC_Autoship_Models_ScheduleItem::id_exists( $schedule_item_id ) ) {
		wc_autoship_ajax_result( 404 );
	}
	$schedule_item = new WC_Autoship_Models_ScheduleItem( $schedule_item_id );
	if ( ! wc_autoship_user_can_edit_schedule( $current_user_id, $schedule_item->get_schedule_id() ) ) {
		wc_autoship_ajax_result( 403 );
	}
	$schedule_item = new WC_Autoship_Models_ScheduleItem( $schedule_item_id );

	$item_data = json_decode( file_get_contents( 'php://input' ), true );
	if ( ! empty( $item_data ) ) {
		$schedule_id = $schedule_item->get_schedule_id();
		$customer_id = $schedule_item->get_schedule()->get_customer_id();
		if ( isset( $item_data['qty'] ) ) {
			$schedule_item->set_quantity( $item_data['qty'] );
			$log_description = __( "User $current_user_id set quantity to {$item_data['qty']} for Item $schedule_item_id for Schedule $schedule_id for Customer $customer_id", 'wc-autoship' );
			wc_autoship_log_action( $current_user_id, 'schedule_item_set_quantity', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $schedule_id, $schedule_item_id, $item_data['qty'] );
		}

		if ( $schedule_item->save() ) {
			wc_autoship_schedules_delete_cached_cart( $schedule_id );
			wp_schedule_single_event( time() + 1800, 'wc_autoship_schedule_updated_event', array ( $schedule_id ) ); // 1800 secs is 30 minutes
			wc_autoship_ajax_result( 200, $schedule_item );
		}
	}
	wc_autoship_ajax_result( 400 );
}
add_action( 'wp_ajax_wc_autoship_schedules_update_schedule_item', 'wc_autoship_ajax_schedules_update_schedule_item' );

function wc_autoship_user_can_edit_schedule( $user_id, $schedule_id ) {
	global $wpdb;

	if ( user_can( $user_id, 'manage_woocommerce' ) ) {
		// User is admin
		return true;
	}
	// Check schedule owner
	$customer_id = $wpdb->get_var( $wpdb->prepare(
		"SELECT customer_id 
			FROM {$wpdb->prefix}wc_autoship_schedules
			WHERE id = %d",
		$schedule_id
	) );
	if ( $user_id == $customer_id ) {
		// User is schedule owner
		return true;
	}
	return false;
}

function wc_autoship_user_can_view_schedule( $user_id, $schedule_id ) {
	return wc_autoship_user_can_view_schedule( $user_id, $schedule_id );
}

function wc_autoship_user_can_edit_customer( $user_id, $customer_id ) {
	if ( ! empty( $user_id ) && ! empty( $customer_id ) && $user_id == $customer_id ) {
		return true;
	}
	if ( user_can( $user_id, 'manage_woocommerce' ) ) {
		// User is admin
		return true;
	}
	return false;
}