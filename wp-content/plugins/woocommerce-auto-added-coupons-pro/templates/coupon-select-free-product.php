<?php
/**
 * Single coupon ( for the "Select Free Product" on Cart or Checkout page )
 * 
 * This template can be overridden by copying it to yourtheme/woocommerce-auto-added-coupons/coupon-select-free-product.php
 * 
 * @version     2.5.1
 */

defined('ABSPATH') or die();

/**************************************************************************

Available variables: 

 $coupon                  : The WC_Coupon object
 $coupon_code             : The coupon code
 $allow_multiple_products : True if multiplication is enabled for this coupon
 $form_items              : WJECF_Free_Product_Item objects. Contains all info about the free products 
 $selected_quantity       : Amount of items selected by the customer
 $max_quantity            : The max amount of free products for this coupon
 $name_prefix             : The name of the form input field (checkbox / radiobutton / input type="number" )
 $id_prefix               : The index of the field; can be used
 $template                : The template helper object (WJECF_Pro_Free_Products_Template)


**************************************************************************

The form must return:
 
 {$name_prefix}["coupon"] = $coupon_code
 {$name_prefix}["product"][]["product_id"] = product_id
 {$name_prefix}["product"][]["quantity"]   = quantity
 {$name_prefix}["product"][]["attributes"] = attributes for variations

Or, for radiobuttons:
 {$name_prefix}["selected_product"] = $product_id 

Example:
 {$name_prefix}[coupon] = "my_coupon_code"
 {$name_prefix}[product][0][product_id] = 101
 {$name_prefix}[product][0][quantity] = 1
 {$name_prefix}[product][1][product_id] = 102
 {$name_prefix}[product][1][quantity] = 2
 {$name_prefix}[product][1][attributes][attribute_pa_color] = "green"

 {$name_prefix}[coupon] = "other_coupon_code"
 {$name_prefix}[product][0][product_id] = 103
 {$name_prefix}[product][0][quantity] = 1
 
 Will apply:
 - 1x product 101 and 2x product 102 (variation green) for the coupon with code "my_coupon_code"
 - 1x product 103 for the coupon with code "other_coupon_code"


**************************************************************************

Example <input /> tags:
 
 echo '<input type="radio"    name="' . $name_prefix . '[product]"                      value="' . $product_id . '" ' . ( $value == '' ? '' : ' checked="checked"' ) ? ' checked="checked"' : '') . ' />';
 echo '<input type="checkbox" name="' . $name_prefix . '[product][' . $product_id . ']" value="1" ' . ( $value == '' ? '' : ' checked="checked"' ) . ' />';
 echo '<input type="number"   name="' . $name_prefix . '[product][' . $product_id . ']" value="' . esc_attr( $value ) . '" />';


**************************************************************************/

$tooltip = sprintf(
    _n( 
        'You can select one free product.', //singular
        'You can select up to %d free products.', //plural
        $max_quantity,
        'woocommerce-jos-autocoupon'
    ), 
    $max_quantity
);

$input_type = $allow_multiple_products ? 'number' : 'radio';

//This DOM object will manage the total quantity of the selected products
$totalizer_id = $id_prefix . '_total_qty';

?>
<div class="wjecf-select-free-products coupon-<?php echo esc_attr( sanitize_title( $coupon_code ) ); ?>">
    <h3><?php echo WJECF_API()->get_select_free_product_message( $coupon ); ?></h3>
    <input type="hidden" name="<?php echo $name_prefix; ?>[coupon]" value="<?php echo esc_attr( $coupon_code ); ?>" />
    <input type="hidden" id="<?php echo $totalizer_id; ?>" data-wjecf-qty-max="<?php echo $max_quantity; ?>" />
    <ul class="wjecf-cols cols-4">
    <?php 
        foreach ( $form_items as $key => $form_item ):
            $product = $form_item->getProduct(); 
            if ( ! $product instanceof WC_Product || ! $product->is_in_stock() ) {
                //Only display items that are in stock
                continue;
            }

            $value = $form_item->getQuantity();
            $product_id = $form_item->getProductId(); 
            $field_id = esc_attr( $id_prefix . '_' . $key ); // e.g. wjecf_sel_0_0
            $field_name_prefix = esc_attr( "{$name_prefix}[products][{$key}]" ); // e.g. wjecf_sel[0][product][0]
    ?>
            <li>
                <input type="hidden" name="<?php echo $field_name_prefix; ?>[product_id]" value="<?php echo $product_id; ?>" />
                <?php
                    switch( $input_type ) {
                        case 'radio':
                            echo '<input type="radio"    id="' . $field_id . '" name="' . $name_prefix . '[selected_product]" value="' . $product_id . '" ' . ( empty( $value ) ? '' : ' checked="checked"' ) . ' />';
                            break;
                        case 'checkbox':
                            echo '<input type="checkbox" id="' . $field_id . '" name="' . $field_name_prefix . '[quantity]"   value="1" ' . ( empty( $value ) ? '' : ' checked="checked"' ) . ' title="' . esc_attr( $tooltip ) . '" data-wjecf-qty-totalizer="' . $id_prefix . '_total_qty" />';
                            break;
                        case 'number':
                            echo '<input type="number"   id="' . $field_id . '" name="' . $field_name_prefix . '[quantity]"   value="' . intval( $value ) . '" min="0" max="' . $max_quantity . '" title="' . esc_attr( $tooltip ) . '" data-wjecf-qty-totalizer="' . $id_prefix . '_total_qty" />';
                            break;
                    }
                    echo '<label for="' . $field_id . '">' . esc_html( $product->get_title(), 'woocommerce' ) . '</label><br>';
                    echo $product->get_image();

                    //Variable product attributes
                    if ( $product->is_type( 'variable' ) ) {
                        $template->render_attribute_selectors( $product, $form_item->getAttributes(), $field_id, $field_name_prefix . '[attributes]' );
                    }
                ?>
            </li>
    <?php
        endforeach;
    ?>
    </ul>
    <p>
</div>

