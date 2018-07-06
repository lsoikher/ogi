<?php

function wc_autoship_add_cart_item( $data, $cart_item_key, $values = NULL ) {
	$product_id = $data['product_id'];
	$variation_id = $data['variation_id'];
	if ( $values == NULL ) {
		$values = $_REQUEST;
	}
	$autoship_enabled = get_post_meta( $product_id, '_wc_autoship_enable_autoship', true );
	if ( $autoship_enabled != 'yes' || empty( $values['wc_autoship_frequency'] ) ) {
		// No autoship
		return $data;
	}

	// Auto-Ship frequency
	$autoship_frequency = intval( $values['wc_autoship_frequency'] );
	if ( ! wc_autoship_add_cart_item_frequency_is_valid( $product_id, $autoship_frequency ) ) {
		return $data;
	}

	// Price
	$price = $data['data']->get_price();
	// Auto-Ship price
	$price_product_id = empty( $variation_id ) ? $product_id : $variation_id;
	$product = wc_get_product( $price_product_id );
	$autoship_price = apply_filters( 'wc_autoship_price',
		get_post_meta( $price_product_id, '_wc_autoship_price', true ),
		$price_product_id,
		$autoship_frequency,
		get_current_user_id(),
		0
	);
	if ( ! empty( $autoship_price ) ) {
		$price = $autoship_price;
	}
	// Auto-Ship cart item discount
	$cart_item_discount = apply_filters( 'wc_autoship_cart_item_discount',
		0.0,
		$cart_item_key,
		$price_product_id,
		$autoship_frequency,
		get_current_user_id()
	);
	$price -= $cart_item_discount;
	// Filter price
	$price = apply_filters( 'wc_autoship_cart_item_price',
		$price,
		$cart_item_key,
		$price_product_id,
		$autoship_frequency,
		get_current_user_id()
	);
	// Set new price
	$data['data']->set_price( $price );
	return $data;
}
add_filter( 'woocommerce_add_cart_item', 'wc_autoship_add_cart_item', 10, 2 );

function wc_autoship_get_cart_item_from_session( $data, $values, $cart_item_key ) {
	// Persistent keys
	$persistent_keys = array(
		'wc_autoship_frequency'
	);
	foreach ( $persistent_keys as $key ) {
		if ( isset( $values[ $key ] ) ) {
			$data[ $key ] = $values[ $key ];
		}
	}
	// Add cart item
	$data = wc_autoship_add_cart_item( $data, $cart_item_key, $values );
	return $data;
}
add_filter( 'woocommerce_get_cart_item_from_session', 'wc_autoship_get_cart_item_from_session', 10, 3 );

function wc_autoship_add_cart_item_data( $data, $product_id, $variation_id ) {
	$autoship_enabled = get_post_meta( $product_id, '_wc_autoship_enable_autoship', true );
	if ( $autoship_enabled != 'yes' || empty( $_REQUEST['wc_autoship_frequency'] ) ) {
		// No autoship
		return $data;
	}

	// Auto-Ship frequency
	$autoship_frequency = intval( $_REQUEST['wc_autoship_frequency'] );
	if ( ! wc_autoship_add_cart_item_frequency_is_valid( $product_id, $autoship_frequency ) ) {
		$message = __( 'Invalid Auto-Ship Frequency selected. This item could not be added to auto-ship.', 'wc-autoship' );
		wc_add_notice( $message, 'error' );
		return $data;
	}
	$data['wc_autoship_frequency'] = $autoship_frequency;

	return $data;
}
add_filter( 'woocommerce_add_cart_item_data', 'wc_autoship_add_cart_item_data', 10, 3 );

function wc_autoship_add_cart_item_frequency_is_valid( $product_id, $autoship_frequency ) {
	$autoship_min_frequency = intval( get_post_meta( $product_id, '_wc_autoship_min_frequency', true ) );
	$autoship_max_frequency = intval( get_post_meta( $product_id, '_wc_autoship_max_frequency', true ) );
	if ( $autoship_frequency < $autoship_min_frequency || $autoship_frequency > $autoship_max_frequency ) {
		return false;
	}
	return true;
}

function wc_autoship_get_item_data( $data, $item ) {
	if ( ! empty( $item['wc_autoship_frequency'] ) ) {
		$frequency = intval( $item['wc_autoship_frequency'] );
		$data[] = array(
			'name' => __( 'Auto-Ship', 'wc-autoship' ),
			'value' => __( "Every $frequency days", 'wc-autoship' )
		);
	}
	return $data;
}
add_filter( 'woocommerce_get_item_data', 'wc_autoship_get_item_data', 10, 2 );

function wc_autoship_available_payment_gateways( $available_gateways ) {
	if ( is_admin() && ! is_ajax() ) {
		return $available_gateways;
	}
	if ( ! wc_autoship_cart_has_autoship_items() ) {
		// No cart, return default payment gateways
		return $available_gateways;
	}

	// Filter for autoship payment gateways
	$autoship_gateways = array();
	foreach ( $available_gateways as $id => $gateway ) {
		if ( $gateway->supports( 'tokenization' ) ) {
			$autoship_gateways[ $id ] = $gateway;
		}
	}
	return $autoship_gateways;
}
add_filter( 'woocommerce_available_payment_gateways', 'wc_autoship_available_payment_gateways', 10, 1 );

function wc_autoship_get_option_enable_guest_checkout( $enable_guest_checkout ) {
	if ( is_admin() && ! is_ajax() ) {
		return $enable_guest_checkout;
	}
	if ( wc_autoship_cart_has_autoship_items() ) {
		return 'no';
	}
	return $enable_guest_checkout;
}
add_filter( 'pre_option_woocommerce_enable_guest_checkout', 'wc_autoship_get_option_enable_guest_checkout', 10, 1 );

function wc_autoship_cart_has_autoship_items() {
	$cart = WC()->cart;
	if ( empty( $cart ) || ( ! is_cart() && ! is_checkout() && ! is_ajax() ) ) {
		// No cart
		return false;
	}
	// Check if cart has autoship items
	$has_autoship_items = false;
	foreach ( $cart->get_cart() as $item ) {
		if ( isset( $item['wc_autoship_frequency'] ) ) {
			// Autoship item in cart
			$has_autoship_items = true;
			break;
		}
	}
	return $has_autoship_items;
}

function wc_autoship_print_cart_autoship_options( $for_product = null ) {
	global $product;
	if ( empty( $for_product ) ) {
		$for_product = $product;
	}

	if ( $for_product->is_type( 'variable' ) ) {
		return;
	}

	$parent_id = $for_product->is_type( 'variation' ) ? $for_product->get_parent_id() : $for_product->get_id();
	$autoship_enabled = get_post_meta( $parent_id, '_wc_autoship_enable_autoship', true );
	if ( $autoship_enabled == 'yes' ) {
		// Get autoship price
		$autoship_price = apply_filters( 'wc_autoship_price',
			get_post_meta( $for_product->get_id(), '_wc_autoship_price', true ),
			$for_product->get_id(),
			0,
			get_current_user_id(),
			0
		);
		// Filter template autoship price
		$autoship_price = apply_filters( 'wc_autoship_product_autoship_options_price',
			$autoship_price,
			$for_product->get_id(),
			get_current_user_id()
		);
		// Get frequency ranges
		$autoship_min_frequency = get_post_meta( $parent_id, '_wc_autoship_min_frequency', true );
		$autoship_max_frequency = get_post_meta( $parent_id, '_wc_autoship_max_frequency', true );
		$autoship_default_frequency = get_post_meta( $parent_id, '_wc_autoship_default_frequency', true );
		// Render template
		wc_autoship_include_template(
			'product/autoship-options',
			array(
				'product' => $for_product,
				'autoship_price' => $autoship_price,
				'autoship_min_frequency' => $autoship_min_frequency,
				'autoship_max_frequency' => $autoship_max_frequency,
				'autoship_default_frequency' => $autoship_default_frequency
			)
		);
	}
}
$product_page_options_hook = get_option( 'wc_autoship_product_page_options_hook' );
add_action( ( ! empty( $product_page_options_hook ) ) ? $product_page_options_hook : 'woocommerce_before_add_to_cart_button', 'wc_autoship_print_cart_autoship_options' );

function wc_autoship_print_cart_autoship_options_variable( $for_product = null ) {
	global $product;
	if ( empty( $for_product ) ) {
		$for_product = $product;
	}

	$autoship_enabled = get_post_meta( $for_product->get_id(), '_wc_autoship_enable_autoship', true );
	if ( $autoship_enabled == 'yes' ) {
		// Find autoship price for default attributes
		$autoship_price = '';
		$default_attributes = version_compare( WC()->version, '3.0', '<' ) ? $for_product->get_variation_default_attributes() : $for_product->get_default_attributes();
		if ( ! empty( $default_attributes ) ) {
			$variations = $for_product->get_available_variations();
			foreach ( $variations as $variation ) {
				foreach ( $default_attributes as $name => $value ) {
					if ( isset( $variation['attributes'][ 'attribute_' . $name ] ) && $variation['attributes'][ 'attribute_' . $name ] == '' ) {
						continue;
					} elseif ( ! isset( $variation['attributes'][ 'attribute_' . $name ] ) || $variation['attributes'][ 'attribute_' . $name ] != $value ) {
						continue 2;
					}
				}
				// Get autoship price
				$autoship_price = apply_filters( 'wc_autoship_price',
					get_post_meta( $variation['variation_id'], '_wc_autoship_price', true ),
					$variation['variation_id'],
					0,
					get_current_user_id(),
					0
				);
				// Filter template autoship price
				$autoship_price = apply_filters( 'wc_autoship_product_autoship_options_variable_price',
					$autoship_price,
					$variation['variation_id'],
					get_current_user_id()
				);
				break;
			}
		}
		// Get frequency ranges
		$autoship_min_frequency = get_post_meta( $for_product->get_id(), '_wc_autoship_min_frequency', true );
		$autoship_max_frequency = get_post_meta( $for_product->get_id(), '_wc_autoship_max_frequency', true );
		$autoship_default_frequency = get_post_meta( $for_product->get_id(), '_wc_autoship_default_frequency', true );
		wc_autoship_include_template(
			'product/autoship-options-variable',
			array(
				'product' => $for_product,
				'autoship_price' => $autoship_price,
				'autoship_min_frequency' => $autoship_min_frequency,
				'autoship_max_frequency' => $autoship_max_frequency,
				'autoship_default_frequency' => $autoship_default_frequency
			)
		);
	}
}
$variable_product_page_options_hook = get_option( 'wc_autoship_variable_product_page_options_hook' );
add_action( ( ! empty( $variable_product_page_options_hook ) ) ? $variable_product_page_options_hook : 'woocommerce_before_single_variation', 'wc_autoship_print_cart_autoship_options_variable' );