<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWSBS_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription_Order Class
 *
 * @class   YWSBS_Subscription_Order
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  Yithemes
 */
if ( !class_exists( 'YWSBS_Subscription_Order' ) ) {

    class YWSBS_Subscription_Order {

        /**
         * Single instance of the class
         *
         * @var \YWSBS_Subscription_Order
         */
        protected static $instance;

        public $post_type_name = 'ywsbs_subscription';

        public $subscription_meta = array();

        /**
         * Returns single instance of the class
         *
         * @return \YWSBS_Subscription_Order
         * @since 1.0.0
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function __construct() {

            add_action( 'woocommerce_create_order', array( $this, 'get_extra_subscription_meta'), 10, 2);


            // Add subscriptions from orders
            add_action('woocommerce_checkout_update_order_meta', array($this, 'check_order_for_subscription'), 100, 2);

            // Start subscription after payment received
            add_action('woocommerce_payment_complete', array($this, 'payment_complete'));
            add_action('woocommerce_order_status_completed', array($this, 'payment_complete'));
            add_action('woocommerce_order_status_processing', array($this, 'payment_complete'));

          // add_action( 'wp_loaded', array( $this, 'renew_order') , 30 );

        }

        public function get_extra_subscription_meta( $order_id, $checkout ) {

            //get discount cart
            if ( isset(WC()->cart) ) {
                $this->subscription_meta['cart_discount'] = WC()->cart->get_cart_discount_total();
                $this->subscription_meta['cart_discount_tax'] = WC()->cart->get_cart_discount_tax_total();
            }

            if ( isset($checkout->posted['payment_method']) && $checkout->posted['payment_method'] ) {

                $enabled_gateways = WC()->payment_gateways->get_available_payment_gateways();

                if ( isset( $enabled_gateways[ $checkout->posted['payment_method'] ] ) ) {
                    $payment_method = $enabled_gateways[ $checkout->posted['payment_method'] ];
                    $payment_method->validate_fields();
                    $this->subscription_meta['payment_method'] = $payment_method->id;
                    $this->subscription_meta['payment_method_title'] = $payment_method->get_title();
                }
            }

            $shippings = array();
            // Store shipping for all packages on this order (as this can differ between each order), WC 2.1
            if ( method_exists( WC()->shipping, 'get_packages' ) ) {
                $packages = WC()->shipping->get_packages();

                foreach ( $packages as $key => $package ) {

                    if ( isset( $package['rates'][ $checkout->shipping_methods[ $key ] ] ) ) {
                        $item_ids = array();
                        if ( isset( $package['contents'] ) ) {
                            foreach (  $package['contents'] as $cart_item_key => $cart_item ) {
                                if( YITH_WC_Subscription()->is_subscription( $cart_item['product_id'] ) ){
                                    $item_ids[] = $cart_item['product_id'];
                                }
                            }
                            if( !empty( $item_ids )){
                                $ship['method'] = $package['rates'][ $checkout->shipping_methods[ $key ] ];
                                $ship['ids'] = $item_ids;
                            }
                        }
                    }

                    $shippings[] = $ship;
                }

            }

            $this->subscription_meta['subscriptions_shippings'] = $shippings;

        }

        public function check_order_for_subscription( $order_id, $posted ) {

            $order = wc_get_order( $order_id );
            $order_items = $order->get_items();

            if( empty( $order_items)){
                return;
            }

            foreach ( $order_items as $key => $order_item  ) {

                $product_id = $order_item['product_id'];
                $variation_id = ( isset( $order_item['variation_id'] ) && !empty( $order_item['variation_id'] ) ) ? $order_item['variation_id'] : '';
                $shippings = $this->subscription_meta['subscriptions_shippings'];

                $shipping_method = '';
                if( YITH_WC_Subscription()->is_subscription( $product_id ) ){

                    foreach ( $shippings as $ship ) {
                        if( in_array( $product_id, $ship['ids'])){
                            $shipping_method[] = $ship;
                        }
                    }

                    $args = array(
                        'product_id'             => $order_item['product_id'],
                        'variation_id'           => $variation_id,
                        'product_name'           => $order_item['name'],

                        'user_id'                => get_post_meta( $order_id, '_customer_user', true ),

                        'customer_ip_address'    => get_post_meta( $order_id, '_customer_ip_address', true ),
                        'customer_user_agent'    => get_post_meta( $order_id, '_customer_user_agent', true ),

                        'quantity'               => $order_item['qty'],
                        'order_id'               => $order_id,
                        'order_ids'              => array($order_id),
                        'order_total'            => get_post_meta( $order_id, '_order_total', true ),
                        'order_currency'         => get_post_meta( $order_id, '_order_currency', true ),
                        'prices_include_tax'     => get_post_meta( $order_id, '_prices_include_tax', true ),

                        'payment_method'         => $this->subscription_meta['payment_method'],
                        'payment_method_title'   => $this->subscription_meta['payment_method_title'],
                        'subscriptions_shippings'=> $shipping_method,


                        'line_subtotal'          => $order_item['line_subtotal'],
                        'line_total'             => $order_item['line_total'],

                        'line_subtotal_tax'     => $order_item['line_subtotal_tax'],
                        'line_tax'              => $order_item['line_tax'],
                        'line_tax_data'          => $order_item['line_tax_data'],
                        'cart_discount'         => $this->subscription_meta['cart_discount'],
                        'cart_discount_tax'         => $this->subscription_meta['cart_discount_tax'],


                        'price_is_per'           => get_post_meta( $product_id, '_ywsbs_price_is_per', true ),
                        'price_time_option'      => get_post_meta( $product_id, '_ywsbs_price_time_option', true ),
                        'max_length'             => get_post_meta( $product_id, '_ywsbs_max_length', true ),


                        'billing_first_name'     => get_post_meta( $order_id, '_billing_first_name', true ),
                        'billing_last_name'      => get_post_meta( $order_id, '_billing_last_name', true ),
                        'billing_company'        => get_post_meta( $order_id, '_billing_company', true ),
                        'billing_address_1'      => get_post_meta( $order_id, '_billing_address_1', true ),
                        'billing_address_2'      => get_post_meta( $order_id, '_billing_address_2', true ),
                        'billing_city'           => get_post_meta( $order_id, '_billing_city', true ),
                        'billing_state'          => get_post_meta( $order_id, '_billing_state', true ),
                        'billing_postcode'       => get_post_meta( $order_id, '_billing_postcode', true ),
                        'billing_country'        => get_post_meta( $order_id, '_billing_country', true ),
                        'billing_email'          => get_post_meta( $order_id, '_billing_email', true ),
                        'billing_phone'          => get_post_meta( $order_id, '_billing_phone', true ),

                        'shipping_first_name'    => get_post_meta( $order_id, '_shipping_first_name', true ),
                        'shipping_last_name'     => get_post_meta( $order_id, '_shipping_last_name', true ),
                        'shipping_company'       => get_post_meta( $order_id, '_shipping_company', true ),
                        'shipping_address_1'     => get_post_meta( $order_id, '_shipping_address_1', true ),
                        'shipping_address_2'     => get_post_meta( $order_id, '_shipping_address_2', true ),
                        'shipping_city'          => get_post_meta( $order_id, '_shipping_city', true ),
                        'shipping_state'         => get_post_meta( $order_id, '_shipping_state', true ),
                        'shipping_postcode'      => get_post_meta( $order_id, '_shipping_postcode', true ),
                        'shipping_country'       => get_post_meta( $order_id, '_shipping_country', true ),


                    );


                    $subscription_id = YWSBS_Subscription()->add_subscription( $args );

                    $subscriptions = get_post_meta( $order_id, 'subscriptions', true);
                    if( $subscription_id ){
                        $subscriptions[] = $subscription_id;
                        update_post_meta( $order_id, 'subscriptions', $subscriptions );
                    }
                }
            }



        }

        public function payment_complete( $order_id  ) {

            $subscriptions = get_post_meta( $order_id, 'subscriptions', true);

            if( $subscriptions != '' ){
                foreach ( $subscriptions as $subscription  ) {
                    $renew_order = get_post_meta( $subscription, '_renew_order', true);

                    if( $renew_order!=0  && $renew_order == $order_id ){
                        YWSBS_Subscription()->update_subscription( $subscription, $order_id );
                    }elseif( $renew_order == 0 ){
                        YWSBS_Subscription()->start_subscription( $subscription, $order_id );
                    }
                }
            }
        }

        public function renew_order( $subscription_id ) {

            $subscription_meta = YWSBS_Subscription()->get_subscription_meta( $subscription_id );

            $order = wc_create_order( $args = array(
                'status'      => 'on-hold',
                'customer_id' => $subscription_meta['user_id']
            ) );


            $args = array(
                'subscriptions'       => array($subscription_id),
                'billing_first_name'  => $subscription_meta['billing_first_name'],
                'billing_last_name'   => $subscription_meta['billing_last_name'],
                'billing_company'     => $subscription_meta['billing_company'],
                'billing_address_1'   => $subscription_meta['billing_address_1'],
                'billing_address_2'   => $subscription_meta['billing_address_2'],
                'billing_city'        => $subscription_meta['billing_city'],
                'billing_state'       => $subscription_meta['billing_state'],
                'billing_postcode'    => $subscription_meta['billing_postcode'],
                'billing_country'     => $subscription_meta['billing_country'],
                'billing_email'       => $subscription_meta['billing_email'],
                'billing_phone'       => $subscription_meta['billing_phone'],

                'shipping_first_name' => $subscription_meta['shipping_first_name'],
                'shipping_last_name'  => $subscription_meta['shipping_last_name'],
                'shipping_company'    => $subscription_meta['shipping_company'],
                'shipping_address_1'  => $subscription_meta['shipping_address_1'],
                'shipping_address_2'  => $subscription_meta['shipping_address_2'],
                'shipping_city'       => $subscription_meta['shipping_city'],
                'shipping_state'      => $subscription_meta['shipping_state'],
                'shipping_postcode'   => $subscription_meta['shipping_postcode'],
                'shipping_country'    => $subscription_meta['shipping_country'],
            );

            foreach ( $args as $key => $value  ) {
                add_post_meta( $order->id, '_'.$key, $value );
            }


            $_product = wc_get_product( ( isset( $subscription_meta['variation_id'] ) && !empty($subscription_meta['variation_id'])) ? $subscription_meta['variation_id'] : $subscription_meta['product_id'] );

            $total = 0;
            $tax_total = 0;

            $variations = array();


            $item_id = $order->add_product(
                $_product,
                $subscription_meta['quantity'],
                array(
                    'variation' => $variations,
                    'totals'    => array(
                        'subtotal'     => $subscription_meta['line_subtotal'],
                        'subtotal_tax' => $subscription_meta['line_subtotal_tax'],
                        'total'        => $subscription_meta['line_total'],
                        'tax'          => $subscription_meta['line_tax'],
                        'tax_data'     => maybe_unserialize($subscription_meta['line_tax_data'])
                    )
                )
            );

            if ( !$item_id ) {
                throw new Exception( sprintf( __( 'Error %d: unable to create the order. Please try again.','yith-woocommerce-subscription'), 402 ) );
            }
            else {
                $total += $subscription_meta['line_total'];
                $tax_total += $subscription_meta['line_tax'];
            }

            $shipping_cost = 0;
            //Shipping
            if ( !empty( $subscription_meta['subscriptions_shippings'] ) ) {
                foreach ( $subscription_meta['subscriptions_shippings']  as $ship ) {

                    $shipping_item_id = wc_add_order_item( $order->id, array(
                        'order_item_name'   => $ship['method']->label,
                        'order_item_type'   => 'shipping',
                    ));

                    $shipping_cost += $ship['method']->cost;
                    wc_add_order_item_meta($shipping_item_id, 'method_id',  $ship['method']->method_id);
                    wc_add_order_item_meta($shipping_item_id, 'cost', wc_format_decimal( $ship['method']->cost));
                    wc_add_order_item_meta($shipping_item_id, 'taxes',  $ship['method']->taxes);
                }

                $order->set_total( wc_format_decimal( $shipping_cost ), 'shipping' );

            }

            $order->set_total( $total + $tax_total + $shipping_cost );
            $order->update_taxes();

            //attach the new order to the subscription
            $subscription_meta['order_ids'][] = $order->id;
            update_post_meta( $subscription_id,'_order_ids', $subscription_meta['order_ids'] );

            $order->add_order_note( sprintf(__( 'This order has been created to renew the subscription #%d', 'yith-woocommerce-subscription' ), $subscription_id));

            return $order->id;

        }

    }
}

/**
 * Unique access to instance of YWSBS_Subscription_Order class
 *
 * @return \YWSBS_Subscription_Order
 */
function YWSBS_Subscription_Order() {
    return YWSBS_Subscription_Order::get_instance();
}
