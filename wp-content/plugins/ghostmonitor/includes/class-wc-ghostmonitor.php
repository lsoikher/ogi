<?php
/**
 * GhostMonitor Integration
 *
 * Allows GhostMonitor tracking code to be inserted into store pages.
 *
 * @class        WC_Ghostmonitor
 * @extends      WC_Integration
 */

defined( 'ABSPATH' ) or die();

class WC_Ghostmonitor extends WC_Integration {
	private $plugin_config;
	private $gm_helper;
	private $ghostmonitor_id;
	private $ghostmonitor_domain_name;
	private $server_side_requests;
	private $db_table_name;

	public function __construct() {
		// WooCommerce integration settings
		$this->method_description = __(
			'Paste your Site ID here, which can be found in your GhostMonitor <a href="https://app.ghostmonitor.com/#/plugins">account</a>. If you don’t have a GhostMonitor account sign up <a href="https://ghostmonitor.com">free</a> here.',
			'woocommerce'
		);

		$this->method_title = __( 'Ghostmonitor', 'woocommerce' );
		$this->id           = 'wc_ghostmonitor';

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Load config.json
		$config_file_path    = GHOSTMONITOR_PLUGIN_PATH . 'config.json';
		$this->plugin_config = file_exists( $config_file_path ) ? json_decode( file_get_contents( $config_file_path ) ) : false;

		// Define user set variables
		$this->ghostmonitor_domain_name = $this->get_gm_config_value( 'ghostmonitor_domain_name' ) ?: $this->get_option( 'ghostmonitor_domain_name' );
		$this->ghostmonitor_id          = $this->get_gm_config_value( 'ghostmonitor_id' ) ?: $this->get_option( 'ghostmonitor_id' );

		// Initialize helper
		$this->gm_helper = new Ghostmonitor\Helper(
			$this->ghostmonitor_id,
			$this->ghostmonitor_domain_name,
			$this->get_gm_config_value( 'trackingUrl' ),
			$this->get_gm_config_value( 'cdnUrl' )
		);
		$this->gm_helper->setLogPath( GHOSTMONITOR_PLUGIN_PATH . 'log.txt' );
		$this->gm_helper->setLogentriesToken( (string) $this->get_gm_config_value( 'logentriesToken' ) );

		$this->gm_helper->logDebug( array( 'PLUGIN_CONFIG' => $this->plugin_config ) );

		// Check database table
		$this->db_table_name = 'ghostmonitor_data';
		$this->check_database_changes();

		$this->server_side_requests = get_option( 'ghostmonitor_http_status' );

		// Actions
		add_action( 'woocommerce_update_options_integration_wc_ghostmonitor', array( $this, 'process_admin_options' ) );
		add_action( 'wp_loaded', array( $this, 'refill_cart' ) );

		// GM code to HTML
		add_action( 'wp_footer', array( $this, 'inject_to_html' ) );

		// Reorder checkout fields
		add_filter( 'woocommerce_checkout_fields', array( $this, 'reorder_checkout_fields' ) );

		// Add script for client side cart data sending
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_gm_script' ) );

		// wc-ajax is only available from WC 2.4
		if ( $this->woocommerce_version_check( '2.4.0' ) ) {
			// WooCommerce way
			add_action( 'wc_ajax_gm_send_cart_data', array( $this, 'send_cart_data_ajax' ) );
		} else {
			// Wordpress way
			add_action( 'wp_ajax_gm_send_cart_data', array( $this, 'send_cart_data_ajax' ) );
			add_action( 'wp_ajax_nopriv_gm_send_cart_data', array( $this, 'send_cart_data_ajax' ) );
		}

		if ( $this->server_side_requests ) {
			// Webhook for setCartData, setCartItem
			// add_action('woocommerce_after_calculate_totals', array($this, 'send_cart_data'));
			// add_action('woocommerce_add_to_cart', array($this, 'send_cart_data'));

			// Webhook for setConversion on server side
			add_action( 'woocommerce_order_status_processing', array( $this, 'send_conversion' ) );
			add_action( 'woocommerce_order_status_completed', array( $this, 'send_conversion' ) );
		}

		add_action( 'woocommerce_new_order', array( $this, 'save_gm_order_meta' ) );
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'ghostmonitor_id'          => array(
				'title'       => __( 'GhostMonitor Account', 'woocommerce' ),
				'description' => __( 'You can find your unique Site ID on the Implementation page inside of your GhostMonitor dashboard.', 'woocommerce' ),
				'type'        => 'text',
				'default'     => ''
			),
			'ghostmonitor_domain_name' => array(
				'title'       => __( 'Your Domain Name', 'woocommerce' ),
				'description' => __( 'This field is automatically generated for you.', 'woocommerce' ),
				'type'        => 'text',
				'default'     => '',
				'disabled'    => true
			),
			'ghostmonitor_email_first' => array(
				'title'       => __( 'Email Field First', 'woocommerce' ),
				'description' => __( 'If checked, email field will be the first at the checkout page.', 'woocommerce' ),
				'type'        => 'checkbox',
				'default'     => 'no'
			)
		);
	}

	/**
	 * Validate the ghostmonitor_id field
	 * @see validate_settings_fields()
	 */
	public function validate_ghostmonitor_id_field( $key ) {
		$this->gm_helper->logDebug( 'validate_ghostmonitor_id_field()' );
		// get the posted value
		$value                 = $_POST[ $this->plugin_id . $this->id . '_' . $key ];
		$this->ghostmonitor_id = $value;

		if ( strlen( $value ) < 1 ) {
			$this->errors[] = 'Ghostmonitor Account must be set!';
			$this->gm_helper->logError( 'validate_ghostmonitor_id_field() validation error' );
		}

		return $value;
	}

	/**
	 * Get the domain name by ghostmonitor_id and validate
	 * @see validate_settings_fields()
	 */
	public function validate_ghostmonitor_domain_name_field( $key ) {
		$this->gm_helper->logDebug( 'validate_ghostmonitor_domain_name_field()' );
		$response = wp_remote_get( "{$this->get_gm_config_value('sitesUrl', 'https://sites.ghostmonitor.com/')}getdomain?id={$this->ghostmonitor_id}", array( 'httpversion' => '1.1' ) );
		$domain   = '';

		if ( is_array( $response ) ) {
			$body   = $response['body'];
			$json   = json_decode( $body );
			$domain = is_object( $json ) && isset( $json->domain ) ? $json->domain : '';
			if ( $domain === '' ) {
				switch ( $code = $response['response']['code'] ) {
					case '400':
						$json           = json_decode( $body );
						$this->errors[] = $json->status . ' ' . $json->msg;
						break;
					default:
						$this->errors[] = $response['response']['code'] . ' ' . $response['response']['message'];
						break;
				}
			}
		} elseif ( is_wp_error( $response ) ) {
			$this->gm_helper->logError( $response->get_error_message() );
			$this->errors[] = $response->get_error_message();
			$this->gm_helper->logError( array(
				'validate_ghostmonitor_domain_name_field() validation error',
				$this->errors
			) );
		}

		return $domain;
	}

	/**
	 * Display errors by overriding the display_errors() method
	 * @see display_errors()
	 */
	public function display_errors() {
		foreach ( $this->errors as $error ) {
			print_r( "<div class='error'><p>" . $error . "</p></div>" );
		}
	}

	public function refill_cart() {
		$gm_cart = isset( $_GET['gm_cart'] ) ? $_GET['gm_cart'] : false;

		$this->gm_helper->logDebug( array( 'ACTION STARTED: wp_loaded', 'refill_cart()', 'GET_PARAMS: ', $_GET ) );

		if ( $gm_cart === false ) {
			return false;
		}

		$cart_data = $this->get_gm_data( $gm_cart );

		$this->gm_helper->logDebug( array( 'CART DATA' => $cart_data ) );

		if ( $cart_data === false ) {
			return false;
		}

		global $woocommerce;

		$woocommerce->cart->empty_cart();

		foreach ( $cart_data['setCartItem'] as $item ) {
			if ( array_key_exists( 'productAttributeId', $item ) ) {
				$variation = $item['productAttributeId'];
				$woocommerce->cart->add_to_cart( $item['productId'], $item['qty'], $variation['variation_id'], $variation['variation'] );
			} else {
				$woocommerce->cart->add_to_cart( $item['productId'], $item['qty'] );
			}
		}

		if ( isset( $_GET['ghostmonitor_session_id'] ) ) {
			$validation = $this->gm_helper->validateDiscount( $_GET['ghostmonitor_session_id'] );
		} else {
			$validation = array( 'valid' => false );
		}

		if ( $validation['valid'] && get_option( 'woocommerce_enable_coupons' ) === 'yes' ) {

			$coupon = new WC_Coupon( $validation['discount_code'] );

			if ( ! $coupon->is_valid() ) {
				# Add discount
				$coupon = array(
					'post_title'   => $validation['discount_code'],
					'post_type'    => 'shop_coupon',
					'post_status'  => 'publish',
					'post_author'  => 1,
					'post_excerpt' => $validation['discount_name'],
				);

				$new_coupon_id = wp_insert_post( $coupon );

				// Add meta
				update_post_meta( $new_coupon_id, 'discount_type', $validation['discount_type'] );
				update_post_meta( $new_coupon_id, 'coupon_amount', $validation['amount'] );
				update_post_meta( $new_coupon_id, 'individual_use', 'no' );
				update_post_meta( $new_coupon_id, 'product_ids', '' );
				update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
				update_post_meta( $new_coupon_id, 'usage_limit', '1' );
				update_post_meta( $new_coupon_id, 'expiry_date', date( 'Y-m-d', strtotime( '+14 days' ) ) );
				update_post_meta( $new_coupon_id, 'apply_before_tax', 'no' );
				update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
			}

			$woocommerce->cart->add_discount( sanitize_text_field( $validation['discount_code'] ) );
		}

		$cart_url = $woocommerce->cart->get_checkout_url();

		unset( $_GET['gm_cart'] );

		$parameters = array_map( function ( $key, $value ) {
			return $key . '=' . $value;
		}, array_keys( $_GET ), array_values( $_GET ) );

		$redirect_url = $cart_url . '?' . implode( '&', $parameters );

		$this->gm_helper->logDebug( 'REDIRECT URL: ' . $redirect_url );

		wp_redirect( $redirect_url );
		exit;
	}

	public function save_gm_order_meta( $order_id ) {
		$gm_order_meta = get_post_meta( $order_id, '_gm_params', true );
		if ( '' !== $gm_order_meta ) {
			return true;
		}

		$conversion_data = $this->gm_helper->getConversionData();
		if ( false === $conversion_data ) {
			return false;
		}

		update_post_meta( $order_id, '_gm_params', $conversion_data );

		return true;
	}

	public function send_conversion( $order_id ) {
		$gm_session_data = get_post_meta( $order_id, '_gm_params', true );
		if ( ! $gm_session_data ) {
			return false;
		}
		$ghostmonitor_data = $this->collect_ghostmonitor_data_from_order( $order_id, $gm_session_data['session_id'] );
		$this->gm_helper->logDebug( array(
			'ACTION STARTED: woocommerce_order_status_processing',
			'GM SESSION DATA',
			$gm_session_data
		) );
		$this->gm_helper->sendGhostData( $ghostmonitor_data );
		$this->gm_helper->sendConversionData( $gm_session_data );
	}

	public function send_cart_data() {
		$this->gm_helper->logDebug( 'ACTION STARTED: woocommerce_after_calculate_totals or woocommerce_add_to_cart --> send_cart_data()' );

		$ghostmonitorData = $this->collect_ghostmonitor_data();

		$this->gm_helper->sendGhostData( $ghostmonitorData, 2 );
	}

	public function inject_to_html() {
		$this->ghostmonitor_tracking_code();
		$this->client_side_cart_tracking();
	}

	public function client_side_cart_tracking() {
		$is_thankyou = (bool) did_action( 'woocommerce_thankyou' );

		if ( $is_thankyou ) {
			echo '<script type="application/javascript">_ghostmonitor.push([\'setConversion\']);</script>';

			return true;
		}

		$ghostmonitorData = $this->collect_ghostmonitor_data();

		echo $this->gm_helper->getInlineGMScripts( $ghostmonitorData );

		return true;
	}

	private function ghostmonitor_tracking_code() {
		$this->gm_helper->logDebug( 'ACTION STARTED: wp_footer --> inject ghostmonitor init script into the html code' );
		global $woocommerce;
		global $wp_version;

		$is_cart_empty = WC()->cart->cart_contents_count > 0 ? false : true;

		$debug_lines = '';
		if ( $this->gm_helper->isLoggingEnabled() ) {
			$debug_lines .= '<!--http-stat:' . ( $this->gm_helper->testHTTP() ? 'ok' : 'failed' ) . '-->';
		}

		$additional_lines = '
            <!--ghostmonitor-version:' . $this->get_gm_config_value( 'version', 'ERROR LOADING CONFIG' ) . '-->
            <!--wp:' . $wp_version . '-->
            <!--wc:' . $woocommerce->version . '-->
        ' . $debug_lines;

		echo $this->gm_helper->ghost_init( $is_cart_empty, $additional_lines );
	}

	private function collect_ghostmonitor_data_from_order( $order_id, $session_id ) {
		$order = new WC_Order_Factory();
		$order = $order->get_order( $order_id );

		$order_items = $order->get_items();

		$cart_items       = array();
		$cart_items_count = 0;
		foreach ( $order_items as $i ) {
			$product = new WC_Product_Factory();
			$product = $product->get_product( $i['variation_id'] == '0' ? $i['product_id'] : $i['variation_id'] );

			$thumb_id  = get_post_thumbnail_id( $product->post->ID );
			$thumb_url = wp_get_attachment_image_src( $thumb_id, 'shop_thumbnail', true );

			if ( ! empty( $thumb_url[0] ) ) {
				$image = $thumb_url[0];
			} else {
				$image = wc_placeholder_img( 'shop_thumbnail' );
			}

			$cart_items[] = array(
				'session_id' => $session_id,
				'productId'  => $i['product_id'] . ( $i['variation_id'] == '0' ? '' : '-' . $i['variation_id'] ),
				'qtyPrice'   => $this->format_money( $i['line_total'] ),
				'imageUrl'   => $this->format_image_url( $image ),
				'price'      => $this->format_money( $i['line_total'] / $i['qty'] ),
				'name'       => $i['name'],
				'qty'        => $i['qty'],
			);

			$cart_items_count += $i['qty'];
		}

		$shipping = array(
			'session_id' => $session_id,
			'productId'  => $order->get_shipping_method(),
			'qtyPrice'   => $order->get_total_shipping(),
			'imageUrl'   => 'http://shipping.ghostmonitor.com/shipping.png',
			'price'      => $order->get_total_shipping(),
			'name'       => 'Shipping',
			'qty'        => 1,
		);

		$cart_items[] = $shipping;

		$return_url = add_query_arg( 'gm_cart', $session_id, wc_get_checkout_url() );
		$cart_data  = array(
			'returnUrl'  => $return_url,
			'session_id' => $session_id,
			'value'      => $this->format_money( $order->get_total() ),
			'itemCount'  => $cart_items_count,
			'email'      => $order->billing_email,
		);

		$ghostmonitor_data = array(
			'setCartData' => $cart_data,
			'setCartItem' => $cart_items,
		);

		$this->gm_helper->logDebug( array( 'GHOSTMONITOR DATA' => $ghostmonitor_data ) );

		$ghostmonitor_data['site_id']    = $this->ghostmonitor_id;
		$ghostmonitor_data['session_id'] = $session_id;

		return $ghostmonitor_data;
	}

	private function collect_ghostmonitor_data() {
		$setCartItem   = array();
		$gm_session_id = $this->gm_helper->setGhostmonitorSessionId();

		if ( $gm_session_id === false ) {
			$this->gm_helper->log( 'Could not find ghostmonitor_session_id cookie in send_cart_data()', 'warning' );

			return false;
		}

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item_data ) {

			$gm_cart_item = array();

			$_product = new WC_Product_Factory();
			$_product = $_product->get_product( $cart_item_data['product_id'] );

			if ( $_product && $_product->exists() && $cart_item_data['quantity'] > 0 ) {
				$thumb_id  = get_post_thumbnail_id( $_product->post->ID );
				$thumb_url = wp_get_attachment_image_src( $thumb_id, 'shop_thumbnail', true );

				if ( ! empty( $thumb_url[0] ) ) {
					$image = $thumb_url[0];
				} else {
					$image = wc_placeholder_img( 'shop_thumbnail' );
				}

				$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item_data, $cart_item_key );
				$product_name = html_entity_decode( $product_name );

				$gm_cart_item['session_id'] = $gm_session_id;
				$gm_cart_item['qtyPrice']   = $this->format_money( $_product->get_price() * $cart_item_data['quantity'] );
				$gm_cart_item['imageUrl']   = $this->format_image_url( $image );
				$gm_cart_item['price']      = $this->format_money( $_product->get_price() );
				$gm_cart_item['name']       = $product_name;
				$gm_cart_item['qty']        = $cart_item_data['quantity'];

				if ( isset( $cart_item_data['variation_id'] ) && $cart_item_data['variation_id'] != '' ) {
					$variation_data                 = array();
					$variation_data['variation_id'] = $cart_item_data['variation_id'];

					if ( array_key_exists( 'variation', $cart_item_data ) ) {
						$variation_data['variation'] = $cart_item_data['variation'];
					}

					$gm_cart_item['productId']          = $_product->post->ID . '-' . $variation_data['variation_id'];
					$gm_cart_item['productAttributeId'] = $variation_data;
				} else {
					$gm_cart_item['productId'] = $_product->post->ID;
				}
			}

			$setCartItem[] = $gm_cart_item;
		}

		$return_url  = add_query_arg( 'gm_cart', $gm_session_id, WC()->cart->get_checkout_url() );
		$setCartData = array(
			'returnUrl'  => $return_url,
			'session_id' => $gm_session_id,
			'value'      => $this->format_money( WC()->cart->cart_contents_total + WC()->cart->tax_total ),
			'itemCount'  => WC()->cart->cart_contents_count,
		);

		if ( is_user_logged_in() ) {
			$user                 = wp_get_current_user();
			$setCartData['email'] = $user->user_email;
		}

		$ghostmonitorData = array(
			'setCartData' => $setCartData,
			'setCartItem' => $setCartItem,
		);

		$this->gm_helper->logDebug( array( 'GHOSTMONITOR DATA' => $ghostmonitorData ) );

		$this->set_gm_data( $gm_session_id, $ghostmonitorData );

		$ghostmonitorData['site_id']    = $this->ghostmonitor_id;
		$ghostmonitorData['session_id'] = $gm_session_id;

		return $ghostmonitorData;
	}

	public function reorder_checkout_fields( $fields ) {
		if ( empty( $fields['billing']['billing_email']['class'] ) || $this->get_option( 'ghostmonitor_email_first' ) !== 'yes' ) {
			return $fields;
		}

		// if email is in a row at the checkout page set email's and the other element's class to form-row-wide
		$billing_keys        = array_keys( $fields['billing'] );
		$billing_email_index = array_search( 'billing_email', $billing_keys );

		if ( ( $email_class_index = array_search( 'form-row-first', $fields['billing']['billing_email']['class'] ) ) !== false ) {
			$fields['billing']['billing_email']['class'][ $email_class_index ] = 'form-row-wide';
			$after_email_field                                                 = $billing_keys[ $billing_email_index + 1 ];
			if ( ( $after_email_class_index = array_search( 'form-row-last', $fields['billing'][ $after_email_field ]['class'] ) ) !== false ) {
				$fields['billing'][ $after_email_field ]['class'][ $after_email_class_index ] = 'form-row-wide';
			}
		} elseif ( ( $email_class_index = array_search( 'form-row-last', $fields['billing']['billing_email']['class'] ) ) !== false ) {
			$fields['billing']['billing_email']['class'][ $email_class_index ] = 'form-row-wide';
			$before_email_field                                                = $billing_keys[ $billing_email_index - 1 ];
			if ( ( $before_email_class_index = array_search( 'form-row-first', $fields['billing'][ $before_email_field ]['class'] ) ) !== false ) {
				$fields['billing'][ $before_email_field ]['class'][ $before_email_class_index ] = 'form-row-wide';
			}
		}

		// move the email field to the first place in the array with union
		$fields['billing'] = array( 'billing_email' => $fields['billing']['billing_email'] ) + $fields['billing'];

		return $fields;
	}

	public function enqueue_gm_script() {
		wp_enqueue_script( 'ghostmonitor_push', GHOSTMONITOR_PLUGIN_URL . 'assets/js/ghostmonitor_push.js', array( 'jquery' ) );
		wp_localize_script( 'ghostmonitor_push', 'GhostMonitorAjax', array(
				'ajax_url' => $this->woocommerce_version_check( '2.4.0' ) ? WC_AJAX::get_endpoint( 'gm_send_cart_data' ) : admin_url( 'admin-ajax.php' )
			)
		);
	}

	public function send_cart_data_ajax() {
		wp_send_json( $this->collect_ghostmonitor_data() );
	}

	private function set_gm_data( $session_id, $ghostmonitor_data ) {
		global $wpdb;
		$wpdb->hide_errors();
		$ghostmonitor_data = serialize( $ghostmonitor_data );

		$table_name = $wpdb->prefix . $this->db_table_name;

		$sql = "INSERT INTO `$table_name` (`session_id`, `data`, `time`)
                VALUES (%s, %s, %d)
                ON DUPLICATE KEY UPDATE
                `data` = %s,
                `time` = %d";

		$prep   = $wpdb->prepare( $sql, $session_id, $ghostmonitor_data, current_time( 'timestamp' ), $ghostmonitor_data, current_time( 'timestamp' ) );
		$result = $wpdb->query( $prep );
		if ( false === $result ) {
			$this->gm_helper->logError( 'Error inserting GM data to database' );
		}

		return $result;
	}

	private function get_gm_data( $session_id ) {
		global $wpdb;
		$wpdb->hide_errors();

		$table_name = $wpdb->prefix . $this->db_table_name;

		$sql = "SELECT `data` FROM `$table_name` WHERE `session_id` = %s";

		$prep = $wpdb->prepare( $sql, $session_id );

		$result = $wpdb->get_var( $prep );

		if ( $result === null ) {
			$this->gm_helper->logError( 'Could not find GM data in database with session_id ' . $session_id );

			return false;
		}

		return unserialize( $result );
	}

	private function check_database_changes() {
		$this->gm_helper->logDebug( 'Check database table...' );
		if ( get_option( 'ghostmonitor_db_version' ) ) {
			return true;
		}

		$this->gm_helper->logDebug( 'CREATE DATABASE TABLE' );

		$this->create_database_table_if_not_exists();
		$this->move_old_sessions();

		update_option( 'ghostmonitor_db_version', $this->get_gm_config_value( 'version' ), true );

		$config_file_path = plugin_dir_path( __FILE__ ) . '../config.json';
		if ( file_exists( $config_file_path ) ) {
			file_put_contents( $config_file_path, json_encode( $this->plugin_config ) );
		}
	}

	private function create_database_table_if_not_exists() {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->db_table_name;

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
            `session_id` varchar(255) NOT NULL PRIMARY KEY,
            `data` text NOT NULL,
            `time` int NOT NULL
        ) ENGINE='InnoDB' CHARSET='utf8'";

		$result = $wpdb->query( $sql );

		if ( false === $result ) {
			$this->gm_helper->logError( 'Error creating GM database' );
		}

		return $result;
	}

	private function move_old_sessions() {
		global $wpdb;

		$sql = "SELECT * FROM $wpdb->options WHERE `option_name` LIKE '_transient_gmcart_%'";

		$gm_sessions = $wpdb->get_results( $sql );

		foreach ( $gm_sessions as $session ) {
			$this->set_gm_data( str_replace( '_transient_gmcart_', '', $session->option_name ), maybe_unserialize( $session->option_value ) );
			$wpdb->delete( $wpdb->options, array( 'option_name' => $session->option_name ) );
		}
	}

	private function format_money( $number ) {
		return number_format( (float) $number, 2, '.', '' );
	}

	private function format_image_url( $url ) {
		if ( stripos( $url, 'http' ) === false ) {
			$url = get_site_url() . $url;
		}

		return $url;
	}

	private function woocommerce_version_check( $version = '2.3.3' ) {
		global $woocommerce;

		$this->gm_helper->logError( 'WOOCOMMERCE VERSION: ' . $woocommerce->version );

		if ( version_compare( $woocommerce->version, $version, ">=" ) ) {
			return true;
		}

		return false;
	}

	private function get_gm_config_value( $key, $default = false ) {
		if ( isset( $this->plugin_config ) && $this->plugin_config && property_exists( $this->plugin_config, $key ) ) {
			return $this->plugin_config->{$key};
		}

		return $default;
	}
}
