<?php

/**
 * Delete a customer and their autoship schedules
 * @param int $customer_id
 */
function wc_autoship_delete_customer( $customer_id ) {
	global $wpdb;
	$schedule_item_ids = $wpdb->get_col( $wpdb->prepare(
		"SELECT id FROM {$wpdb->prefix}wc_autoship_schedule_items 
			WHERE schedule_id IN (
				SELECT id FROM {$wpdb->prefix}wc_autoship_schedules WHERE customer_id = %d
			)",
		$customer_id
	) );
	foreach ( $schedule_item_ids as $id ) {
		$wpdb->delete( "{$wpdb->prefix}wc_autoship_schedule_items", array(
			'id' => $id
		) );
	}
	$wpdb->delete( "{$wpdb->prefix}wc_autoship_schedules", array(
		'customer_id' => $customer_id
	) );
}
add_action( 'delete_user', 'wc_autoship_delete_customer', 10, 1 );

function wc_autoship_customer_alert_missing_payment_methods() {
	$site_url = site_url( $_SERVER['REQUEST_URI'] );
	$my_account_url = wc_get_page_permalink( 'myaccount' );
	if ( 0 !== strpos( $site_url, $my_account_url ) ) {
		return;
	}
	$autoship_schedules_url = wc_get_account_endpoint_url( 'autoship-schedules' );
	if ( 0 === strpos( $site_url, $autoship_schedules_url ) ) {
		return;
	}

	$customer_id = get_current_user_id();
	global $wpdb;
	$missing_tokens_count = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*)
			FROM {$wpdb->prefix}wc_autoship_schedules AS s
			LEFT JOIN {$wpdb->prefix}woocommerce_payment_tokens AS pt
			ON(s.payment_token_id = pt.token_id)
			WHERE s.customer_id = %s AND pt.token_id IS NULL",
		$customer_id
	) );
	if ( $missing_tokens_count > 0 ) {
		$autoship_schedules_link = sprintf( '<a href="%s">Autoship Schedules</a>', esc_attr( $autoship_schedules_url ) );
		wc_add_notice( __( "$missing_tokens_count of your $autoship_schedules_link are missing a valid payment method!", 'wc_autoship' ), 'error' );
	}
}
//add_action( 'woocommerce_init', 'wc_autoship_customer_alert_missing_payment_methods' );