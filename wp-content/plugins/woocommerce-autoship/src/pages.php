<?php

function wc_autoship_account_menu_items( $items ) {
	flush_rewrite_rules();
	$autoship_items = array( 'autoship-schedules' => __( 'Autoship Schedules', 'wc-autoship' ) );
	$item_count = count( $items );
	$front = array_slice( $items, 0, $item_count - 1, true );
	$back = array_slice( $items, $item_count - 1, 1, true );
	$items = array_merge( $front, $autoship_items, $back );
	return $items;
}
add_filter( 'woocommerce_account_menu_items', 'wc_autoship_account_menu_items' );

function wc_autoship_account_autoship_schedules_endpoint( $value ) {
	echo do_shortcode( '[autoship-schedules]' );
}
add_action( 'woocommerce_account_autoship-schedules_endpoint', 'wc_autoship_account_autoship_schedules_endpoint' );

function wc_autoship_account_autoship_schedules_title( $title ) {
	global $wp_query;

	if ( isset( $wp_query->query_vars[ 'autoship-schedules' ] ) && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
		return __( 'Autoship Schedules', 'wc-autoship' );
	}

	return $title;
}
add_filter( 'the_title', 'wc_autoship_account_autoship_schedules_title' );

function wc_autoship_account_add_endpoints() {
	add_rewrite_endpoint( 'autoship-schedules', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'wc_autoship_account_add_endpoints' );

function wc_autoship_account_add_query_vars( $vars ) {
	$vars[] = 'autoship-schedules';
	return $vars;
}
add_filter( 'query_vars', 'wc_autoship_account_add_query_vars' );
