<?php

function wc_autoship_ajax_is_pipey_request() {
	return isset( $_SERVER['HTTP_X_WC_AUTOSHIP_PIPEY_KEY'] );
}

function wc_autoship_ajax_pipey_is_authorized() {
	$pipey_ip_list = wc_autoship_get_pipey_ip_list();
	if ( WC_AUTOSHIP_PIPEY_VERIFY_IP ) {
		if ( ! in_array( $_SERVER['REMOTE_ADDR'], $pipey_ip_list ) ) {
			$log_description = __( sprintf( 'Client IP mismatch: expected %s, received %s', implode( ",", $pipey_ip_list ), $_SERVER['REMOTE_ADDR'] ), 'wc-autoship' );
			$payload = file_get_contents( 'php://input' );
			wc_autoship_log_action( get_current_user_id(), 'auth_failed_client', $log_description, $_SERVER['REQUEST_URI'], NULL, NULL, NULL, $payload );
			return false;
		}
	}

	
	if ( ! isset( $_SERVER['HTTP_X_WC_AUTOSHIP_PIPEY_KEY'] ) ) {
		$log_description = __( "Auth failed due to missing access key. Autoship requires configuration.", 'wc-autoship' );
		$payload = file_get_contents( 'php://input' );
		wc_autoship_log_action( get_current_user_id(), 'auth_failed_missing_key', $log_description, $_SERVER['REQUEST_URI'], NULL, NULL, NULL, $payload );
		return false;
	}
	$key = wc_autoship_get_pipey_key();
	$request_key = base64_decode( $_SERVER['HTTP_X_WC_AUTOSHIP_PIPEY_KEY'] );
	if ( empty( $key ) || empty( $request_key ) || $key != $request_key ) {
		$log_description = __( "Auth failed due to invalid access key. Autoship requires configuration.", 'wc-autoship' );
		$payload = file_get_contents( 'php://input' );
		wc_autoship_log_action( get_current_user_id(), 'auth_failed_invalid_key', $log_description, $_SERVER['REQUEST_URI'], NULL, NULL, NULL, $payload );
		return false;
	}
	return true;
}

function wc_autoship_ajax_pipey_authorize() {
	if ( ! wc_autoship_ajax_pipey_is_authorized() ) {
		wc_autoship_ajax_result( 403 );
	}
}

function wc_autoship_ajax_pipey_get_ip() {
	$nonce = get_option( 'wc_autoship_get_pipey_ip_nonce' );
	if ( empty( $nonce ) || $nonce != $_POST['nonce'] ) {
		wc_autoship_ajax_result( 403 );
	}
	wc_autoship_ajax_result( 200, $_SERVER['REMOTE_ADDR'] );
}
add_action( 'wp_ajax_nopriv_wc_autoship_pipey_get_ip', 'wc_autoship_ajax_pipey_get_ip' );

function wc_autoship_ajax_pipey_get_cart() {
	wc_autoship_ajax_pipey_authorize();

	WC()->shipping()->calculate_shipping(WC()->cart->get_shipping_packages());
	$shipping_packages = WC()->shipping()->get_packages();

	$shipping_methods = array();
	if ( ! empty( $shipping_packages ) && isset( $shipping_packages[0]['rates'] ) ) {
		$shipping_methods = array_values( $shipping_packages[0]['rates'] );
	}

	$cart_data = array(
		'items' => WC()->cart->get_cart(),
		'shipping_methods' => $shipping_methods,
		'total' => wc_autoship_clean_price( WC()->cart->get_total() ),
		'subtotal' => wc_autoship_clean_price( WC()->cart->get_cart_subtotal() ),
		'discount_total' => wc_autoship_clean_price( WC()->cart->get_total_discount() ),
		'tax_total' => wc_autoship_clean_price( WC()->cart->get_cart_tax() ),
		'shipping_total' => wc_autoship_clean_price( WC()->cart->get_cart_shipping_total() ),
		'shipping_tax' => '',
		'user_id' => get_current_user_id(),
		'coupons' => WC()->cart->get_applied_coupons()
	);

	wc_autoship_ajax_result( 200, $cart_data );
}
add_action( 'wp_ajax_wc_autoship_pipey_get_cart', 'wc_autoship_ajax_pipey_get_cart' );

function wc_autoship_ajax_pipey_create_update_order_review_nonce() {
	wc_autoship_ajax_pipey_authorize();
	$data = array(
		'nonce' => wp_create_nonce( 'update-order-review' ),
		'referer' => $_SERVER['REQUEST_URI']
	);
	wc_autoship_ajax_result( 200, $data );
}
add_action( 'wp_ajax_wc_autoship_pipey_create_update_order_review_nonce', 'wc_autoship_ajax_pipey_create_update_order_review_nonce' );

function wc_autoship_ajax_pipey_create_woocommerce_process_checkout_nonce() {
	wc_autoship_ajax_pipey_authorize();
	$data = array(
		'nonce' => wp_create_nonce( 'woocommerce-process_checkout' ),
		'referer' => $_SERVER['REQUEST_URI']
	);
	wc_autoship_ajax_result( 200, $data );
}
add_action( 'wp_ajax_wc_autoship_pipey_create_woocommerce_process_checkout_nonce', 'wc_autoship_ajax_pipey_create_woocommerce_process_checkout_nonce' );

function wc_autoship_ajax_pipey_create_apply_coupon_nonce() {
	wc_autoship_ajax_pipey_authorize();
	$data = array(
		'nonce' => wp_create_nonce( 'apply-coupon' ),
		'referer' => $_SERVER['REQUEST_URI']
	);
	wc_autoship_ajax_result( 200, $data );
}
add_action( 'wp_ajax_wc_autoship_pipey_create_apply_coupon_nonce', 'wc_autoship_ajax_pipey_create_apply_coupon_nonce' );

function wc_autoship_ajax_pipey_create_update_shipping_method_nonce() {
	wc_autoship_ajax_pipey_authorize();
	$data = array(
		'nonce' => wp_create_nonce( 'update-shipping-method' ),
		'referer' => $_SERVER['REQUEST_URI']
	);
	wc_autoship_ajax_result( 200, $data );
}
add_action( 'wp_ajax_wc_autoship_pipey_create_update_shipping_method_nonce', 'wc_autoship_ajax_pipey_create_update_shipping_method_nonce' );

function wc_autoship_ajax_pipey_set_user() {
	wc_autoship_ajax_pipey_authorize();
	$user_id = $_POST['user_id'];
	$user = get_user_by( 'id', $user_id );
	if( $user ) {
		wp_set_current_user( $user_id, $user->user_login );
		wp_set_auth_cookie( $user_id );
		do_action( 'wp_login', $user->user_login );
		wc_autoship_ajax_result( 200, null );
	}
	wc_autoship_ajax_result( 500, null );
}
add_action( 'wp_ajax_wc_autoship_pipey_set_user', 'wc_autoship_ajax_pipey_set_user' );
add_action( 'wp_ajax_nopriv_wc_autoship_pipey_set_user', 'wc_autoship_ajax_pipey_set_user' );

function wc_autoship_ajax_pipey_empty_cart() {
	wc_autoship_ajax_pipey_authorize();
	WC()->cart->empty_cart( true );
	wc_clear_notices();
	wc_autoship_ajax_result( 200, null );
}
add_action( 'wp_ajax_wc_autoship_pipey_empty_cart', 'wc_autoship_ajax_pipey_empty_cart' );
