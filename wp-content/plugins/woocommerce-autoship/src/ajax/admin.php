<?php

function wc_autoship_ajax_upgrade_db() {
	if ( ! wc_autoship_user_is_admin( get_current_user_id() ) ) {
		wc_autoship_ajax_result( 403 );
	}
	wc_autoship_upgrade_db();
	wc_autoship_add_message( __( 'WC Autoship database upgraded!', 'wc-autoship' ) );
	wp_redirect( admin_url( '/' ) );
}
add_action( 'wp_ajax_wc_autoship_upgrade_db', 'wc_autoship_ajax_upgrade_db' );

function wc_autoship_ajax_create_pages() {
	if ( ! current_user_can('publish_pages') ) {
		// Unauthorized
		wc_autoship_ajax_result( 403 );
	}

	// Schedules page
	$schedules_page_option_name = 'wc_autoship_schedules_page_id';
	$schedules_page_id = get_option( $schedules_page_option_name );
	if ( empty( $schedules_page_id ) ) {
		$schedules_page_id = wc_autoship_create_page( 'Autoship Schedules', '[autoship-schedules]' );
		if ( ! empty( $schedules_page_id ) ) {
			update_option( $schedules_page_option_name, $schedules_page_id );
		}
	}

	wp_redirect( admin_url( '/admin.php?page=wc-settings&tab=wc_autoship' ) );
}
add_action( 'wp_ajax_wc_autoship_create_pages', 'wc_autoship_ajax_create_pages' );

function wc_autoship_create_page( $page_title, $page_content, $page_parent = 0 ) {
	$blog_page = array(
		'post_type' => 'page',
		'post_title' => $page_title,
		'post_content' => $page_content,
		'post_status' => 'publish',
		'post_author' => 1,
		'post_parent' => $page_parent,
		'comment_status' => 'closed'
	);
	return wp_insert_post( $blog_page );
}

function wc_autoship_ajax_create_pipey_key() {
	if ( ! wc_autoship_user_is_admin( get_current_user_id() ) ) {
		wc_autoship_ajax_result( 403 );
	}
	wc_autoship_create_pipey_key();
	wc_autoship_add_message( __( 'WC Autoship configured!', 'wc-autoship' ) );
	wp_redirect( admin_url( '/' ) );
}
add_action( 'wp_ajax_wc_autoship_create_pipey_key', 'wc_autoship_ajax_create_pipey_key' );

function wc_autoship_ajax_create_pipey_ip() {
	if ( ! wc_autoship_user_is_admin( get_current_user_id() ) ) {
		wc_autoship_ajax_result( 403 );
	}
	require_once( WC_AUTOSHIP_SRC_DIR . '/Pipey/Client.php' );
	try {
		$pipey = new WC_Autoship_Pipey_Client();
		$ip = $pipey->get_ip( $_REQUEST['nonce'] );
		update_option( 'wc_autoship_pipey_ip', $ip );
		delete_option( 'wc_autoship_get_pipey_ip_nonce' );
		wc_autoship_add_message( __( 'WC Autoship configured!', 'wc-autoship' ) );
	} catch ( Exception $e ) {
		wc_autoship_add_message( __( 'WC Autoship could not be configured!', 'wc-autoship' ), 'error' );
		wc_autoship_add_message( esc_html( $e->getCode() . ' ' . $e->getMessage() ), 'error' );
	}
	wp_redirect( admin_url( '/' ) );
}
add_action( 'wp_ajax_wc_autoship_create_pipey_ip', 'wc_autoship_ajax_create_pipey_ip' );

function wc_autoship_ajax_reset_configuration() {
	if ( ! wc_autoship_user_is_admin( get_current_user_id() ) ) {
		wc_autoship_ajax_result( 403 );
	}
	// Delete config options
	delete_option( 'wc_autoship_pipey_key' );
	delete_option( 'wc_autoship_pipey_ip' );
	delete_option( 'wc_autoship_get_pipey_ip_nonce' );
	// Notify user
	wc_autoship_add_message( __( 'The WC Autoship Configuration was reset! Please follow the alerts below to re-configure.', 'wc-autoship' ), 'notice-warning' );
	// Redirect to admin dashboard
	wp_redirect( admin_url( '/' ) );
}
add_action( 'wp_ajax_wc_autoship_reset_configuration', 'wc_autoship_ajax_reset_configuration' );

function wc_autoship_ajax_delete_cache() {
	if ( ! wc_autoship_user_is_admin( get_current_user_id() ) ) {
		wc_autoship_ajax_result( 403 );
	}
	wc_autoship_cache_delete( 'schedules_cart' );
	// Notify user
	wc_autoship_add_message( __( 'The WC Autoship Cache has been deleted!', 'wc-autoship' ) );
	// Redirect to settings page
	wp_redirect( admin_url( '/admin.php?page=wc-settings&tab=wc_autoship' ) );
}
add_action( 'wp_ajax_wc_autoship_delete_cache', 'wc_autoship_ajax_delete_cache' );

function wc_autoship_ajax_reset_cron() {
	if ( ! wc_autoship_user_is_admin( get_current_user_id() ) ) {
		wc_autoship_ajax_result( 403 );
	}
	wc_autoship_unschedule_autoship();
	wc_autoship_schedule_autoship();
	wc_autoship_add_message( __( "Autoship cron events have been reset!", "wc-autoship" ) );
	wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=wc_autoship' ) );
}
add_action( 'wp_ajax_wc_autoship_reset_cron', 'wc_autoship_ajax_reset_cron' );

function wc_autoship_admin_dismiss_wp_cron_warning() {
	if ( ! wc_autoship_user_is_admin( get_current_user_id() ) ) {
		wc_autoship_ajax_result( 403 );
	}
	// Update dismiss message
	update_option( 'wc_autoship_wp_cron_warning_dismissed', 'yes' );
	// Redirect to admin dashboard
	wp_redirect( admin_url( '/' ) );
}
add_action( 'wp_ajax_wc_autoship_dismiss_wp_cron_warning', 'wc_autoship_admin_dismiss_wp_cron_warning' );