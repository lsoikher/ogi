<?php
function wc_autoship_price_example( $price, $product_id, $unknown, $user_id, $unknown ) {
    
}
add_filter( 'wc_autoship_price', 'wc_autoship_price_example', 5, 0 );

function wc_autoship_product_autoship_options_variable_price_example( $price, $variation_id, $user_id ) {
    return $price;
}
add_filter( 'wc_autoship_product_autoship_options_variable_price', 'wc_autoship_product_autoship_options_variable_price_example', 3, 0 );