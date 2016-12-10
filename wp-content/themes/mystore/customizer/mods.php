<?php
/**
 * Functions used to implement options
 *
 * @package Customizer Library myStore
 */

/**
 * Enqueue Google Fonts Example
 */
function customizer_mystore_fonts() {

	// Font options
	$fonts = array(
		get_theme_mod( 'mystore-body-font', customizer_library_get_default( 'mystore-body-font' ) ),
		get_theme_mod( 'mystore-heading-font', customizer_library_get_default( 'mystore-heading-font' ) ),
		get_theme_mod( 'mystore-site-title-font', customizer_library_get_default( 'mystore-site-title-font' ) ),
	);

	$font_uri = customizer_library_get_google_font_uri( $fonts );

	// Load Google Fonts
	wp_enqueue_style( 'customizer_mystore_fonts', $font_uri, array(), null, 'screen' );

}
add_action( 'wp_enqueue_scripts', 'customizer_mystore_fonts' );