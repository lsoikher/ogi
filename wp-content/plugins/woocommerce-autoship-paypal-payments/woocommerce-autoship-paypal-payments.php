<?php
/*
Plugin Name: WC Autoship PayPal Payments
Plugin URI: http://wooautoship.com
Description: PayPal Payments for WC Autoship
Version: 2.0.5
Author: Patterns in the Cloud
Author URI: http://patternsinthecloud.com
License: Single-site
*/

define( 'WC_AUTOSHIP_PAYPAL_PAYMENTS_VERSION', '2.0.5' );
	
function wc_autoship_paypal_payments_install() {

}
register_activation_hook( __FILE__, 'wc_autoship_paypal_payments_install' );

function wc_autoship_paypal_payments_deactivate() {

}
register_deactivation_hook( __FILE__, 'wc_autoship_paypal_payments_deactivate' );

function wc_autoship_paypal_payments_uninstall() {

}
register_uninstall_hook( __FILE__, 'wc_autoship_paypal_payments_uninstall' );

function wc_autoship_load_gateway_class() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	// Initialize WooCommerce
	if ( is_plugin_active( 'woocommerce-autoship/woocommerce-autoship.php' ) && function_exists( 'WC' ) ) {
		WC();
		// Include gateway class
		require_once( 'classes/wc-autoship-paypal-gateway.php' );
	}
}
// add_action( 'plugins_loaded', 'wc_autoship_load_gateway_class' );

function wc_autoship_paypal_payments_register_gateway( $methods ) {
	require_once( 'classes/wc-autoship-paypal-gateway.php' );
	$methods[] = 'WC_Autoship_Paypal_Gateway';
	return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'wc_autoship_paypal_payments_register_gateway' );

function wc_autoship_paypal_payments_ipn_callback() {
	WC();
	// Include gateway class
	require_once( 'classes/wc-autoship-paypal-gateway.php' );
	// Create instance
	$payment_gateway = new WC_Autoship_Paypal_Gateway();
	// Process callback
	$payment_gateway->api_callback();
}
add_action( 'wp_ajax_wc_autoship_paypal_payments_ipn_callback', 'wc_autoship_paypal_payments_ipn_callback' );
add_action( 'wp_ajax_nopriv_wc_autoship_paypal_payments_ipn_callback', 'wc_autoship_paypal_payments_ipn_callback' );

function wc_autoship_paypal_payments_scripts() {
	$base_uri = plugins_url( '' , __FILE__ );

	wp_enqueue_style(
		'wc-autoship-paypal-payments',
		$base_uri . '/css/styles.css'
	);

}
add_action( 'wp_enqueue_scripts', 'wc_autoship_paypal_payments_scripts' );

function wc_autoship_paypal_payments_addon_license_keys( $addon_license_keys ) {
	if ( ! isset( $addon_license_keys['wc_autoship_paypal_payments_license_key'] ) ) {
		$settings = get_option( 'woocommerce_wc_autoship_paypal_settings' );
		if ( ! $settings ) {
			return;
		}

		if ( ! isset( $settings['license_key'] ) ) {
			return;
		}

		$license_key = trim( $settings['license_key'] );
		$addon_license_keys['wc_autoship_paypal_payments_license_key'] = array(
			'item_name' => 'WC Autoship PayPal Payments',
			'license' => $license_key,
			'version' => WC_AUTOSHIP_PAYPAL_PAYMENTS_VERSION,
			'plugin_file' => __FILE__
		);
	}
	return $addon_license_keys;
}
add_filter( 'wc_autoship_addon_license_keys', 'wc_autoship_paypal_payments_addon_license_keys', 10, 1 );
