<?php

function wc_autoship_cache_add( $type, $value, $customer_id = NULL, $schedule_id = NULL, $schedule_item_id = NULL ) {
	global $wpdb;
	if ( ! empty( $wpdb ) ) {
		$data = array(
			'cache_time' => date( 'Y-m-d H:i:s' ),
			'cache_type' => $type,
			'cache_value' => serialize( $value ),
			'cache_customer_id' => $customer_id,
			'cache_schedule_id' => $schedule_id,
			'cache_schedule_item_id' => $schedule_item_id
		);
		$wpdb->show_errors( false );
		$wpdb->insert( "{$wpdb->prefix}wc_autoship_cache", $data );
		$wpdb->show_errors( true );
	}
}

function wc_autoship_cache_find( $type, $age = 0, $customer_id = NULL, $schedule_id = NULL, $schedule_item_id = NULL ) {
	global $wpdb;
	if ( ! empty( $wpdb ) ) {
		$where = array(
			'TIMESTAMPDIFF(SECOND, cache_time, %s) <= %d',
			'cache_type = %s'
		);
		$args = array(
			date( 'Y-m-d H:i:s' ), $age,
			$type
		);
		if ( ! empty( $customer_id ) ) {
			$where[] = 'cache_customer_id = %d';
			$args[] = $customer_id;
		}
		if ( ! empty( $schedule_id ) ) {
			$where[] = 'cache_schedule_id = %d';
			$args[] = $schedule_id;
		}
		if ( ! empty( $schedule_item_id ) ) {
			$where[] = 'cache_schedule_item_id = %d';
			$args[] = $schedule_item_id;
		}
		$wpdb->show_errors( false );
		$value = $wpdb->get_var( $wpdb->prepare(
			"SELECT cache_value FROM {$wpdb->prefix}wc_autoship_cache WHERE " . implode( ' AND ', $where ) . ' ORDER BY cache_time DESC LIMIT 0,1',
			$args
		) );
		if ( ! empty( $value ) ) {
			$data = unserialize( $value );
			return $data;
		}
		$wpdb->show_errors( true );
	}

	return NULL;
}

function wc_autoship_cache_delete( $type, $age = 0, $customer_id = NULL, $schedule_id = NULL, $schedule_item_id = NULL ) {
	global $wpdb;
	if ( ! empty( $wpdb ) ) {
		$where = array(
			'TIMESTAMPDIFF(SECOND, cache_time, %s) > %d',
			'cache_type = %s'
		);
		$args = array(
			date( 'Y-m-d H:i:s' ), $age,
			$type
		);
		if ( ! empty( $customer_id ) ) {
			$where[] = 'cache_customer_id = %d';
			$args[] = $customer_id;
		}
		if ( ! empty( $schedule_id ) ) {
			$where[] = 'cache_schedule_id = %d';
			$args[] = $schedule_id;
		}
		if ( ! empty( $schedule_item_id ) ) {
			$where[] = 'cache_schedule_item_id = %d';
			$args[] = $schedule_item_id;
		}
		$wpdb->show_errors( false );
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}wc_autoship_cache WHERE " . implode( ' AND ', $where ),
			$args
		) );
		$wpdb->show_errors( true );
	}
}