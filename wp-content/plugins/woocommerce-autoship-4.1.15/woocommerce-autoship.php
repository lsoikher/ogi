<?php
/*
Plugin Name: WC Autoship
Plugin URI: http://wooautoship.com
Description: Add autoship options to products in WooCommerce
Version: 4.1.15
Author: Patterns in the Cloud
Author URI: http://patternsinthecloud.com
License: Single-site
*/

/*
 * Define constants
 */
/** @const WC Autoship Version */
define( 'WC_AUTOSHIP_VERSION', '4.1.15' );
if ( ! defined( 'WC_AUTOSHIP_PLUGIN_FILE' ) ) {
	define( 'WC_AUTOSHIP_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'WC_AUTOSHIP_PLUGIN_DIR' ) ) {
	define( 'WC_AUTOSHIP_PLUGIN_DIR', __DIR__ );
}
if ( ! defined( 'WC_AUTOSHIP_SRC_DIR' ) ) {
	define( 'WC_AUTOSHIP_SRC_DIR', WC_AUTOSHIP_PLUGIN_DIR . '/src' );
}
if ( ! defined( 'WC_AUTOSHIP_STATUS_ACTIVE' ) ) {
	define( 'WC_AUTOSHIP_STATUS_ACTIVE', 1 );
}
if ( ! defined( 'WC_AUTOSHIP_STATUS_PAUSED' ) ) {
	define( 'WC_AUTOSHIP_STATUS_PAUSED', 0 );
}
if ( ! defined( 'WC_AUTOSHIP_STATUS_INACTIVE' ) ) {
	define( 'WC_AUTOSHIP_STATUS_INACTIVE', -1 );
}
if ( ! defined( 'WC_AUTOSHIP_BATCH_INTERVAL' ) ) {
	/** @const Interval in seconds between autoship orders batches */
	define( 'WC_AUTOSHIP_BATCH_INTERVAL', 300 );
}
if ( ! defined( 'WC_AUTOSHIP_BATCH_SIZE' ) ) {
	/** @const Batch size for pending autoship orders */
	define( 'WC_AUTOSHIP_BATCH_SIZE', 2 );
}
if ( ! defined( 'WC_AUTOSHIP_SEMAPHORE_DIR' ) ) {
	/** @const Directory for cron semaphores */
	define( 'WC_AUTOSHIP_SEMAPHORE_DIR', WP_CONTENT_DIR . '/uploads/wc-autoship' );
}
if ( ! defined( 'WC_AUTOSHIP_PIPEY_DIR' ) ) {
	/** @const Directory for pipey client */
	define( 'WC_AUTOSHIP_PIPEY_DIR', sys_get_temp_dir() );
}
if ( ! defined( 'WC_AUTOSHIP_PIPEY_USER_AGENT' ) ) {
	/** @const Pipey user agent */
	define( 'WC_AUTOSHIP_PIPEY_USER_AGENT', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36' );
}
if ( ! defined( 'WC_AUTOSHIP_PIPEY_VERIFY_IP' ) ) {
	/** @const Verify the IP address for the pipey client */
	define( 'WC_AUTOSHIP_PIPEY_VERIFY_IP', true );
}
if ( ! defined( 'WC_AUTOSHIP_PIPEY_IP' ) ) {
	define( 'WC_AUTOSHIP_PIPEY_IP', '');
}
if ( ! defined( 'WC_AUTOSHIP_PIPEY_AUTH' ) ) {
	/** @const Set auth for the pipey client */
	define( 'WC_AUTOSHIP_PIPEY_AUTH', '' );
}
if ( ! defined( 'WC_AUTOSHIP_MIN_FREQUENCY' ) ) {
	/** @const Minimum Autoship Frequency */
	define( 'WC_AUTOSHIP_MIN_FREQUENCY', 7 );
}
if ( ! defined( 'WC_AUTOSHIP_MAX_FREQUENCY' ) ) {
	/** @const Maximum Autoship Frequency */
	define( 'WC_AUTOSHIP_MAX_FREQUENCY', 365 );
}

// Include startup files
require_once( 'src/settings.php' );
require_once( 'src/install.php' );
// Include source files
require_once( 'src/admin.php' );
require_once( 'src/ajax.php' );
require_once( 'src/cache.php' );
require_once( 'src/cron.php' );
require_once( 'src/cart.php' );
require_once( 'src/checkout.php' );
require_once( 'src/coupons.php' );
require_once( 'src/emails.php' );
require_once( 'src/customers.php' );
require_once( 'src/pages.php' );
require_once( 'src/payment_tokens.php' );
require_once( 'src/products.php' );
require_once( 'src/scripts.php' );
require_once( 'src/shipping.php' );
require_once( 'src/shortcodes.php' );
require_once( 'src/templates.php' );
require_once( 'src/text.php' );
require_once( 'src/languages.php' );
require_once( 'src/analytics.php' );



// 	function wc_autoship_plugin_update_message() {

// 	}
// 	$plugin_file   = basename( __FILE__ );
// 	$plugin_folder = basename( dirname( __FILE__ ) );
// 	$update_hook = "in_plugin_update_message-{$plugin_folder}/{$plugin_file}";
// 	add_action( $update_hook, 'wc_autoship_plugin_update_message', 20, 2 );

function wc_autoship_plugin_updater() {
	$license_key = trim( get_option( 'wc_autoship_license_key' ) );
	if ( empty( $license_key ) ) {
		return;
	}

	require_once( 'src/edd/wc-autoship-plugin-updater.php' );
	new WC_Autoship_Plugin_Updater( wc_autoship_get_licensing_url(), __FILE__, array(
		'version' => WC_AUTOSHIP_VERSION,
		'license' => $license_key,
		'item_name' => 'WC Autoship',
		'author' => 'Patterns In the Cloud'
	) );

	// Addons
	$addon_license_keys = apply_filters( 'wc_autoship_addon_license_keys', array() );
	foreach ( $addon_license_keys as $args ) {
		new WC_Autoship_Plugin_Updater( wc_autoship_get_licensing_url(), $args['plugin_file'], array(
			'version' => $args['version'],
			'license' => $args['license'],
			'item_name' => urlencode( $args['item_name'] ),
			'author' => 'Patterns In the Cloud'
		) );
	}
}
add_action( 'admin_init', 'wc_autoship_plugin_updater', 0 );