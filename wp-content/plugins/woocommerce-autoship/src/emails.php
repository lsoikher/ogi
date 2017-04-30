<?php
function send_wc_autoship_email( $recipient, $subject, $message, $title ) {
	// concatenate templates
	//    Use our own header/footer ripoffs, which rely on manually echo'ing all the styles inline
	$header = wc_autoship_render_template('emails/wc-autoship-email-header', array('email_heading' => $title)); // Not inserting the title
	$footer = wc_autoship_render_template('emails/wc-autoship-email-footer');

	// Get WooCommerce email styles and make them inline
	$body = $header . $message . $footer;
	// From header
	$from_name = get_option( 'woocommerce_email_from_name' );
	if ( empty( $from_name ) ) {
		$from_name = get_bloginfo( 'name' );
	}
	$from_name = wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
	$from_address = get_option( 'woocommerce_email_from_address' );
	if ( empty( $from_address ) ) {
		$from_address = get_bloginfo( 'admin_email' );
	}
	$from_address = sanitize_email( $from_address );
	// Set headers
	$headers = array(
		"MIME-Version: 1.0",
		"Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"",
		"From: $from_name <$from_address>"
	);
	// send email
	$sent = wp_mail( $recipient, $subject, $body, $headers );
	return $sent;
}

function wc_autoship_send_schedule_updated_email( $schedule_id ) {
	if ( 'no' != get_option( 'wc_autoship_send_schedule_updated_email' ) ) {
		$subject = __( 'Your Autoship Schedule has been updated.', 'wc-autoship' );
		$title   = __( 'Your Autoship Schedule has been updated.', 'wc-autoship' );
		$message = __( 'Please review your Autoship Schedule below', 'wc-autoship' );
		$sent = wc_autoship_send_schedule_email( $schedule_id, $subject, $title, $message );
		return $sent;
	} else {
		return false;
	}

}
add_action( 'wc_autoship_schedule_updated_event', 'wc_autoship_send_schedule_updated_email' );


function send_wc_autoship_error_email( $customer_id, $message ) {
	if ( 'no' != get_option( 'wc_autoship_send_error_email' ) ) {
		$user = new WP_User( $customer_id );
		if ( ! $user->exists() ) {
			return false;
		}

		$recipient = $user->user_email;
		$name = "";
		if ( ! empty( $user->user_firstname ) ) {
			$name = " " . $user->user_firstname;
		} else {
			$user_data = get_userdata( $customer_id );
			$name = $user_data->user_login;
		}

		$subject = __( 'There was an error processing your Autoship order.', 'wc-autoship' );
		$title = __( 'There was an error processing your Autoship order.', 'wc-autoship' );
		$vars = array(
			'name' => $name,
			'message' => __( 'There was an error processing your Autoship order. Your Autoship Schedule has been paused.', 'wc-autoship' )
		);
		$body = wc_autoship_render_template( 'emails/autoship-error', $vars );
		$sent = send_wc_autoship_email( $recipient, $subject, $body, $title );
	} else {
		return false;
	}

}

function wc_autoship_send_schedule_email( $schedule_id, $subject, $title, $message ) {
	require_once( WC_AUTOSHIP_SRC_DIR . '/Models/Schedule.php' );
	if ( ! WC_Autoship_Models_Schedule::id_exists( $schedule_id ) ) {
		return false;
	}

	$schedule  = new WC_Autoship_Models_Schedule( $schedule_id );
	// Get our recipient info
	$customer           = $schedule->get_customer();
	$email              = $customer->user_email;
	$customer_id        = $customer->ID;
	$shipping_first_name = get_user_meta( $customer_id, 'shipping_first_name', true );
	$shipping_last_name = get_user_meta( $customer_id, 'shipping_last_name', true );
	$shipping_address   = get_user_meta( $customer_id, 'shipping_address_1', true );
	$shipping_address_2 = get_user_meta( $customer_id, 'shipping_address_2', true );
	$shipping_city      = get_user_meta( $customer_id, 'shipping_city', true );
	$shipping_state     = get_user_meta( $customer_id, 'shipping_state', true );
	$shipping_postcode  = get_user_meta( $customer_id, 'shipping_postcode', true );
	$shipping_country   = get_user_meta( $customer_id, 'shipping_country', true );
	$billing_first_name = get_user_meta( $customer_id, 'billing_first_name', true );
	$billing_last_name  = get_user_meta( $customer_id, 'billing_last_name', true );

	$billing_address    = get_user_meta( $customer_id, 'billing_address_1', true );
	$billing_address2   = get_user_meta( $customer_id, 'billing_address_2', true );
	$billing_city       = get_user_meta( $customer_id, 'billing_city', true );
	$billing_state      = get_user_meta( $customer_id, 'billing_state', true );
	$billing_postcode   = get_user_meta( $customer_id, 'billing_postcode', true );
	$billing_country    = get_user_meta( $customer_id, 'billing_country', true );

	$schedule_items = $schedule->get_items();
	$items = array();
	foreach( $schedule_items as $schedule_item ) {
		$product = $schedule_item->get_product();
		if ( $product != null ) {
			array_push( $items, $schedule_item );
		}
	}
	$next_ship_date     = $schedule->get_next_order_date();
	$frequency          = $schedule->get_autoship_frequency();
	$shipping_method_id = $schedule->get_shipping_method_id();
	$payment_method     = $schedule->get_payment_token();
	$schedule_status    = $schedule->get_autoship_status() == 1 ? 'Active' : 'Paused';


	$vars = array(
		'message'            => $message,
		'schedule_id'        => $schedule_id,
		'items'              => $items,
		'next_ship_date'     => $next_ship_date,
		'frequency'          => $frequency,
		'shipping_method_id' => $shipping_method_id,
		'payment_method'     => $payment_method,
		'schedule_status'    => $schedule_status,
		'shipping_first_name' => $shipping_first_name,
		'shipping_last_name' => $shipping_last_name,
		'shipping_address'   => $shipping_address,
		'shipping_address_2' => $shipping_address_2,
		'shipping_city'      => $shipping_city,
		'shipping_state'     => $shipping_state,
		'shipping_postcode'  => $shipping_postcode,
		'shipping_country'   => $shipping_country,
		'billing_first_name' => $billing_first_name,
		'billing_last_name'  => $billing_last_name,
		'billing_address'    => $billing_address,
		'billing_address2'   => $billing_address2,
		'billing_city'       => $billing_city,
		'billing_state'      => $billing_state,
		'billing_postcode'   => $billing_postcode,
		'billing_country'    => $billing_country,
		'email'              => $email
	);

	$body = wc_autoship_render_template( 'emails/general-schedule-template', $vars );
	$sent = send_wc_autoship_email( $email, $subject, $body, $title );
	return $sent;
}


function wc_autoship_send_10day_email( $schedule_id ) {
	$subject = __( 'Autoship Order in 10 days', 'wc-autoship' );
	$title   = __( 'Autoship Order in 10 days', 'wc-autoship' );
	$message = __( 'Your order is shipping in 10 days!', 'wc-autoship' );
	$sent = wc_autoship_send_schedule_email( $schedule_id, $subject, $title, $message );
	return $sent;
}

function wc_autoship_send_10day_emails() {
	$semaphore = wc_autoship_get_cron_semaphore( '10day-emails-semaphore', WC_AUTOSHIP_BATCH_INTERVAL );
	if ( ! $semaphore ) {
		return;
	}

	global $wpdb;
	if ( 'no' != get_option( 'wc_autoship_send_10day_email' ) ) {
		$schedules_result = $wpdb->get_results( $wpdb->prepare(
			"SELECT s.id
			FROM {$wpdb->prefix}wc_autoship_schedules AS s
			WHERE s.autoship_status = %s
			AND s.next_order_date = %s
			ORDER BY s.id ASC",
			WC_AUTOSHIP_STATUS_ACTIVE,
			date( 'Y-m-d', strtotime( '+10 day' ) )
		) );
		foreach ( $schedules_result as $schedule ) {
			wc_autoship_send_10day_email( $schedule->id );
		}
	}

	wc_autoship_release_cron_semaphore( $semaphore );
}
add_action( 'wc_autoship_notify_10day', 'wc_autoship_send_10day_emails');

function wc_autoship_send_1day_email( $schedule_id ) {
	$subject = __( 'Autoship Order tomorrow', 'wc-autoship' );
	$title   = __( 'Autoship Order tomorrow', 'wc-autoship' );
	$message = __( 'Your order is shipping tomorrow!', 'wc-autoship' );
	$sent = wc_autoship_send_schedule_email( $schedule_id, $subject, $title, $message );
	return $sent;
}

// This gets called once a day and sends notification emails to all orders one day from completing
function wc_autoship_send_1day_emails() {
	$semaphore = wc_autoship_get_cron_semaphore( '1day-emails-semaphore', WC_AUTOSHIP_BATCH_INTERVAL );
	if ( ! $semaphore ) {
		return;
	}

	global $wpdb;
	if ( 'no' != get_option( 'wc_autoship_send_1day_email' ) ) {
		$schedules_result = $wpdb->get_results( $wpdb->prepare(
			"SELECT s.id
			FROM {$wpdb->prefix}wc_autoship_schedules AS s
			WHERE s.autoship_status = %s
			AND s.next_order_date = %s
			ORDER BY s.id ASC",
			WC_AUTOSHIP_STATUS_ACTIVE,
			date( 'Y-m-d', strtotime( '+1 day' ) )
		) );
		foreach ( $schedules_result as $schedule ) {
			wc_autoship_send_1day_email( $schedule->id );
		}
	}

	wc_autoship_release_cron_semaphore( $semaphore );
}
add_action( 'wc_autoship_notify_1day', 'wc_autoship_send_1day_emails' );



