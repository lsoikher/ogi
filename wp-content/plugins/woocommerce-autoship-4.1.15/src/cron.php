<?php

/**
 * Create an autoship order from an autoship schedule
 * @param int $schedule_id
 * @param array $items
 * @param bool $process_payment
 * @return int Order ID
 */
function wc_autoship_create_autoship_order( $schedule_id ) {
	global $wpdb;

	// Get schedule
	require_once ( WC_AUTOSHIP_SRC_DIR . '/Models/Schedule.php' );
	if ( ! WC_Autoship_Models_Schedule::id_exists( $schedule_id ) ) {
		throw new Exception( __( 'The Autoship Schedule does not exist.', 'wc-autoship' ) );
	}
	$schedule = new WC_Autoship_Models_Schedule( $schedule_id );
	// Get items
	$items = $schedule->get_items();
	// Get customer
	$customer = $schedule->get_customer();

	// Declare order vars
	$order_id = 0;
	$order = NULL;

	try {

		// No items error
		if ( count( $items ) < 1 ) {
			$schedule->set_autoship_status( WC_AUTOSHIP_STATUS_PAUSED );
			$schedule->save();
			throw new Exception( __( 'The Autoship Schedule does not contain any valid items to add.', 'wc-autoship') );
		}

		// No customer error
		if ( $customer == NULL ) {
			throw new Exception( __( 'The Customer for this Autoship Schedule does not exist.', 'wc-autoship' ) );
		}

		// Get payment token
		$payment_token = WC_Payment_Tokens::get( $schedule->get_payment_token_id() );
		if ( empty( $payment_token ) ) {
			throw new Exception( "Payment token not found" );
		}

		// Create Pipey client
		require_once( WC_AUTOSHIP_SRC_DIR . '/Pipey/Client.php' );
		require_once( WC_AUTOSHIP_SRC_DIR . '/Pipey/Exception.php' );
		$pipey = new WC_Autoship_Pipey_Client();
		// Create cart
		$pipey->login( $customer->ID );
		// Add items
		foreach ( $items as $item ) {
			$pipey->add_item( $item->get_product_id(), $item->get_variation_id(), $item->get_quantity(), $schedule->get_autoship_frequency() );
		}
		// Coupon
		$coupon_code = $schedule->get_coupon_code();
		if ( ! empty( $coupon_code ) && ! $schedule->coupon_is_expired() ) {
			$pipey->apply_coupon( $coupon_code );
		}
		// Checkout
		$checkout_result = $pipey->checkout( $customer->ID, $schedule->get_shipping_method_id(), $payment_token->get_gateway_id(), $payment_token->get_id() );
		if ( $checkout_result['result'] == 'success' ) {
			$query_string = parse_url( $checkout_result['redirect'], PHP_URL_QUERY );
			if ( $query_string !== false ) {
				// Get order ID
				$args = array( 'key' => '' );
				parse_str( $query_string, $args );
				$order_id = wc_get_order_id_by_order_key( $args['key'] );
				if ( ! empty( $order_id ) ) {
					// Get order
					$order = wc_get_order( $order_id );
					// Tag this as an autoship order
					update_post_meta( $order_id, '_wc_autoship_order', '1' );
					// Tag schedule ID
					update_post_meta( $order_id, '_wc_autoship_schedule_id', $schedule_id );
					// Add notes to order
					$order->add_order_note( __( 'Autoship order processed', 'wc-autoship' ), 0 );
					// Update schedule
					$autoship_frequency = $schedule->get_autoship_frequency();
					$next_order_time = strtotime( "+{$autoship_frequency} days", (int) current_time( 'timestamp' ) );
					$next_order_date = date( 'Y-m-d', $next_order_time );
					$schedule->set_next_order_date( $next_order_date );

					$now = time();
					$current_date = date( 'Y-m-d', $now );
					$schedule->set_last_order_date( $current_date );

					$schedule->save();
					// Run autoship order hooks
					do_action( 'wc_autoship_payment_complete', $order_id, $schedule_id );
				}
			}
		} else if ( isset( $checkout_result['messages'] ) ) {
			$messages = html_entity_decode( strip_tags( $checkout_result['messages'] ) );
			throw new Exception( $messages );
		} else {
			throw new Exception( __( "Error creating order!", 'wc-autoship' ) );
		}

		// If we got here, the order was created without problems!

	} catch ( Exception $e ) {
		// There was an error adding order data!

		// Pause schedule
		$schedule->set( 'autoship_status', WC_AUTOSHIP_STATUS_PAUSED );
		$schedule->save();
		$message = __( "Autoship order failed for Schedule {$schedule_id} for Customer {$customer->ID}", 'wc-autoship' );
		wc_autoship_log_action( 1, 'autoship_order_failed', $message, $_SERVER['REQUEST_URI'], $customer->ID, $schedule_id, null, $e->getMessage() );
		send_wc_autoship_error_email( $customer->ID, $e->getMessage() );

		// Update order status
		if ( $order !== NULL ) {
			$order->update_status(
				'failed', $e->getMessage()
			);
			$order->add_order_note(
				__( 'Autoship order failed. Autoship is paused. ' . $e->getMessage(), 'wc-autoship' ), 1
			);
		}

		throw $e;
	}

	return $order_id;
}

function wc_autoship_create_scheduled_autoship_orders() {
	if ( ! wc_autoship_process_autoship_orders_is_ok() ) {
		return;
	}

	$current_timestamp = (int) current_time( 'timestamp' );
	$processing_start_time = get_option( 'wc_autoship_processing_start_time' );
	if ( ! empty( $processing_start_time ) ) {
		$start_time = strtotime( $processing_start_time, $current_timestamp );
		if ( $current_timestamp < $start_time ) {
			return;
		}
	}
	$processing_end_time = get_option( 'wc_autoship_processing_end_time' );
	if ( ! empty( $processing_end_time ) ) {
		$end_time = strtotime( $processing_end_time, $current_timestamp );
		if ( $end_time < $current_timestamp ) {
			return;
		}
	}

	$semaphore = wc_autoship_get_cron_semaphore( 'cron-semaphore', WC_AUTOSHIP_BATCH_INTERVAL );
	if ( ! $semaphore ) {
		return;
	}

	require_once ( WC_AUTOSHIP_SRC_DIR . '/Models/Schedule.php' );
	$ids = WC_Autoship_Models_Schedule::get_pending_autoship_schedule_ids();
	foreach ( $ids as $id ) {
		try {
			wc_autoship_create_autoship_order( $id );
		} catch ( Exception $e ) {}
	}
	wc_autoship_release_cron_semaphore( $semaphore );
}
add_action( 'wc_autoship_create_autoship_orders', 'wc_autoship_create_scheduled_autoship_orders' );

function wc_autoship_create_scheduled_autoship_orders_ajax() {
	if ( current_user_can( 'manage_woocommerce' ) ) {
		wc_autoship_create_scheduled_autoship_orders();
		die( "done\n" );
	}
	die( "access denied\n" );
}
add_action( 'wp_ajax_create_autoship_orders', 'wc_autoship_create_scheduled_autoship_orders_ajax' );

function wc_autoship_get_cron_semaphore( $filename, $interval, $wait = 0 ) {
	// Semaphore dir
	$dir = WC_AUTOSHIP_SEMAPHORE_DIR;
	if ( ! file_exists( $dir ) ) {
		// Create dir
		if ( ! mkdir( $dir ) ) {
			// Log mkdir error
			$description = "Could not create directory $dir";
			wc_autoship_log_action( 1, 'cron_semaphore_mkdir', $description, $_SERVER['REQUEST_URI'] );
			return false;
		}
	}
	$file = $dir . '/' . $filename;
	// Get lock file
	$lock_start_time = time();
	$lock_file = wc_autoship_lock_file( $file );
	if ( $wait > 0 ) {
		while ( empty( $lock_file ) && ( time() - $lock_start_time ) < $wait ) {
			usleep(100000);
			$lock_file = wc_autoship_lock_file( $file );
		}
	}
	if ( empty( $lock_file ) ) {
		$description = "Could not lock semaphore '$filename'";
		wc_autoship_log_action( 1, 'cron_semaphore_lock', $description, $_SERVER['REQUEST_URI'] );
		return false;
	}
	// Get semaphore file
	$data_fp = @fopen( $file, 'c+' );
	if ( ! $data_fp ) {
		$description = "Could not open semaphore '$filename'";
		wc_autoship_log_action( 1, 'cron_semaphore_open', $description, $_SERVER['REQUEST_URI'] );
		return false;
	}
	
	$contents = @stream_get_contents( $data_fp );
	if ( $contents ) {
		$timestamp = (int) $contents;
		$now = time();
		$time_diff = ( $now - $timestamp );
		if ( $time_diff < $interval ) {
			rmdir( $lock_file );
			@fclose( $data_fp );
			return false;
		}
	}
	$semaphore = array(
		'lock_file' => $lock_file,
		'data_fp' => $data_fp
	);
	return $semaphore;
}

function wc_autoship_release_cron_semaphore( $semaphore ) {
	if ( ! empty( $semaphore ) ) {
		@ftruncate( $semaphore['data_fp'], 0 );
		@fseek( $semaphore['data_fp'], 0 );
		$timestamp = (string) time();
		$length = @fwrite( $semaphore['data_fp'], $timestamp );
		if ( $length < 1 ) {
			$description = "Could not write semaphore timestamp";
			wc_autoship_log_action( 1, 'cron_semaphore_timestamp', $description, $_SERVER['REQUEST_URI'] );
		}
		@fclose( $semaphore['data_fp'] );
		@rmdir( $semaphore['lock_file'] );
	} else {
		$description = "Semaphore is empty";
		wc_autoship_log_action( 1, 'cron_semaphore_empty', $description, $_SERVER['REQUEST_URI'] );
	}
}

function wc_autoship_lock_file( $filename ) {
	$lock_filename = $filename . '.lock';
	if ( file_exists( $lock_filename ) ) {
		if ( ( time() - filectime( $lock_filename ) ) < 60 ) {
			return null;
		}
		@rmdir( $lock_filename );
	}
	if ( @mkdir( $lock_filename ) ) {
		return $lock_filename;
	}
	return null;
}

/**
 * Add cron intervals for autoship
 * @param array $schedules
 * @return array
 */
function wc_autoship_add_cron_intervals( $schedules ) {
	$schedules['wc_autoship_batch'] = array(
		'interval' => WC_AUTOSHIP_BATCH_INTERVAL,
		'display' => __( 'Autoship batch interval', 'wc-autoship' )
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'wc_autoship_add_cron_intervals' );