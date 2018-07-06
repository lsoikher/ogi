<?php

/**
 * Frontend / visualisation stuff 
 *  - Templating
 *  - Appearance of free products in the cart
 */
class WJECF_Pro_Free_Products_Template {
    /**
     * @var The WJECF_Pro_Free_Products instance
     */
    private $plugin = null;

    public function __construct( $plugin ) {
        $this->plugin = $plugin;
        // $this->plugin = WJECF()->get_plugin('WJECF_Pro_Free_Products');
    }

    public function init_hook() {
        //Frontend hooks - Cart visualisation
        add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'filter_woocommerce_cart_item_remove_link' ), 10, 2 );
        add_filter( 'woocommerce_cart_item_price', array( $this, 'filter_woocommerce_cart_item_price' ), 10, 3 );
        add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'filter_woocommerce_cart_item_subtotal' ), 10, 3 );
        add_filter( 'woocommerce_cart_item_quantity', array( $this, 'filter_woocommerce_cart_item_quantity' ), 10, 2 );        
    }


    /**
     * Renders the attribute selectors for the given product
     * @param WC_Product $product The variable product
     * @param array $selected_attributes Array with the selected attributes as [ attrib_name => value ]
     * @param string $id_prefix prefix for the DOM-element id
     * @param string $field_name_prefix prefix for the DOM-element name
     */
    public function render_attribute_selectors( $product, $selected_attributes, $id_prefix, $field_name_prefix ) {
        //Variable product attributes
        $attributes = $product->get_variation_attributes();
        foreach ( $attributes as $attribute_name => $options ) {
            $field_id = $id_prefix . '_' . sanitize_title( $attribute_name );

            $sane_attribute_name = 'attribute_' . sanitize_title( $attribute_name );

            $selected = isset( $selected_attributes[ $sane_attribute_name ] ) 
                ? wc_clean( urldecode( $selected_attributes[ $sane_attribute_name ] ) ) 
                : WJECF_Wrap( $product )->get_variation_default_attribute( $attribute_name );

            sprintf( '<label for="%s">%s</label>', $field_id, wc_attribute_label( $attribute_name ) );
            WJECF_WC()->wc_dropdown_variation_attribute_options( array( 
                'id' => $field_id,
                'name' => $field_name_prefix . '[' . $sane_attribute_name . ']',
                'options' => $options, 
                'attribute' => $attribute_name, 
                'product' => $product, 
                'selected' => $selected 
            ) );
        }
    }

    /**
     * Calls WJECF()->include_template(), but will inject $this as $template
     * @param string $template_name The PHP filename in the templates directory
     * @param array $variables Array of variables that must be available in the template
     */
    public function render_template( $template_name, $variables ) {
        WJECF()->include_template( $template_name, array_merge( $variables, array( 'template' => $this ) ) );
    }    


    /**
     * Notifies the customer that the amount of products in qtock is not sufficient.
     * @param type $product 
     * @return type
     */
    public function notify_not_enough_stock( $product ) {
        $msg = __( 'Sorry, we do not have enough "%1$s" in stock (%2$s in stock). Please review your selection.', 'woocommerce-jos-autocoupon' );
        $msg = sprintf( $msg, $product->get_title(), $product->get_stock_quantity() );
        wc_add_notice( $msg, 'error' );
    }

    public function notify_select_variation( $product ) {
        $msg = __( 'Please choose a variation of "%s".', 'woocommerce-jos-autocoupon' );
        $msg = sprintf( $msg, $product->get_title() );
        wc_add_notice( $msg, 'error' );
    }    

    /**
     * Show 'Free!' in the cart for free product
     */
    public function filter_woocommerce_cart_item_price( $price_html, $cart_item, $cart_item_key ) {
        if ( $this->plugin->is_free_product( $cart_item['data'] ) ) {
            $price_html = apply_filters( 'wjecf_free_cart_item_price', __('Free!', 'woocommerce'), $price_html, $cart_item, $cart_item_key );
        }
        return $price_html;
    }

    /**
     * Show 'Free!' in the cart for free product
     */
    public function filter_woocommerce_cart_item_subtotal( $price_html, $cart_item, $cart_item_key ) {
        if ( $this->plugin->is_free_product( $cart_item['data'] ) ) {
            $price_html = apply_filters( 'wjecf_free_cart_item_subtotal', __('Free!', 'woocommerce'), $price_html, $cart_item, $cart_item_key );
        }
        return $price_html;
    }

    /**
     * Quantity is readonly for free product
     */
    public function filter_woocommerce_cart_item_quantity ( $product_quantity_html, $cart_item_key ) {        
        $cart_item = WJECF_WC()->get_cart_item( $cart_item_key );

        if ( $this->plugin->is_free_product( $cart_item['data'] ) ) {
            $qty = intval($cart_item['quantity']);
            $product_quantity_html = sprintf( '%d <input type="hidden" name="cart[%s][qty]" value="%d" />', $qty, $cart_item_key, $qty );
        }
        return $product_quantity_html;

    }

    /**
     * Remove the 'remove item'-link
     */
    public function filter_woocommerce_cart_item_remove_link( $remove_html, $cart_item_key ) {
        $cart_contents = WC()->cart->get_cart();
        //Remove the link if it's a free item
        if ( $this->plugin->is_free_product( $cart_contents[$cart_item_key]['data'] ) ) {
            return '';
        }
        return $remove_html;
    }

}