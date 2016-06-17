<?php
/**
 * Jetpack Compatibility File
 * See: https://jetpack.me/
 *
 * @package myStore
 */

/**
 * Add theme support for Infinite Scroll.
 * See: https://jetpack.me/support/infinite-scroll/
 */
function mystore_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'render'    => 'mystore_infinite_scroll_render',
		'footer'    => 'page',
	) );
} // end function mystore_jetpack_setup
add_action( 'after_setup_theme', 'mystore_jetpack_setup' );

function mystore_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'templates/contents/content', get_post_format() );
	}
} // end function mystore_infinite_scroll_render