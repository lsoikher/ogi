<?php
/**
 * WooCommerce Stamps.com Export
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Stamps.com Export to newer
 * versions in the future. If you wish to customize WooCommerce Stamps.com Export for your
 * needs please refer to http://docs.woothemes.com/document/stamps-com-xml-file-export/ for more information.
 *
 * @package     WC-Stamps-Com/Admin
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2014, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Stamps.com Admin Class
 *
 * Loads admin UX and functionality
 *
 * @since 2.0
 */
class WC_Stamps_Com_Admin {


	/** @var string sub-menu page hook suffix */
	public $page;

	/** @var array tab IDs / titles */
	public $tabs;

	/** @var SV_WP_Admin_Message_Handler instance */
	public $message_handler;


	/**
	 * Setup admin class
	 *
	 * @since 2.0
	 */
	public function __construct() {

		$this->tabs = array(
			'export'   => __( 'Export', WC_Stamps_Com::TEXT_DOMAIN ),
			'settings' => __( 'Settings', WC_Stamps_Com::TEXT_DOMAIN ),
		);

		// Load datepicker on export page
		add_action( 'admin_enqueue_scripts', array( $this, 'load_datepicker' ) );

		// Load WC styles / scripts
		add_filter( 'woocommerce_screen_ids', array( $this, 'load_wc_scripts' ) );

		// process bulk export
		add_action( 'admin_init', array( $this, 'process_export' ) );

		// Add 'Stamps.com Export' link under WooCommerce menu
		add_action( 'admin_menu', array( $this, 'add_menu_link' ) );

		// Add 'Download Stamps.com XML' action on orders page
		add_action( 'woocommerce_admin_order_actions_end', array( $this, 'add_order_action' ) );

		// Add 'Download Stamps.com XML' order meta box order action
		add_action( 'woocommerce_order_actions', array( $this, 'add_order_meta_box_actions' ) );

		// Process 'Download Stamps.com XML' order meta box order action
		add_action( 'woocommerce_order_action_wc_stamps_com_download_xml', array( $this, 'process_order_meta_box_actions' ) );

		// Add bulk action to download stamps.com XML for multiple orders
		add_action( 'admin_footer-edit.php', array( $this, 'add_order_bulk_actions' ) );

		// Process bulk action to download stamps.com XML for multiple orders
		add_action( 'load-edit.php', array( $this, 'process_order_bulk_actions' ) );
	}


	/**
	 * Load datepicker on export page
	 *
	 * @since 2.0
	 * @param $hook_suffix
	 */
	public function load_datepicker( $hook_suffix ) {
		global $wp_scripts;

		if ( 'edit.php' == $hook_suffix || 'post.php' == $hook_suffix ) {

			// admin CSS
			wp_enqueue_style( 'wc-stamps-com-admin', $GLOBALS['wc_stamps_com']->get_plugin_url() . '/assets/css/admin/wc-stamps-com-admin.min.css', array( 'dashicons' ), WC_Stamps_Com::VERSION );
		}

		// load datepicker CSS on export page
		if ( $this->page == $hook_suffix ) {

			// enqueue script
			wp_enqueue_script( 'jquery-ui-datepicker' );

			// get jQuery UI version
			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			// enqueue UI CSS
			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
		}
	}


	/**
	 * Add settings/export screen ID to the list of pages for WC to load its JS on
	 *
	 * @since 2.0
	 * @param array $screen_ids
	 * @return array
	 */
	public function load_wc_scripts( $screen_ids ) {

		$screen_ids[] = 'woocommerce_page_wc_stamps_com';

		return $screen_ids;
	}


	/**
	 * Add 'Stamps.com Export' sub-menu link under 'WooCommerce' top level menu
	 *
	 * @since 2.0
	 */
	public function add_menu_link() {

		$this->page = add_submenu_page(
			'woocommerce',
			__( 'Stamps.com Export', WC_Stamps_Com::TEXT_DOMAIN ),
			__( 'Stamps.com Export', WC_Stamps_Com::TEXT_DOMAIN ),
			'manage_woocommerce',
			'wc_stamps_com',
			array( $this, 'render_submenu_pages' )
		);
	}


	/**
	 * Render the sub-menu page for 'CSV Export'
	 *
	 * @since 2.1
	 */
	public function render_submenu_pages() {

		// permissions check
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// load woocommerce_admin_fields()/woocommerce_update_options() methods
		SV_WC_Plugin_Compatibility::load_wc_admin_functions();

		$current_tab = ( empty( $_GET[ 'tab' ] ) ) ? 'export' : urldecode( $_GET[ 'tab' ] );

		// settings
		if ( ! empty( $_POST ) && 'settings' == $current_tab ) {

			// security check
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], __FILE__ ) ) {

				wp_die( __( 'Action failed. Please refresh the page and retry.', WC_Stamps_com::TEXT_DOMAIN ) );
			}

			// save settings
			woocommerce_update_options( $this->get_settings( 'settings' ) );

			$this->message_handler->add_message( __( 'Your settings have been saved.', WC_Stamps_com::TEXT_DOMAIN ) );
		}

		?>
		<div class="wrap woocommerce">
			<form method="post" id="mainform" action="" enctype="multipart/form-data">
				<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
					<?php
					foreach ( $this->tabs as $tab_id => $tab_title ) :

						$class = ( $tab_id == $current_tab ) ? 'nav-tab nav-tab-active' : 'nav-tab';
						$url   = add_query_arg( 'tab', $tab_id, admin_url( 'admin.php?page=wc_stamps_com' ) );

						printf( '<a href="%s" class="%s">%s</a>', $url, $class, $tab_title );

					endforeach;
					?> </h2> <?php

				$this->message_handler->show_messages();

				if ( 'settings' == $current_tab ) {

					$this->render_settings_page();

				} else {

					$this->render_export_page();
				}

				?> </form>
		</div> <?php
	}


	/**
	 * Show Export page
	 *
	 * @since 2.1
	 */
	private function render_export_page() {

		// permissions check
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// show export form
		woocommerce_admin_fields( $this->get_settings( 'export' ) );

		// helper input
		?><input type="hidden" name="wc_stamps_com_export" value="1" /><?php

		// datepicker js
		SV_WC_Plugin_Compatibility::wc_enqueue_js( '
			// start date
			$( "#wc_stamps_com_export_start_date" ).datepicker( {
				dateFormat      : "yy-mm-dd",
				numberOfMonths  : 1,
				showButtonPanel : true,
				showOn          : "button",
				buttonImage     : "' . SV_WC_Plugin_Compatibility::WC()->plugin_url(). '/assets/images/calendar.png' . '",
				buttonImageOnly : true
			} );
			// end date
			$( "#wc_stamps_com_export_end_date" ).datepicker( {
				dateFormat      : "yy-mm-dd",
				numberOfMonths  : 1,
				showButtonPanel : true,
				showOn          : "button",
				buttonImage     : "' . SV_WC_Plugin_Compatibility::WC()->plugin_url(). '/assets/images/calendar.png' . '",
				buttonImageOnly : true
			} );
			//$( "div.woocommerce" ).find( "table:first" ).find( "th" ).hide();
			$( "select.chosen_select" ).chosen();
		' );

		wp_nonce_field( __FILE__ );
		submit_button( __( 'Export', WC_Stamps_com::TEXT_DOMAIN ) );
	}


	/**
	 * Process bulk export
	 *
	 * Note this is hooked into `admin_init` as WC 2.1+ interferes with sending headers() from a sub-menu page
	 *
	 * @since 2.1
	 */
	public function process_export() {

		if ( ! isset( $_POST['wc_stamps_com_export'] ) ) {
			return;
		}

		// security check
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], __FILE__ ) ) {

			wp_die( __( 'Action failed. Please refresh the page and retry.', WC_Stamps_com::TEXT_DOMAIN ) );
		}

		$query_args = array(
			'fields'      => 'ids',
			'post_type'   => 'shop_order',
			'post_status' => 'publish',
			'nopaging'    => true,
			'date_query'  => array(
				array(
					'columnn'   => 'post_date_gmt',
					'before'    => empty( $_POST['wc_stamps_com_export_end_date'] ) ? date( 'Y-m-d 23:59' ) : $_POST['wc_stamps_com_export_end_date'],
					'after'     => empty( $_POST['wc_stamps_com_export_start_date'] ) ? date( 'Y-m-d', 0 ) : $_POST['wc_stamps_com_export_start_date'],
					'inclusive' => true,
				),
			),
		);

		// add order statuses
		if ( ! empty( $_POST['wc_stamps_com_statuses'] ) ) {

			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'shop_order_status',
					'field'    => 'slug',
					'terms'    => $_POST['wc_stamps_com_statuses'],
					'operator' => 'IN',
				),
			);
		}

		// get order IDs
		$query = new WP_Query( $query_args );

		if ( $query->post_count ) {

			$export = new WC_Stamps_Com_Exporter( $query->posts );

			$export->download();
		} else {

			$this->message_handler->add_message( __( 'No orders found to export', WC_Stamps_Com::TEXT_DOMAIN ) );
		}
	}


	/**
	 * Show Settings page
	 *
	 * @since 1.0.8-1
	 */
	private function render_settings_page() {

		// render settings fields
		woocommerce_admin_fields( $this->get_settings( 'settings' ) );

		wp_nonce_field( __FILE__ );
		submit_button( __( 'Save settings', WC_Stamps_com::TEXT_DOMAIN ) );
	}


	/**
	 * Adds 'Download Stamps.com XML' order action to 'Order Actions' column
	 * Processed via AJAX
	 *
	 * @since 2.0
	 * @param object $order WC_Order object
	 */
	public function add_order_action( $order ) {

		$action = 'download_stamps_com';
		$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=wc_stamps_com_download_xml&order_id=' . $order->id ), 'wc_stamps_com_download_xml' );
		$name = __( 'Download to Stamps.com XML', WC_Stamps_com::TEXT_DOMAIN );

		printf( '<a class="button tips %s" href="%s" data-tip="%s">%s</a>', $action, esc_url( $url ), $name, $name );
	}


	/**
	 * Add 'Download Stamps.com XML' link to Order Actions dropdown
	 *
	 * @since 2.0
	 * @param array $actions order actions array to display
	 * @return array
	 */
	public function add_order_meta_box_actions( $actions ) {

		// add download to XML action
		$actions['wc_stamps_com_download_xml'] = __( 'Download Stamps.com XML', WC_Stamps_Com::TEXT_DOMAIN );

		return $actions;
	}


	/**
	 * Process the 'Download Stamps.com XML' link in Order Actions dropdown
	 *
	 * @since 2.0
	 * @param object $order \WC_Order object
	 */
	public function process_order_meta_box_actions( $order ) {

		$export = new WC_Stamps_Com_Exporter( $order->id );

		$export->download();
	}


	/**
	 * Add "Download Stamps.com XML" custom bulk action to the 'Orders' page bulk action drop-down
	 *
	 * @since 2.0
	 */
	public function add_order_bulk_actions() {
		global $post_type, $post_status;

		if ( $post_type == 'shop_order' && $post_status != 'trash' ) {
			?>
				<script type="text/javascript">
					jQuery( document ).ready(function ( $ ) {
						var $downloadXml = $( '<option>' ).val( 'download_xml' ).text( '<?php _e( 'Download Stamps.com XML', WC_Stamps_Com::TEXT_DOMAIN )?>' );
						$( 'select[name^="action"]' ).append( $downloadXml );
					});
				</script>
			<?php
		}
	}


	/**
	 * Processes the "Download Stamps.com XML" custom bulk action on the 'Orders' page bulk action drop-down
	 *
	 * @since 2.0
	 */
	public function process_order_bulk_actions() {
		global $typenow;

		if ( 'shop_order' == $typenow ) {

			// get the action
			$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
			$action        = $wp_list_table->current_action();

			// return if not processing our actions
			if ( ! in_array( $action, array( 'download_xml' ) ) ) {
				return;
			}

			// security check
			check_admin_referer( 'bulk-posts' );

			// make sure order IDs are submitted
			if ( isset( $_REQUEST['post'] ) ) {
				$order_ids = array_map( 'absint', $_REQUEST['post'] );
			}

			// return if there are no orders to export
			if ( empty( $order_ids ) ) {
				return;
			}

			// give ourselves an unlimited timeout if possible
			@set_time_limit( 0 );

			if ( 'download_xml' == $action ) {

				// setup export class
				$export = new WC_Stamps_Com_Exporter( $order_ids );

				// download the orders
				$export->download();
			}
		}
	}


	/**
	 * Returns settings array for use by output/save functions
	 *
	 * @since 2.0
	 * @param string $tab_id
	 * @return array
	 */
	public static function get_settings( $tab_id ) {

		// get available order statuses
		$order_status_terms = get_terms( 'shop_order_status', array( 'hide_empty' => false ) );

		if ( is_wp_error( $order_status_terms ) ) {

			$order_status_terms = array();
		}

		$order_statuses = array();

		foreach ( $order_status_terms as $term ) {

			$order_statuses[ $term->slug ] = $term->name;
		}

		$settings = array(

			'settings' => array(

				array(
					'name' => __( 'General Settings', WC_Stamps_Com::TEXT_DOMAIN ),
					'type' => 'title',
				),

				array(
					'id'       => 'wc_stamps_com_export_file_name',
					'name'     => __( 'Export Filename', WC_Stamps_Com::TEXT_DOMAIN ),
					'desc_tip' => __( 'The XML filename for exported orders. Use %%timestamp%% to represent the date in the filename.', WC_Stamps_Com::TEXT_DOMAIN ),
					'default'  => 'woocommerce-orders.xml',
					'css'      => 'min-width: 300px;',
					'type'     => 'text',
				),

				array(
					'id'      => 'wc_stamps_com_attach_exports',
					'name'    => __( 'Attach order XML file to admin new order email', WC_Stamps_Com::TEXT_DOMAIN ),
					'desc'    => __( 'Enable this to attach the order XML file to the admin new order email notification.', WC_Stamps_Com::TEXT_DOMAIN ),
					'default' => 'no',
					'type'    => 'checkbox'
				),

				array( 'type' => 'sectionend' ),

				array(
					'name'        => __( 'Export Settings', WC_Stamps_Com::TEXT_DOMAIN ),
					'type'        => 'title',
					'description' => __( 'These settings will apply to all exports, either on the bulk export tab or on the Orders page.', WC_Stamps_Com::TEXT_DOMAIN ),
				),

				array(
					'id'      => 'wc_stamps_com_include_customs_info',
					'name'    => __( 'Include Customs Information', WC_Stamps_Com::TEXT_DOMAIN ),
					'desc'    => __( 'Enable this to include customs information in the exported XML.', WC_Stamps_Com::TEXT_DOMAIN ),
					'default' => 'no',
					'type'    => 'checkbox',
				),

				array( 'type' => 'sectionend' ),

			),

			'export' => array(

				array(
					'name' => __( 'Order Statuses', WC_Stamps_Com::TEXT_DOMAIN ),
					'type' => 'title',
				),

				array(
					'id'       => 'wc_stamps_com_statuses',
					'name'     => __( 'Order Statuses', WC_Stamps_Com::TEXT_DOMAIN ),
					'desc_tip' => __( 'Orders with these statuses will be included in the export.', WC_Stamps_Com::TEXT_DOMAIN ),
					'type'     => 'multiselect',
					'options'  => $order_statuses,
					'default'  => '',
					'class'    => 'chosen_select',
					'css'      => 'min-width: 250px',
				),

				array( 'type' => 'sectionend' ),

				array(
					'name' => __( 'Date Range', WC_Stamps_Com::TEXT_DOMAIN ),
					'type' => 'title',
					'desc' => __( 'Orders created during these dates will be included in the exported file.', WC_Stamps_Com::TEXT_DOMAIN )
				),

				array(
					'id'   => 'wc_stamps_com_export_start_date',
					'name' => __( 'Start Date', WC_Stamps_Com::TEXT_DOMAIN ),
					'desc' => __( 'Start date of orders to include in the exported file, in the format <code>YYYY-MM-DD.</code>', WC_Stamps_Com::TEXT_DOMAIN ),
					'type' => 'text',
				),

				array(
					'id'   => 'wc_stamps_com_export_end_date',
					'name' => __( 'End Date', WC_Stamps_Com::TEXT_DOMAIN ),
					'desc' => __( 'End date of orders to include in the exported file, in the format <code>YYYY-MM-DD.</code>', WC_Stamps_Com::TEXT_DOMAIN ),
					'type' => 'text',
				),

				array( 'type' => 'sectionend' ),
			)
		);

		return $settings[ $tab_id ];
	}


} // end \WC_Stamps_Com_Admin class
