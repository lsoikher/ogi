<?php
/**
 * Functions for users wanting to upgrade to premium
 *
 * @package myStore
 */

/**
 * Display the upgrade to Premium page & load styles.
 */
function mystore_premium_admin_menu() {
    global $mystore_upgrade_page;
    $mystore_upgrade_page = add_theme_page( __( 'About myStore', 'mystore' ), '<span class="premium-link">' . __( 'About myStore', 'mystore' ) . '</span>', 'edit_theme_options', 'theme_info', 'mystore_render_upgrade_page' );
}
add_action( 'admin_menu', 'mystore_premium_admin_menu' );

/**
 * Enqueue admin stylesheet only on upgrade page.
 */
function mystore_load_upgrade_page_scripts( $hook ) {
    global $mystore_upgrade_page;
    if ( $hook != $mystore_upgrade_page )
        return;
    
    wp_enqueue_style( 'mystore-upgrade-css', get_template_directory_uri() . '/upgrade/css/upgrade-admin.css' );
    wp_enqueue_script( 'caroufredsel', get_template_directory_uri() . '/js/jquery.carouFredSel-6.2.1-packed.js', array( 'jquery' ), MYSTORE_THEME_VERSION, true );
    wp_enqueue_script( 'mystore-upgrade-js', get_template_directory_uri() . '/upgrade/js/upgrade-custom.js', array( 'jquery' ), MYSTORE_THEME_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'mystore_load_upgrade_page_scripts' );

/**
 * Render the premium upgrade/order page
 */
function mystore_render_upgrade_page() {
	get_template_part( 'upgrade/tpl/upgrade-page' );
}
