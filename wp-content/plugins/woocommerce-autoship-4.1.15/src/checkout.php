<?php


function wc_autoship_checkout_add_order_item_meta($item_id, $values ) {
	if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {
		return;
	}

	if ( isset( $values['wc_autoship_frequency'] ) ) {
		$autoship_frequency = $values['wc_autoship_frequency'];
		wc_add_order_item_meta(
			$item_id,
			'_wc_autoship_frequency',
			$autoship_frequency,
			true
		);
		wc_add_order_item_meta(
			$item_id,
			__( 'Auto-Ship', 'wc-autoship' ),
			__( "Every $autoship_frequency days", 'wc-autoship' ),
			true
		);
	}
}
add_action( 'woocommerce_add_order_item_meta', 'wc_autoship_checkout_add_order_item_meta', 10, 2 );


function wc_autoship_add_order_line_item_meta( $item, $cart_item_key, $values, $order ) {
	if ( isset( $values['wc_autoship_frequency'] ) ) {
		$frequency = $values['wc_autoship_frequency'];
		$item->add_meta_data( '_wc_autoship_frequency', $frequency );
		$item->add_meta_data( __( 'Auto-Ship', 'wc-autoship' ), __( "Every {$frequency} days", 'wc-autoship' ) );
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'wc_autoship_add_order_line_item_meta', 10, 4 );


function wc_autoship_checkout_create_autoship_schedule( $order_id ) {
	global $wpdb;

	if ( wc_autoship_ajax_is_pipey_request() && wc_autoship_ajax_pipey_is_authorized() ) {
		// This is an autoship order, so do not create a new autoship schedule
		return;
	}

	// Identify whether this order is to init autoship
	$autoship_count = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*)
		FROM {$wpdb->prefix}woocommerce_order_itemmeta
		WHERE order_item_id IN (SELECT order_item_id
								FROM {$wpdb->prefix}woocommerce_order_items
								WHERE order_id = %d)
		AND meta_key = '_wc_autoship_frequency'",
		$order_id
	) );
	if ( $autoship_count > 0 ) {
		// This is an autoship order
		add_post_meta( $order_id, '_wc_autoship_init', '1', true );
	} else {
		// This is not an autoship order, so return
		return;
	}

	// Get WC
	$woocommerce = WC();
	// Get order
	$order = wc_get_order( $order_id );

	// Get customer id
	$customer_id = $order->customer_user;

	// Get payment token
	$autoship_payment_token = null;
	// Try to get payment tokens from the order
	$payment_tokens = $order->get_payment_tokens();
	if ( ! empty( $payment_tokens ) ) {
		// Dbl check what this array contains in case it's just token Id's instead of tokens
		$last_entry = end( $payment_tokens );
		$type = gettype( $last_entry );
		if ( $type == 'integer' ) {
			$autoship_payment_token = WC_Payment_Tokens::get( $last_entry );
		} else if ( $type == 'WC_Payment_Token_CC' ) {
			$autoship_payment_token = $last_entry;
		}
	} else {
		// No payment tokens were attached to the order, try to get payment tokens from POST data
		$payment_gateway_id = $_POST['payment_method'];
		$payment_token_post_name = 'wc-' . $payment_gateway_id . '-payment-token';
		if ( isset( $_POST[ $payment_token_post_name ] ) && is_numeric( $_POST[ $payment_token_post_name ] ) ) {
			// This order used an existing token
			$payment_token = WC_Payment_Tokens::get( $_POST[ $payment_token_post_name ] );
			if ( ! empty( $payment_token ) && $customer_id == $payment_token->get_user_id() ) {
				$autoship_payment_token = $payment_token;
			}
		} else {
			// This order created a new token
			$payment_tokens = WC_Payment_Tokens::get_customer_tokens( $customer_id, $payment_gateway_id );
			if ( ! empty( $payment_tokens ) ) {
				$autoship_payment_token = end( $payment_tokens );
			}
		}
	}
	
	// Order shipping methods
	$autoship_shipping_method = null;
	$free_shipping = get_option( 'wc_autoship_free_shipping' );
	if ( $free_shipping == 'yes' ) {
		// Only free shipping method
		$autoship_shipping_method = 'free_shipping';
	} else {
		$shipping_methods = $order->get_shipping_methods();
		$first_shipping_method = current( $shipping_methods );
		$autoship_shipping_method = ( ! empty ( $first_shipping_method ) ) ? $first_shipping_method : null;
	}

	// Get coupon
	$coupons = $order->get_used_coupons();
	$coupon = ( ! empty( $coupons ) ) ? $coupons[0] : '';

	// Create schedule items
	$schedule_items = array();
	require_once( 'Models/ScheduleItem.php' );
	foreach ( $order->get_items() as $order_item ) {
		// Autoship enabled
		$autoship_enabled = get_post_meta( $order_item['product_id'], '_wc_autoship_enable_autoship', true );
		if ( $autoship_enabled != 'yes' ) {
			// Auto-ship is not enabled for this product
			continue;
		}
		// Autoship frequency
		if ( empty( $order_item['wc_autoship_frequency'] ) ) {
			continue;
		}
		$autoship_frequency = intval( $order_item['wc_autoship_frequency'] );
		$autoship_min_frequency = intval( get_post_meta( $order_item['product_id'], '_wc_autoship_min_frequency', true ) );
		$autoship_max_frequency = intval( get_post_meta( $order_item['product_id'], '_wc_autoship_max_frequency', true ) );
		if ( $autoship_frequency < $autoship_min_frequency || $autoship_frequency > $autoship_max_frequency ) {
			// Auto-ship frequency is out of range
			continue;
		}

		// Create item
		$item = new WC_Autoship_Models_ScheduleItem();
		$item->set_product_id( $order_item['product_id'] );
		$item->set_variation_id( $order_item['variation_id'] );
		$item->set_quantity( $order_item['qty'] );
		$schedule_items[ $autoship_frequency ][] = $item;
	}

	// Create schedules
	if ( ! empty( $schedule_items ) ) {
		require_once( 'Models/Schedule.php' );
		foreach ( $schedule_items as $frequency => $items ) {
			// Create schedule
			$schedule = new WC_Autoship_Models_Schedule();
			$schedule->set_customer_id( $customer_id );
			$schedule->set_autoship_frequency( $frequency );
			$schedule->set_autoship_status( WC_AUTOSHIP_STATUS_ACTIVE );
			if ( ! empty( $autoship_payment_token ) ) {
				$schedule->set_payment_token_id( $autoship_payment_token->get_id() );
			}
			if ( ! empty( $autoship_shipping_method ) ) {
				$schedule->set_shipping_method_id( $autoship_shipping_method['method_id'] );
			}
			if ( ! empty( $coupon ) ) {
				$schedule->set_coupon_code( $coupon );
			}
			$schedule->set_last_order_date( date( 'Y-m-d' ) );
			$schedule->set_next_order_date( date( 'Y-m-d', strtotime( '+' . ( (int) $frequency ) . ' day' ) ) );
			// Set items
			foreach ( $items as $item ) {
				$schedule->add_item( $item );
			}
			// Save schedule
			//@todo add error handling
			$affected_rows = $schedule->save();
			// Log action
			if ( $affected_rows ) {
				$current_user_id = get_current_user_id();
				$schedule_id = $schedule->get_id();
				$log_description = "Customer {$customer_id} completed checkout with autoship frequency {$frequency}";
				wc_autoship_log_action( $current_user_id, 'autoship_checkout', $log_description, $_SERVER['REQUEST_URI'], $customer_id, $schedule_id, NULL, $frequency );
			}
			// Trigger hook
			do_action( 'wc_autoship_checkout_create_autoship_schedule', $schedule->get_id(), $order_id );
			/** Deprecated hook */
			do_action( 'wc_autoship_after_create_autoship_schedule', $schedule, $order );
		}
	}
}
add_action( 'woocommerce_payment_complete', 'wc_autoship_checkout_create_autoship_schedule', 11, 1 );

// auth only autoship fix
function wc_autoship_create_schedule_on_auth_only_checkout( $order_id ) {
	if (is_checkout()) {
		wc_autoship_checkout_create_autoship_schedule( $order_id );
	}
}
add_action( 'woocommerce_order_status_on-hold', 'wc_autoship_create_schedule_on_auth_only_checkout', 10, 2 );

function wc_autoship_force_save_card() {
	if ( wc_autoship_cart_has_autoship_items() ):
		?>
		<script>
			var checkbox = document.getElementById('wc-stripe-new-payment-method');
			if (checkbox !== null) {
				checkbox.checked = true;
				checkbox.addEventListener('click', function() {
					this.checked = true;
				});
			}
		</script>
	<?php endif;
}
add_action( 'woocommerce_review_order_after_submit', 'wc_autoship_force_save_card', 10, 0 );