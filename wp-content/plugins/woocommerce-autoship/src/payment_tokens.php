<?php

function wc_autoship_payment_token_deleted( $deleted_token_id ) {
	global $wpdb;
	$schedules_result = $wpdb->get_results( $wpdb->prepare(
		"SELECT s.id
			FROM {$wpdb->prefix}wc_autoship_schedules AS s
			WHERE s.payment_token_id = %s",
		$deleted_token_id
	) );
	$count = count( $schedules_result );
	if ( $count > 0 ) {
		foreach ( $schedules_result as $schedule_row ) {
			$wpdb->update( "{$wpdb->prefix}wc_autoship_schedules", array( 'payment_token_id' => null ), array( 'id' => $schedule_row->id ) );
		}
		wc_add_notice( __( "You have deleted a payment method that was associated with $count of your Autoship Schedules. Please select a new payment method for your Autoship Schedules.", "wc-autoship" ), 'error' );
	}

}
add_action( 'woocommerce_payment_token_deleted', 'wc_autoship_payment_token_deleted', 10, 1 );

