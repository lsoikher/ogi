<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists ( 'WC_Product_Gift_Card' ) ) {

    /**
     *
     * @class   WC_Product_Gift_Card
     * @package Yithemes
     * @since   1.0.0
     * @author  Your Inspiration Themes
     */
    class WC_Product_Gift_Card extends WC_Product {
        /**
         * @var float Minimum amount from the gift card amount list
         */
        public $min_price = null;

        /**
         * @var float Maximum amount from the gift card amount list
         */
        public $max_price = null;

        /**
         * @var array gift card amount list
         */
        private $amounts;

        /**
         * @var int gift card amounts count
         */
        private $amounts_count;

        /**
         * Initialize a gift card product.
         *
         * @param mixed $product
         */
        public function __construct ( $product ) {
            $this->product_type      = 'gift-card';
            $this->virtual           = true;
            $this->sold_individually = false;

            parent::__construct ( $product );

            $this->amounts       = ywgc_instance ()->get_gift_card_product_amounts ( $this->id );
            $this->amounts_count = count ( $this->amounts );

            if ( $count = count ( $this->amounts ) ) {
                $this->min_price = $this->amounts[ 0 ];
                $this->max_price = $this->amounts[ $count - 1 ];
            }
        }

        /**
         * Check if the product is purchasable
         *
         * @return bool
         */
        public function is_purchasable () {

            if ( ! $this->amounts_count ) {

                $purchasable = false;
            } else {
                $purchasable = true;

            }

            return apply_filters ( 'woocommerce_is_purchasable', $purchasable, $this );
        }

        /**
         * Returns the price in html format.
         *
         * @param string $price (default: '')
         *
         * @return string
         */
        public function get_price_html ( $price = '' ) {
            // No price for current gift card
            if ( ! $this->amounts_count ) {

                $price = apply_filters ( 'yith_woocommerce_gift_cards_empty_price_html', '', $this );
            } else {
                $price = $this->min_price !== $this->max_price ? sprintf ( _x ( '%1$s&ndash;%2$s', 'Price range: from-to', 'yith-woocommerce-gift-cards' ), wc_price ( $this->min_price ), wc_price ( $this->max_price ) ) : wc_price ( $this->min_price );
                $price = apply_filters ( 'yith_woocommerce_gift_cards_amount_range', $price, $this );
            }

            return apply_filters ( 'woocommerce_get_price_html', $price, $this );
        }

        /**
         * Get the add to cart button text
         *
         * @return string
         */
        public function add_to_cart_text () {
            return apply_filters ( 'yith_woocommerce_gift_cards_add_to_cart_text', __ ( 'Select amount', 'yith-woocommerce-gift-cards' ), $this );
        }

        /**
         * Get the gift card amount list
         *
         * @return mixed|void
         */
        public function get_gift_card_amounts () {
            return apply_filters ( 'yith_ywgc_gift_card_amounts', $this->amounts, $this );
        }
    }
}