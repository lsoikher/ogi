<?php

require_once( 'Exception.php' );

class WC_Autoship_Pipey_Client {
	
	private $_cookie_file = '';
	private $_init = false;
	private $_semaphore = null;

	public function __construct() {
		$this->_cookie_file = tempnam( WC_AUTOSHIP_PIPEY_DIR, 'pipey-cookie' );
	}

	public function __destruct() {
		if ( file_exists( $this->_cookie_file ) ) {
			unlink( $this->_cookie_file );
		}
		$this->_release_semaphore();
	}

	private function _get_semaphore( $user_id ) {
		$suffix = (int) $user_id;
		$this->_semaphore = wc_autoship_get_cron_semaphore( 'pipey-semaphore-' . $suffix, 0, 60 );
		if ( empty ( $this->_semaphore ) ) {
			throw new WC_Autoship_Pipey_Exception( "Could not get semaphore" );
		}
	}

	private function _release_semaphore() {
		wc_autoship_release_cron_semaphore( $this->_semaphore );
	}

	public function login( $user_id ) {
		do_action( 'wc_autoship_pipey_before_login', $user_id );
		$this->_get_semaphore( $user_id );
		$data = apply_filters( 'wc_autoship_login_data', array( 'user_id' => $user_id ), $user_id );
		$this->_post( admin_url( '/admin-ajax.php?action=wc_autoship_pipey_set_user' ), $data );
		$this->empty_cart();
		do_action( 'wc_autoship_pipey_after_login', $user_id );
	}

	public function empty_cart() {
		do_action( 'wc_autoship_pipey_before_empty_cart' );
		$this->_get( admin_url( '/admin-ajax.php?action=wc_autoship_pipey_empty_cart' ) );
		do_action( 'wc_autoship_pipey_after_empty_cart' );
	}

	public function add_item( $product_id, $variation_id = 0, $quantity = 1, $autoship_frequency = 0 ) {
		do_action( 'wc_autoship_pipey_before_add_item', $product_id, $variation_id, $quantity, $autoship_frequency );
		$data = array(
			'add-to-cart' => $product_id,
			'quantity' => $quantity
		);
		if ( ! empty( $variation_id ) ) {
			$data['product_id'] = $product_id;
			$data['variation_id'] = $variation_id;
			$product = wc_get_product( $variation_id );
			if ( null != $product && 'variation' == $product->get_type() ) {
				$attributes = $product->get_variation_attributes();
				foreach ( $attributes as $name => $value ) {
					$data[$name] = $value;
				}
			}
		}
		if ( ! empty( $autoship_frequency ) ) {
			$data['wc_autoship_frequency'] = $autoship_frequency;
		}
		$data = apply_filters( 'wc_autoship_add_item_data',
			$data,
			$product_id,
			$variation_id,
			$quantity,
			$autoship_frequency
		);
		$response = $this->_post( $this->_cart_url( '?wc-ajax=add-to-cart' ), $data );
		if ( isset( $response['error'] ) ) {
			throw new WC_Autoship_Pipey_Exception( "Error adding item {$product_id}:{$variation_id} to cart" );
		}
		$this->_head( $this->_cart_url() );
		do_action( 'wc_autoship_pipey_after_add_item', $response, $product_id, $variation_id, $quantity, $autoship_frequency );
	}

	public function apply_coupon( $coupon_code ) {
		do_action( 'wc_autoship_pipey_before_apply_coupon', $coupon_code );
		$nonce_data = $this->_get( admin_url( '/admin-ajax.php?action=wc_autoship_pipey_create_apply_coupon_nonce' ) );
		$data = apply_filters( 'wc_autoship_apply_coupon_data',
			array(
				'coupon_code' => $coupon_code,
				'security' => $nonce_data['nonce']
			),
			$coupon_code
		);
		$response = $this->_post( $this->_cart_url( '?wc-ajax=apply_coupon' ), $data );
		$this->_head( $this->_cart_url() );
		do_action( 'wc_autoship_pipey_after_apply_coupon', $response, $coupon_code );
	}

	/**
	 * @param WC_Customer $customer
	 * @param string $shipping_method
	 * @param string $payment_gateway_id
	 * @param string $payment_token_id
	 * @return array|null
	 */
	public function checkout( $customer_id, $shipping_method, $payment_gateway_id, $payment_token_id ) {
		do_action( 'wc_autoship_pipey_before_checkout', $customer_id, $shipping_method, $payment_gateway_id, $payment_token_id );
		$this->_head( $this->_checkout_url() );
		$nonce_data = $this->_get( admin_url( '/admin-ajax.php?action=wc_autoship_pipey_create_woocommerce_process_checkout_nonce' ) );
		$order_data = array(
			'billing_first_name' => get_user_meta( $customer_id, 'billing_first_name', true ),
			'billing_last_name' => get_user_meta( $customer_id, 'billing_last_name', true ),
			'billing_company' => get_user_meta( $customer_id, 'billing_company', true ),
			'billing_email' => get_user_meta( $customer_id, 'billing_email', true ),
			'billing_phone' => get_user_meta( $customer_id, 'billing_phone', true ),
			'billing_country' => get_user_meta( $customer_id, 'billing_country', true ),
			'billing_address_1' => get_user_meta( $customer_id, 'billing_address_1', true ),
			'billing_address_2' => get_user_meta( $customer_id, 'billing_address_2', true ),
			'billing_city' => get_user_meta( $customer_id, 'billing_city', true ),
			'billing_state' => get_user_meta( $customer_id, 'billing_state', true ),
			'billing_postcode' => get_user_meta( $customer_id, 'billing_postcode', true ),
			'ship_to_different_address' => '1',
			'shipping_first_name' => get_user_meta( $customer_id, 'shipping_first_name', true ),
			'shipping_last_name' => get_user_meta( $customer_id, 'shipping_last_name', true ),
			'shipping_company' => get_user_meta( $customer_id, 'shipping_company', true ),
			'shipping_country' => get_user_meta( $customer_id, 'shipping_country', true ),
			'shipping_address_1' => get_user_meta( $customer_id, 'shipping_address_1', true ),
			'shipping_address_2' => get_user_meta( $customer_id, 'shipping_address_2', true ),
			'shipping_city' => get_user_meta( $customer_id, 'shipping_city', true ),
			'shipping_state' => get_user_meta( $customer_id, 'shipping_state', true ),
			'shipping_postcode' => get_user_meta( $customer_id, 'shipping_postcode', true ),
			'order_comments' => 'Autoship Order',
			'shipping_method[0]' => $shipping_method,
			'payment_method' => $payment_gateway_id,
			'wc-' . $payment_gateway_id . '-payment-token' => $payment_token_id,
			'terms' => 'yes',
			'_wpnonce' => $nonce_data['nonce'],
			'_wp_http_referer' => $nonce_data['referer']
		);
		// Get default country
		$default_country = wc_autoship_get_default_country();
		// Billing country
		$billing_country = get_user_meta( $customer_id, 'billing_country', true );
		$order_data['billing_country'] = ( ! empty( $billing_country ) ) ? $billing_country : $default_country;
		// Shipping country
		$shipping_country = get_user_meta( $customer_id, 'shipping_country', true );
		$order_data['shipping_country'] = ( ! empty( $shipping_country ) ) ? $shipping_country : $default_country;
		// Filter order data
		$order_data = apply_filters( 'wc_autoship_checkout_order_data', $order_data, $customer_id, $shipping_method, $payment_gateway_id, $payment_token_id );

		$headers = array(
			'Referer: ' . $nonce_data['referer']
		);
		$response = $this->_post( $this->_checkout_url( '?wc-ajax=checkout' ), $order_data, $headers );
		$this->empty_cart();
		do_action( 'wc_autoship_pipey_after_checkout', $response, $customer_id, $shipping_method, $payment_gateway_id, $payment_token_id );
		return $response;
	}

	public function get_cart() {
		return $this->_get( admin_url( '/admin-ajax.php?action=wc_autoship_pipey_get_cart' ) );
	}

	public function get_ip( $nonce ) {
		return $this->_post( admin_url( '/admin-ajax.php?action=wc_autoship_pipey_get_ip' ), array( 'nonce' => $nonce ) );
	}

	public function update_shipping_method( $shipping_method ) {
		do_action( 'wc_autoship_pipey_before_update_shipping_method', $shipping_method );
		$nonce_data = $this->_get( admin_url( '/admin-ajax.php?action=wc_autoship_pipey_create_update_shipping_method_nonce' ) );
		$data = apply_filters( 'wc_autoship_update_shipping_method_data',
			array(
				'shipping_method[0]' => $shipping_method,
				'security' => $nonce_data['nonce'],
				'_wpnonce' => $nonce_data['nonce'],
				'_wp_http_referer' => $nonce_data['referer']
			),
			$shipping_method
		);
		$response = $this->_post( $this->_cart_url( '?wc-ajax=update_shipping_method' ), $data, array( 'Referer: ' . $nonce_data['referer'] ) );
		$this->_get( $this->_cart_url() );
		do_action( 'wc_autoship_pipey_after_update_shipping_method', $response, $shipping_method );
	}

	private function _get( $url, $headers = array() ) {
		$curl = $this->_get_curl();
		curl_setopt( $curl, CURLOPT_URL, $url );
		return $this->_send( $curl, $headers );
	}

	private function _head( $url, $headers = array() ) {
		$curl = $this->_get_curl();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_NOBODY, true );
		return $this->_send( $curl, $headers );
	}

	private function _post( $url, $data, $headers = array() ) {
		$post_data = is_string( $data ) ? $data : http_build_query( $data );
		$curl = $this->_get_curl();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_data );
		return $this->_send( $curl, $headers );
	}

	private function _send( $curl, $headers = array() ) {
		$default_headers = array(
			'X-WC-Autoship-Pipey-Key: ' . base64_encode( wc_autoship_get_pipey_key() )
		);
		$headers = array_merge( $default_headers, $headers );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );

		$response = curl_exec( $curl );
		$response_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		do_action( 'wc_autoship_pipey_response', $response, $response_code, $curl );
		if ( false === $response || 200 != $response_code ) {
			$error = curl_error( $curl );
			if ( '' == $error ) {
				if ( 401 == $response_code ) {
					$error = 'Unauthorized';
				} elseif ( 403 == $response_code ) {
					$error = 'Forbidden';
				} else {
					$error = 'Unknown error';
				}
			}
			$exception = new WC_Autoship_Pipey_Exception( $error, $response_code );
			$exception->setResponse( $response );
			$exception->setUrl( curl_getinfo( $curl, CURLINFO_EFFECTIVE_URL ) );
			curl_close( $curl );
			// Log response
			$log_description = $error;
			$log_response = $response_code . ' ' . $response;
			wc_autoship_log_action( get_current_user_id(), 'request_failed', $log_description, $_SERVER['REQUEST_URI'], NULL, NULL, NULL, $log_response );
			// Throw exception
			throw $exception;
		}

		$content_type = curl_getinfo( $curl, CURLINFO_CONTENT_TYPE );
		curl_close( $curl );
		if ( false !== strpos( $content_type, 'application/json' ) || ( strlen( $response ) > 0 && $response[0] == '{' ) ) {
			return json_decode( $response, true );
		}

		return $response;
	}

	private function _get_curl() {
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HEADER, false );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt( $curl, CURLOPT_USERAGENT, WC_AUTOSHIP_PIPEY_USER_AGENT );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $curl, CURLOPT_MAXREDIRS, 2 );

		// Auth
		if ( strlen( WC_AUTOSHIP_PIPEY_AUTH ) > 0 ) {
			curl_setopt( $curl, CURLOPT_USERPWD, WC_AUTOSHIP_PIPEY_AUTH );
		} else {
			$pipey_username = get_option( 'wc_autoship_pipey_username' );
			$pipey_password = get_option( 'wc_autoship_pipey_password' );
			if ( ! empty( $pipey_username ) && ! empty( $pipey_password ) ) {
				$auth = "{$pipey_username}:{$pipey_password}";
				curl_setopt( $curl, CURLOPT_USERPWD, $auth );
			}
		}

		if ( ! $this->_init ) {
			curl_setopt( $curl, CURLOPT_COOKIESESSION, true );
			$this->_init = true;
		}
		curl_setopt( $curl, CURLOPT_COOKIEJAR, $this->_cookie_file );
		curl_setopt( $curl, CURLOPT_COOKIEFILE, $this->_cookie_file );
		return $curl;
	}

	private function _cart_url( $path = '' ) {
		return wc_get_cart_url() . $path;
	}

	private function _checkout_url( $path = '' ) {
		return wc_get_checkout_url() . $path;
	}

}