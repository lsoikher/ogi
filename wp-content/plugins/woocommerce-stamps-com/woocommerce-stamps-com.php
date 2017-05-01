<?php
/**
 * Plugin Name: WooCommerce Stamps.com Export
 * Plugin URI: http://www.woothemes.com/products/stamps-com-xml-file-export/
 * Description: A full-featured XML Export Suite designed for <a href="http://www.stamps.com">Stamps.com</a>.
 * Author: SkyVerge
 * Author URI: http://www.skyverge.com
 * Version: 2.1.1
 * Text Domain: woocommerce-stamps-com
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2013-2014 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    WC-Stamps-Com
 * @author     SkyVerge
 * @category   Export
 * @copyright  Copyright (c) 2013-2014, SkyVerge, Inc.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '219cf5ac22b45009c09634bd4a8157aa', '122136' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '2.0.2', __( 'WooCommerce Stamps.com', 'woocommerce-stamps-com' ), __FILE__, 'init_woocommerce_stamps_com' );

function init_woocommerce_stamps_com() {

/**
 * # WooCommerce Stamps.com Export Suite
 *
 * ## Plugin Overview
 *
 * This plugin allows shop admins to export orders to XML in the format required for import into the Stamps.com batch
 * processing desktop client.
 *
 * ## Admin Considerations
 *
 * There are 4 primary ways the admin may export the XML:
 * 1) Orders List page using the 'Stamps.com' order action button
 * 2) Orders List page using the bulk action 'Download Stamps.com XML'
 * 3) Edit Orders page using the Order actions dropdown
 * 4) WooCommerce > Stamps.com Export > Export tab to bulk export *ALL* orders with a specific status, and start/end date
 *
 * ## Database
 *
 * + `wc_stamps_com_version` - the current plugin version, set on install/upgrade
 *
 * ### Global settings
 *
 * These settings are found on WooCommerce > Stamps.com Export
 *
 * + `wc_stamps_com_export_file_name` - the filename used for the download XML files
 * + `wc_stamps_com_attach_exports` - "yes" to attach the exported order XML file to the admin new order notification email
 * + `wc_stamps_com_include_customs_info` - "yes" to include customs information in the exported XML file
 *
 */
class WC_Stamps_Com extends SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '2.1.1';

	/** plugin id */
	const PLUGIN_ID = 'stamps_com';

	/** plugin text domain */
	const TEXT_DOMAIN = 'woocommerce-stamps-com';

	/** @var \WC_Stamps_Com_Admin instance */
	public $admin;


	/**
	 * Initializes the plugin
	 *
	 * @since 1.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			self::TEXT_DOMAIN,
			array( 'dependencies' => array( 'xmlwriter' ) )
		);

		// include required classes
		$this->includes();

		// attach exported XML file to admin new order notification email
		if ( 'yes' === get_option( 'wc_stamps_com_attach_exports' ) ) {
			add_filter( 'woocommerce_email_attachments', array( $this, 'attach_xml' ), 10, 3 );
		}

		// Process 'Download Stamps.com XML' action on orders page
		add_action( 'wp_ajax_wc_stamps_com_download_xml', array( $this, 'process_order_export' ) );
	}


	/**
	 * Include required files
	 *
	 * @since 2.1
	 */
	public function includes() {

		// export handler
		require_once( 'includes/class-wc-stamps-com-exporter.php' );

		// XML writer
		require_once( 'includes/class-wc-stamps-com-xml-writer.php' );

		// admin
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {

			$this->admin_includes();
		}
	}


	/**
	 * Include required files
	 *
	 * @since 2.0
	 */
	public function admin_includes() {

		// admin class
		require_once( 'includes/admin/class-wc-stamps-com-admin.php' );
		$this->admin = new WC_Stamps_Com_Admin();

		// message handler
		$this->admin->message_handler = $this->get_message_handler();
	}


	/**
	 * Load plugin text domain.
	 *
	 * @since 1.0
	 * @see SV_WC_Plugin::load_translation()
	 */
	public function load_translation() {

		load_plugin_textdomain( 'woocommerce-stamps-com', false, dirname( plugin_basename( $this->get_file() ) ) . '/i18n/languages' );
	}


	/** Admin methods ******************************************************/


	/**
	 * Processes 'Download Stamps.com XML' AJAX order action
	 *
	 * @since 2.1
	 */
	public function process_order_export() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', WC_Stamps_Com::TEXT_DOMAIN ) );
		}

		if ( ! check_admin_referer( 'wc_stamps_com_download_xml' ) ) {
			wp_die( __( 'You have taken too long, please go back and try again.', WC_Stamps_Com::TEXT_DOMAIN ) );
		}

		$order_id = isset( $_GET['order_id'] ) && is_numeric( $_GET['order_id'] ) ? (int) $_GET['order_id'] : '';

		if ( ! $order_id ) {
			die;
		}

		$export = new WC_Stamps_Com_Exporter( $order_id );

		$export->download();

		wp_safe_redirect( wp_get_referer() );

		exit;
	}


	/**
	 * Maybe attach a generated XML file to the admin new order notification email
	 *
	 * This works by generating a temporary file, writing the XML to it, and passing the full filename as an attachment
	 * Note that errors are suppressed for the f* functions, as we don't want errors here to interfere with the order
	 * completion process. The process may not work on hosts with overly-strict permissions for PHP.
	 *
	 * See wp_tempnam() for an overview of the process to create a temporary file
	 *
	 * @since 2.0
	 */
	public function attach_xml( $attachments, $email_id, $order ) {

		// only attach XML to new order email notification to admins
		if ( 'new_order' !== $email_id ) {
			return $attachments;
		}

		$writer = new WC_Stamps_Com_XML_Writer();

		// get the order XML
		$order_xml = $writer->get_order_export_xml( array( $order->id ) );

		// set the attachment filename
		$filename =  sprintf( 'order-%s-%s.xml',$order->id, date( 'Y-m-d-H-s', current_time( 'timestamp' ) ) );

		// prepend the temp directory
		$filename = get_temp_dir() . $filename;

		// create the file
		touch( $filename );

		// open the file, write XML, and close it
		$handle = @fopen( $filename, 'w+');
		@fwrite( $handle, $order_xml );
		@fclose( $handle );

		// make sure the temp file is removed after the email is sent
		$this->temp_filename = $filename;
		register_shutdown_function( create_function( '', 'global $wc_stamps_com; @unlink( $wc_stamps_com->temp_filename );' ) );

		// add the XML file to the list of attachment
		if ( ! empty( $filename ) ) {
			$attachments[] = $filename;
		}

		return $attachments;
	}


	/** Helper methods ******************************************************/


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 2.1
	 * @see SV_WC_Plugin::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Stamps.com Export', self::TEXT_DOMAIN );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 2.1
	 * @see SV_WC_Plugin::get_file()
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the URL to the settings page
	 *
	 * @since 2.1
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @param string $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = '' ) {

		return admin_url( 'admin.php?page=wc_stamps_com&tab=settings' );
	}


	/**
	 * Returns true if on the gateway settings page
	 *
	 * @since 1.0.8-1
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @return boolean true if on the settings page
	 */
	public function is_plugin_settings() {

		return ( isset( $_GET['page'] ) && 'wc_stamps_com' == $_GET['page'] );
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Run every time.  Used since the activation hook is not executed when updating a plugin
	 *
	 * @since 2.0
	 * @see SV_WC_Plugin::install()
	 */
	public function install() {

		require_once( 'includes/admin/class-wc-stamps-com-admin.php' );

		// install default settings
		foreach ( WC_Stamps_Com_Admin::get_settings( 'settings' ) as $setting ) {

			if ( isset( $setting['default'] ) ) {
				update_option( $setting['id'], $setting['default'] );
			}
		}

	}


} // end \WC_Stamps_Com class


/**
 * The WC_Stamps_Com global object
 * @name $wc_stamps_com
 * @global WC_Stamps_Com $GLOBALS['wc_stamps_com']
 */
$GLOBALS['wc_stamps_com'] = new WC_Stamps_Com();

} // init_woocommerce_stamps_com()
