<?php

function wc_autoship_shortcode_schedules( $atts = array() ) {
	$atts = shortcode_atts( array(), $atts );

	$current_user_id = get_current_user_id();
	$customer_id = isset( $_REQUEST['customer_id'] ) ? $_REQUEST['customer_id'] : $current_user_id;
	$template = 'autoship-schedules';
	if ( $customer_id != $current_user_id ) {
		if ( ! wc_autoship_user_is_admin( $current_user_id ) ) {
			return '<p>' . __( 'Forbidden', 'wc-autoship' ) . '</p>';
		}
		$template = 'autoship-schedules-admin';
	}
	if ( empty( $customer_id ) ) {
		return wc_autoship_render_template( 'no-autoship' );
	}
	$customer = new WP_User( $customer_id );

	require_once( 'Models/Schedule.php' );
	$schedules = WC_Autoship_Models_Schedule::get_schedules_for_customer( $customer_id );
	if ( empty( $schedules ) ) {
		return wc_autoship_render_template( 'no-autoship' );
	}

	return wc_autoship_render_template( $template, array(
		'customer' => $customer,
		'schedules' => $schedules,
		'enable_coupon_field' => get_option( 'wc_autoship_schedules_enable_coupon_field' )
	) );
}
add_shortcode( 'autoship-schedules', 'wc_autoship_shortcode_schedules' );

function wc_autoship_shortcode_alerts() {
	$customer_id = get_current_user_id();
	if ( empty( $customer_id ) ) {
		return '';
	}
	$alerts = array();
	return wc_autoship_render_template( 'autoship-alerts', array(
		'customer_id' => $customer_id,
		'alerts' => $alerts
	) );
}
add_shortcode( 'autoship-alerts', 'wc_autoship_shortcode_alerts' );

function wc_autoship_shortcode_message() {
	wc_autoship_include_template( 'autoship-message', array(
		'message' => get_option( 'wc_autoship_management_page_message' )
	) );
}