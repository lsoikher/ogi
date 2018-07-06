<?php 


class WC_Autoship_Paypal_Gateway extends WC_Payment_Gateway_CC {
	
	protected $_sandbox_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
	protected $_endpoint = 'https://api-3t.paypal.com/nvp';
	protected $_sandbox_paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	protected $_paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
	protected $_title;
	protected $_user;
	protected $_password;
	protected $_signature;
	protected $_authorize_only;
	protected $_sandbox_mode;
	
	public function __construct() {
		// WooCommerce fields
		$this->id = 'wc_autoship_paypal';
		$this->icon = '';
		$this->order_button_text = __( 'Checkout with PayPal', 'wc-autoship' );
		$this->has_fields = true;
		$this->method_title = __( "WC Autoship PayPal Payments", 'wc-autoship' );
		$this->method_description = __( 
			"PayPal payment gateway for WooCommerce and WC Autoship",
			'wc-autoship'
		);
		$this->description = $this->method_description;
		$this->notify_url = admin_url( '/admin-ajax.php?action=wc_autoship_paypal_payments_ipn_callback' );
		// WooCommerce settings
		$this->init_form_fields();
		$this->init_settings();
		// Assign settings
		$this->title = $this->get_option( 'title' );
		$this->_user = $this->get_option( 'user' );
		$this->_password = $this->get_option( 'password' );
		$this->_signature = $this->get_option( 'signature' );
		$this->_authorize_only = $this->get_option( 'authorize_only' );
		$this->_sandbox_mode = $this->get_option( 'sandbox_mode' );
		// Supports
		$this->supports = array(
			'refunds',
			'tokenization'
		);
		// Payment gateway hooks
		add_action( 
			'woocommerce_update_options_payment_gateways_' . $this->id, 
			array( $this, 'process_admin_options' )
		);
	}
	
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title' => __( 'Enable/Disable', 'wc-autoship' ),
				'type' => 'checkbox',
				'label' => __( 'Enable ' . $this->method_title, 'wc-autoship' ),
				'default' => 'yes'
			),
			'title' => array(
				'title' => __( 'Title', 'wc-autoship' ),
				'type' => 'text',
				'description' => __( 
					'This controls the title which the user sees during checkout.', 'wc-autoship'
				),
				'default' => __( 'PayPal', 'wc-autoship' ),
				'desc_tip' => true,
			),
			'license_key' => array(
				'title' => __( 'License Key', 'wc-autoship' ),
				'description' => __( 'Enter your software license key issued after purchase.', 'wc-autoship' ),
				'desc_tip' => true,
				'type' => 'text'
			),
			'user' => array(
				'title' => __( 'User', 'wc-autoship' ),
				'description' => __( 'PayPal user', 'wc-autoship' ),
				'desc_tip' => true,
				'type' => 'text'
			),
			'password' => array(
				'title' => __( 'Password', 'wc-autoship' ),
				'description' => __( 'PayPal password', 'wc-autoship' ),
				'desc_tip' => true,
				'type' => 'password'
			),
			'signature' => array(
				'title' => __( 'Signature', 'wc-autoship' ),
				'description' => __( 'PayPal API Signature', 'wc-autoship' ),
				'desc_tip' => true,
				'type' => 'text'
			),
			'sandbox_mode' => array(
				'title' => __( 'Sandbox Mode', 'wc-autoship' ),
				'description' => __( 'Enable sandbox mode', 'wc-autoship' ),
				'desc_tip' => __( 
					'Select this option to send transactions to the test gateway.',
					'wc-autoship'
				),
				'type' => 'checkbox',
				'default' => 'yes'
			)
		);
	}
	
	public function payment_fields() {
		$customer_id = get_current_user_id();
		$payment_tokens = WC_Payment_Tokens::get_customer_tokens( $customer_id, $this->id );
		include dirname( dirname( __FILE__ ) ) . '/templates/payment-fields.php';
	}
	
	public function process_payment( $order_id ) {
		$woocommerce = WC();
		
		// Get order
		$order = wc_get_order( $order_id );
		$info = get_post_meta( $order_id );
		// Get customer
		// THIS IS WHERE WE DECIDE WHGT METHOD TO USE, DONT USE BILLING AGREEMENT
		$customer_id = $order->get_user_id();

		// Get totals
		$total = $order->get_total();
		$total_shipping = $order->get_total_shipping();
		$total_tax = $order->get_total_tax();

		$precision = get_option( 'woocommerce_price_num_decimals' );

		// Add line items
		$items = $order->get_items();
		$line_item_names = array();
		foreach ( $items as $item ) {
			$line_item_names[] = $item['name'] . ' x ' . $item['qty'];
		}
		$total_discount = $order->get_total_discount();
		if ( $total_discount > 0.0 ) {
			$line_item_names[] = 'Discount(' . number_format( $total_discount, $precision ) . ') x 1';
		}
		// Item total
		$item_total = $total - $total_shipping - $total_tax;

		// Initialize data
		$data = array();

		if ( empty( $_POST['wc-wc_autoship_paypal-payment-token'] ) || $_POST['wc-wc_autoship_paypal-payment-token'] == 'new' ) {
			// This is a new payment method

			$data['METHOD'] = 'SetExpressCheckout';
			$data['SOLUTIONTYPE'] = 'Sole';
			$data['RETURNURL'] = add_query_arg( array(
				'do' => 'payment',
				'order_id' => $order_id,
				'order_key' => $order->order_key
			), $this->notify_url );
			$data['CANCELURL'] = wc_get_cart_url();
			// See if they are a user, and if so set this field, otherwise dont
			if ( ! empty( $customer_id ) && $customer_id > 0 && wc_autoship_cart_has_autoship_items() ) {
				$data['BILLINGTYPE'] = 'MerchantInitiatedBilling'; // Initiate billing agreement
			}
			// Sale
			$data['PAYMENTREQUEST_0_INVNUM'] = $order->get_order_number();
			$data['PAYMENTREQUEST_0_AMT'] = round( $total, $precision );
			$data['PAYMENTREQUEST_0_PAYMENTACTION'] = 'SALE';
			$data['PAYMENTREQUEST_0_CURRENCYCODE'] = get_woocommerce_currency();
			$data['PAYMENTREQUEST_0_DESC'] = get_bloginfo('name') . __( ' Checkout', 'wc-autoship' );
			// Items
			$data['L_PAYMENTREQUEST_0_NAME0'] = implode( ', ', $line_item_names );
			$data['L_PAYMENTREQUEST_0_AMT0'] = round( $item_total, $precision );
			$data['L_PAYMENTREQUEST_0_QTY0'] = '1';
			$data['PAYMENTREQUEST_0_ITEMAMT'] = round( $item_total, $precision );
			// Shipping
			$data['PAYMENTREQUEST_0_SHIPPINGAMT'] = round( $total_shipping, $precision );
			// Taxes
			$data['PAYMENTREQUEST_0_TAXAMT'] = round( $total_tax, $precision );
			// Customer info
			$data['ADDROVERRIDE'] = '1';
			$data['PAYMENTREQUEST_0_SHIPTONAME'] = sprintf( '%s %s', get_post_meta( $order_id, '_shipping_first_name', true ), get_post_meta( $order_id, '_shipping_last_name', true ) );
			$data['PAYMENTREQUEST_0_SHIPTOSTREET'] = get_post_meta( $order_id, '_shipping_address_1', true );
			$data['PAYMENTREQUEST_0_SHIPTOSTREET2'] = get_post_meta( $order_id, '_shipping_address_2', true );
			$data['PAYMENTREQUEST_0_SHIPTOCITY'] = get_post_meta( $order_id, '_shipping_city', true );
			$data['PAYMENTREQUEST_0_SHIPTOSTATE'] = get_post_meta( $order_id, '_shipping_state', true );
			$data['PAYMENTREQUEST_0_SHIPTOZIP'] = get_post_meta( $order_id, '_shipping_postcode', true );
			$data['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'] = get_post_meta( $order_id, '_shipping_country', true );

			// Do express checkout
			$result = $this->send_request( $data, 'SetExpressCheckout' );
			if ($result['ACK'] == 'Success') {
				// Return redirect to PayPal
				return array(
					'result' => 'success',
					'redirect' => $this->get_paypal_url() . '?cmd=_express-checkout&useraction=commit&token=' . $result['TOKEN']
				);
			} else {
				// Payment error
				wc_add_notice(
					__( 'Error: ', 'wc-autoship' ) . $result['L_LONGMESSAGE0'],
					'error'
				);
				return;
			}

		} else {
			// This is a reference transaction

			$data['METHOD'] = 'DoReferenceTransaction';
			// Sale
			$data['PAYMENTACTION'] = 'SALE';
			$payment_token_id = $_POST['wc-wc_autoship_paypal-payment-token'];
			$payment_token = WC_Payment_Tokens::get( $payment_token_id );
			$data['REFERENCEID'] = $payment_token->get_token();
			$data['CURRENCYCODE'] = get_woocommerce_currency();
			$data['DESC'] = get_bloginfo('name') . __( ' Autoship', 'wc-autoship' );
			$data['AMT'] = round( $total, $precision );
			$data['INVNUM'] = $order->get_order_number();
			// Items
			$data['L_NAME0'] = implode( ', ', $line_item_names );
			$data['L_AMT0'] = round( $item_total, $precision );
			$data['L_QTY0'] = '1';
			$data['ITEMAMT'] = round( $item_total, $precision );
			// Shipping
			$data['SHIPPINGAMT'] = round( $total_shipping, $precision );
			// Taxes
			$data['TAXAMT'] = round( $total_tax, $precision );

			// Do reference transaction
			$result = $this->send_request( $data, 'DoReferenceTransaction' );
			if ($result['ACK'] == 'Success') {
				$order->payment_complete( $result['TRANSACTIONID'] );
				$order->add_payment_token( $payment_token );
				// Return redirect to PayPal
				return array(
					'result' => 'success',
					'redirect' => $this->get_return_url( $order )
				);
			} else {
				// Payment error
				wc_add_notice(
					__( 'Error: ', 'wc-autoship' ) . $result['L_LONGMESSAGE0'],
					'error'
				);
				return;
			}
		}
		
		return;
	}
	
	/**
	 * Process a refund for an order
	 * @see WC_Payment_Gateway::process_refund()
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		if ( $amount == null ) {
			return false;
		}
		
		// Do refund
		$data = array(
			'METHOD' => 'RefundTransaction',
			'TRANSACTIONID' => get_post_meta( $order_id, '_transaction_id', true ),
			'REFUNDTYPE' => 'Partial',
			'AMT' => $amount,
			'CURRENCYCODE' => get_post_meta( $order_id, '_order_currency', true )
		);
		if ( ! empty( $reason ) ) {
			$data['NOTE'] = $reason;
		}
		$result = $this->send_request( $data, 'RefundTransaction' );
		if ( $result['ACK'] == 'Success' ) {
			// Refund was successful
			return true;
		} else {
			$error = new WP_Error();
			$error->add( $result['L_ERRORCODE0'], $result['L_ERRORCODE0'] . ': ' . $result['L_LONGMESSAGE0'], $result );
			return $error;
		}
		
		return false;
	}
	
	public function validate_fields() {
		return true;
	}
	
	public function get_endpoint() {
		if ( $this->_sandbox_mode == 'yes' ) {
			return $this->_sandbox_endpoint;
		}
		return $this->_endpoint;
	}
	
	public function get_paypal_url() {
		if ( $this->_sandbox_mode == 'yes' ) {
			return $this->_sandbox_paypal_url;
		}
		return $this->_paypal_url;
	}
	
	
	/**
	 * Send a request
	 * @param string $data Data to be sent in the body payload not including auth params
	 * @param string $call_id Identifier for the API call to be used in logging
	 * @return array Response query string data
	 */
	public function send_request( $data, $call_id ) {
		$ch = curl_init( $this->get_endpoint() );
		if ( ! $ch ) {
			throw new Exception( 'Could not open connection' );
		}
		
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_POST, true );
		$default_data = array(
			'USER' => $this->_user,
			'PWD' => $this->_password,
			'SIGNATURE' => $this->_signature,
			'VERSION' => '116.0',
			'BUTTONSOURCE' => 'Patterns_SI_Custom'
		);
		$data = array_merge( $default_data, $data );
		$payload = http_build_query( $data );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$response = curl_exec( $ch );
		$result = array();
		parse_str( $response, $result );
		curl_close( $ch );
		if ( $result['ACK'] != 'Success' ) {
			// Log error
			$message = "WC_AUTOSHIP_PAYPAL_{$call_id}\n\n"
				. 'REQUEST: ' . http_build_query( $data ) . "\n\n"
				. 'RESPONSE: ' . $response . "\n\n";
			error_log( $message );
		}
		return $result;
	}
	
	public function api_callback() {
		if ( empty( $_GET['do'] ) ) {
			return;
		}
		$do = $_GET['do'];
		
		
		if ( $do == 'payment' && ! empty( $_GET['order_id'] ) && ! empty( $_GET['order_key'] ) ) {
			
			// Process payment
			$order = new WC_Order( $_GET['order_id'] );
			$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
			if ( $order->order_key == $_GET['order_key'] ) {
				// Get express checkout details
				$data = array(
					'METHOD' => 'GetExpressCheckoutDetails',
					'TOKEN' => $_GET['token'],
					'PAYERID' => $_GET['PayerID']
				);
				$details_result = $this->send_request( $data, 'GetExpressCheckoutDetails' );
				if ( $details_result['ACK'] == 'Success' ) {
					// Do payment
					$data = array(
						'METHOD' => 'DoExpressCheckoutPayment',
						'TOKEN' => $details_result['TOKEN'],
						'PAYERID' => $details_result['PAYERID'],
						'PAYMENTREQUEST_0_AMT' => $details_result['AMT'],
						'PAYMENTREQUEST_0_CURRENCYCODE' => get_woocommerce_currency(),
						'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE'
					);
					$payment_result = $this->send_request( $data, 'DoExpressCheckoutPayment' );
					if ( $payment_result['ACK'] == 'Success' ) {
						if ( ! empty( $payment_result['BILLINGAGREEMENTID'] ) ) {
							// Store billing agreement data
							$customer_id =  $order->get_user_id();
							$billing_agreement_id = $payment_result['BILLINGAGREEMENTID'];
							// Create token instance (WC_Token_CC), save with customer id using method of token
							$token = new WC_Payment_Token_CC();
							$token->set_user_id( $customer_id );
							$token->set_token( $billing_agreement_id );
							$token->set_gateway_id( $this->id );
							$token->set_card_type( 'paypal' );
							$token->set_last4( substr( $billing_agreement_id, -4 ) );
							$token->set_expiry_month( date( 'm' ) );
							$token->set_expiry_year( date( 'Y', strtotime( '+6 years' ) ) );
							$save_result = $token->save();
							if ( $save_result ) {
								$order->add_payment_token( $token );
							}
							add_post_meta( $order_id, 'BILLINGAGREEMENTID', $payment_result['BILLINGAGREEMENTID'] );
						}
						if ( ! empty( $payment_result['CORRELATIONID'] ) ) {
							add_post_meta( $order_id, 'CORRELATIONID', $payment_result['CORRELATIONID'] );
						}
						$order->payment_complete( $payment_result['PAYMENTINFO_0_TRANSACTIONID'] );
						header( 'Location: ' . $this->get_return_url( $order ) );
						die();

					}
				}
			}
			$order->update_status( 'failed', 'The order could not be completed with PayPal.' );
			header( 'Location: ' . $this->get_return_url( $order ) );
			die();
			
		} elseif ( $do == 'update_payment_method' && ! empty( $_GET['customer_id'] )
				&& ( $_GET['customer_id'] == get_current_user_id() ) || user_can( get_current_user_id(), 'manage_woocommerce' ) ) {
			
			// Get express checkout details
			$data = array(
				'METHOD' => 'GetExpressCheckoutDetails',
				'TOKEN' => $_GET['token'],
				'PAYERID' => $_GET['PayerID']
			);
			$details_result = $this->send_request( $data, 'GetExpressCheckoutDetails' );
			if ( $details_result['ACK'] == 'Success' ) {
				// Create billing agreement
				$data = array(
					'METHOD' => 'CreateBillingAgreement',
					'TOKEN' => $_GET['token']
				);
				$billing_result = $this->send_request( $data, 'CreateBillingAgreement' );
				if ( $billing_result['ACK'] == 'Success' ) {
					if ( ! empty( $billing_result['BILLINGAGREEMENTID'] ) ) {
						// Store billing agreement data
						$billing_agreement_id = $billing_result['BILLINGAGREEMENTID'];
						$token = new WC_Payment_Token_CC();
						$customer_id = get_current_user_id();
						$token->set_user_id( $customer_id );
						$token->set_token( $billing_agreement_id );
						$token->set_gateway_id( $this->id );
						$token->set_card_type( 'paypal' );
						$token->set_last4( substr( $billing_agreement_id, -4 ) );
						$token->set_expiry_month( date( 'm' ) );
						$token->set_expiry_year( date( 'Y', strtotime( '+1 year' ) ) );
						$save_result = $token->save();
						log( $save_result );
						// Notify
						wc_add_notice( __( 'PayPal payment method updated', 'wc-autoship' ), 'success' );
						// Redirect to account page
						wp_redirect( wc_get_account_endpoint_url( 'add-payment-method' ) );
						die();
					}
				}
			}
			
		}
		
		// Register error
		wc_add_notice(
			__( 'Unkown error processing PayPal payment ', 'wc-autoship' ),
			'error'
		);
		// Redirect to account page
		wp_redirect( wc_get_account_endpoint_url( '' ) );
		die();
	}

	public function add_payment_method() {
		// Intiate billing agreement
		$data = array(
			'METHOD' => 'SetExpressCheckout',
			'PAYMENTREQUEST_0_PAYMENTACTION' => 'AUTHORIZATION',
			'PAYMENTREQUEST_0_AMT' => '0',
			'PAYMENTREQUEST_0_CURRENCYCODE' => get_woocommerce_currency(),
			'L_BILLINGTYPE0' => 'MerchantInitiatedBilling',
			'L_BILLINGAGREEMENTDESCRIPTION0' => get_bloginfo('name') . __( ' Autoship', 'wc-autoship' ),
			'NOSHIPPING' => '1',
			'RETURNURL' => add_query_arg( array(
				'do' => 'update_payment_method',
				'customer_id' => get_current_user_id()
			), $this->notify_url ),
			'CANCELURL' => wc_get_account_endpoint_url( 'add-payment-method' )
		);
		$result = $this->send_request( $data, 'InitBillingAgreement' );
		if ( $result['ACK'] == 'Success' ) {
			return array(
				'result'   => 'success',
				'redirect' => $this->get_paypal_url() . '?cmd=_express-checkout&useraction=commit&token=' . $result['TOKEN']
			);
		}
	}
	
	public function process_admin_options() {
		parent::process_admin_options();
	
		if ( ! isset( $_POST['woocommerce_wc_autoship_paypal_license_key'] ) ) {
			return false;
		}
		$license = $_POST['woocommerce_wc_autoship_paypal_license_key'];
	
		$api_params = array(
			'edd_action' => 'activate_license',
			'license' => $license,
			'item_name' => urlencode( 'WC Autoship PayPal Payments' ),
			'url' => home_url()
		);
	
		$response = wp_remote_get( add_query_arg( $api_params, 'https://wooautoship.com' ), array(
			'timeout' => 15, 'sslverify' => false
		) );
	
		if ( is_wp_error( $response ) ) {
			WC_Admin_Settings::add_error( __( 'Error validating your license key!', 'wc-autoship' ) );
			return true;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		if ( $license_data->success ) {
			WC_Admin_Settings::add_message( __( 'Your license key is valid!', 'wc-autoship' ) );
		} elseif ( ! $license_data->success ) {
			if ( $license_data->error == 'expired' ) {
				WC_Admin_Settings::add_error( __( 'Your license key expired on ', 'wc-autoship' )
					. date( get_option( 'date_format' ), strtotime( $license_data->expires ) )
				);
			} else {
				WC_Admin_Settings::add_error( __( 'Your license key is invalid!', 'wc-autoship' ) );
			}
		}
		
		return true;
	}
}

?>