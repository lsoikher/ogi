<?php

function wc_autoship_shipping_zone_shipping_methods( $methods, $raw_methods, $allowed_classes, $shipping_zone ) {
	$free_shipping_option = get_option( 'wc_autoship_free_shipping' );
	if ( ( ! empty( $free_shipping_option ) && 'yes' != $free_shipping_option && 'no' != $free_shipping_option ) || 'yes' == get_option( 'wc_autoship_free_shipping_on_shipping_error' ) ) {
		require_once( 'Shipping/FreeShipping.php' );
		$methods[] = new WC_Autoship_Shipping_FreeShipping( 1000 );
	}
	return $methods;
}
add_filter( 'woocommerce_shipping_zone_shipping_methods', 'wc_autoship_shipping_zone_shipping_methods', 10, 4 );

/**
 * @param array $rates Array of rates found for the package.
 * @return array
 */
function wc_autoship_package_rates( $rates ) {
	$cart_has_autoship_items = wc_autoship_cart_has_autoship_items();
	$free_shipping_option = get_option( 'wc_autoship_free_shipping' );
	$free_shipping_enabled = false;
	if ( 'checkout+autoship' == $free_shipping_option && $cart_has_autoship_items ) {
		$free_shipping_enabled = true;
	} elseif ( 'autoship' == $free_shipping_option && wc_autoship_ajax_is_pipey_request() && wc_autoship_ajax_pipey_is_authorized() ) {
		$free_shipping_enabled = true;
	} elseif ( 'yes' == get_option( 'wc_autoship_free_shipping_on_shipping_error' ) && count( $rates ) < 2 && $cart_has_autoship_items ) {
		$free_shipping_enabled = true;
	}

	if ( ! $free_shipping_enabled ) {
		foreach ( $rates as $rate_id => $rate ) {
			if ( 'wc_autoship_free_shipping' === $rate->method_id ) {
				unset( $rates[ $rate_id ] );
				break;
			}
		}
	}
	return $rates;
}
add_filter( 'woocommerce_package_rates', 'wc_autoship_package_rates', 100 );