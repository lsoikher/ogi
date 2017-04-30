<?php

/**
 * Add autoship coupon data tabs
 * @param array $tabs
 * @return array
 */
function wc_autoship_coupon_data_tabs( $tabs ) {
	$tabs['wc_autoship'] = array(
		'label'  => __( 'Auto-Ship', 'wc-autoship' ),
		'target' => 'wc_autoship_coupon_data',
		'class'  => 'wc_autoship_coupon_data'
	);
	return $tabs;
}
add_filter( 'woocommerce_coupon_data_tabs', 'wc_autoship_coupon_data_tabs', 100, 1 );

/**
 * Print autoship coupon data panels
 */
function wc_autoship_coupon_data_panels() {
	?><div id="wc_autoship_coupon_data" class="panel woocommerce_options_panel"><?php

	echo '<div class="options_group">';

	// Expiration date
	woocommerce_wp_text_input( array(
		'id' => 'wc_autoship_expiration_date',
		'label' => __( 'Autoship Expiration Date', 'wc-autoship' ),
		'description' => __( 'The date when this coupon will expire for recurring autoship orders', 'wc-autoship' ),
		'type' => 'date',
		'desc_tip' => true,
		'class' => 'short'
	) );

	echo '</div>';

	?></div><?php
}
add_action( 'woocommerce_coupon_data_panels', 'wc_autoship_coupon_data_panels' );


function wc_autoship_coupon_usage_restriction_panel() {
	// Require autoship
	woocommerce_wp_checkbox( array(
		'id' => 'wc_autoship_require_autoship_checkout',
		'label' => __( 'Autoship Checkout', 'wc-autoship' ),
		'description' => __( 'Require autoship items in the cart at checkout to use this coupon', 'wc-autoship' ),
	) );
}
add_action( 'woocommerce_coupon_options_usage_restriction', 'wc_autoship_coupon_usage_restriction_panel' );

/**
 * Check if coupon is valid
 * @param boolean $valid
 * @param WC_Coupon $coupon
 * @return boolean
 */
function wc_autoship_coupon_is_valid( $valid, $coupon ) {
	if ( ! $valid ) {
		return $valid;
	}

	if ( wc_autoship_ajax_is_pipey_request() && wc_autoship_ajax_pipey_is_authorized() ) {
		return $valid;
	}

	$autoship_required = get_post_meta( $coupon->id, 'wc_autoship_require_autoship_checkout', true );
	if ( 'yes' == $autoship_required ) {
		$woocommerce = WC();
		$cart_items = $woocommerce->cart->get_cart();
		foreach ( $cart_items as $values ) {
			if ( $values['data'] instanceof WC_Product ) {
				if ( ! empty( $values['wc_autoship_frequency'] ) ) {
					return true;
				}
			}
		}
		return false;
	}

	// Return default
	return $valid;
}
add_filter( 'woocommerce_coupon_is_valid', 'wc_autoship_coupon_is_valid', 10, 2 );

/**
 * Save custom fields for autoship coupons
 * @param int $post_id
 */
function wc_autoship_save_custom_fields( $post_id ) {
	$autoship_field_names = array(
		'wc_autoship_expiration_date',
		'wc_autoship_require_autoship_checkout'
	);
	foreach ( $autoship_field_names as $name ) {
		$value = isset( $_POST[ $name ] ) ? $_POST[ $name ] : '';
		update_post_meta( $post_id, $name, $value );
	}
}
add_action( 'woocommerce_process_shop_coupon_meta', 'wc_autoship_save_custom_fields', 10, 1 );
