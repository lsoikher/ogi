<?php
/**
 * Plugin Name: GhostMonitor WooCommerce
 * Plugin URI: http://www.ghostmonitor.com
 * Description: Pre-built and Automated Cart Abandonment Campaign for WooCommerce
 * Author: Ghostmonitor INC
 * Author URI: http://www.ghostmonitor.com
 * Version: v1.8.1
 */

defined( 'ABSPATH' ) or die();

require_once 'includes/ghostmonitor_helper/vendor/autoload.php';

class Woocommerce_Ghostmonitor {
	private $config;

	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin' ) );

		add_filter( 'woocommerce_integrations', array( $this, 'wc_ghostmonitor' ), 10 );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_settings_link' ) );
		add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );

		$this->config = file_exists( plugin_dir_path( __FILE__ ) . 'config.json' ) ? json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'config.json' ) ) : false;
	}

	public function activate_plugin() {
		$gm_helper   = $this->get_gm_helper();
		$http_status = $gm_helper->testHTTP() !== false ? true : false;
		update_option( 'ghostmonitor_http_status', $http_status, true );

		$this->send_shop_info();
	}

	public function deactivate_plugin() {
		$this->send_shop_info();
	}

	public function send_shop_info() {
		global $woocommerce;
		global $wpdb;
		global $wp_version;

		$version = 'v1.8.1';

		$discount_enabled = get_option( 'woocommerce_enable_coupons' ) === 'yes' ? 'true' : 'false';
		$db_version       = (string) $wpdb->get_var( "SELECT VERSION()" );
		$gm_helper        = $this->get_gm_helper();
		$shop_data        = array(
			'plugin_type'           => 'woocommerce',
			'plugin_version'        => $this->config ? $this->config->version : $version,
			'engine_version'        => $woocommerce->version,
			'wordpress_version'     => $wp_version,
			'php_version'           => PHP_VERSION,
			'mysql_version'         => $db_version,
			'has_curl'              => extension_loaded( 'curl' ) ? 'true' : 'false',
			'is_discount_supported' => $discount_enabled,
		);
		$gm_helper->sendShopData( $shop_data );
	}

	public function wc_ghostmonitor( $integrations ) {
		global $woocommerce;

		if ( is_object( $woocommerce ) && version_compare( $woocommerce->version, '2.3', '>=' ) ) {
			define( 'GHOSTMONITOR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
			define( 'GHOSTMONITOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			include_once( 'includes/class-wc-ghostmonitor.php' );
			$integrations[] = 'wc_ghostmonitor';
		}

		return $integrations;
	}

	public function display_admin_notice() {
		$gm_config = ( $this->config && property_exists( $this->config, 'ghostmonitor_id' ) ? false : true );

		if ( $gm_config && ! get_option( 'woocommerce_wc_ghostmonitor_settings' ) && false === strpos( $_SERVER['REQUEST_URI'], 'admin.php?page=wc-settings&tab=integration' ) ) :?>
			<div class="update-nag" style="background-color: #ffba00;">
				<?php echo 'Welcome to GhostMonitor! Please make sure that your Site ID is entered correctly in <a href="' . admin_url( 'admin.php?page=wc-settings&tab=integration' ) . '">Settings</a>.'; ?>
			</div>
		<?php endif;
	}

	public function add_settings_link( $links ) {
		$links[] = '<a href="' . esc_url( get_admin_url( null, 'admin.php?page=wc-settings&tab=integration' ) ) . '">Settings</a>';

		return $links;
	}

	private function get_gm_helper() {
		$ghostmonitor_id = get_option( 'woocommerce_wc_ghostmonitor_settings' );
		$ghostmonitor_id = empty( $ghostmonitor_id['ghostmonitor_id'] ) ? $ghostmonitor_id['ghostmonitor_id'] : '';

		return new Ghostmonitor\Helper(
			$ghostmonitor_id,
			get_site_url(),
			$this->config ? $this->config->trackingUrl : false,
			$this->config ? $this->config->cdnUrl : false
		);
	}
}

new Woocommerce_Ghostmonitor();
