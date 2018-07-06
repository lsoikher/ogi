<?php

function wc_autoship_activate() {
	// Default settings
	$settings = wc_autoship_default_settings();
	foreach ( $settings as $name => $value ) {
		add_option( $name, $value );
	}
	// Create tables
	wc_autoship_create_tables();
	// Schedule tasks
	wc_autoship_unschedule_autoship();
	wc_autoship_schedule_autoship();
	// Create keys
	wc_autoship_create_pipey_key();
	// Flush rewrite rules
	flush_rewrite_rules();
}
register_activation_hook( WC_AUTOSHIP_PLUGIN_FILE, 'wc_autoship_activate' );

function wc_autoship_deactivate() {
	wc_autoship_unschedule_autoship();
}
register_deactivation_hook( WC_AUTOSHIP_PLUGIN_FILE, 'wc_autoship_deactivate' );

function wc_autoship_uninstall() {

}
register_uninstall_hook( WC_AUTOSHIP_PLUGIN_FILE, 'wc_autoship_uninstall' );

function wc_autoship_default_settings() {
	$default_settings = array(

	);
	return $default_settings;
}

function wc_autoship_create_tables() {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$prefix = $wpdb->prefix . 'wc_autoship_';

	$wpdb->hide_errors();

	// Schedules
	$status_active = WC_AUTOSHIP_STATUS_ACTIVE;
	$create_sql =
		"CREATE TABLE {$prefix}schedules (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		customer_id BIGINT(20) UNSIGNED NOT NULL,
		autoship_frequency INT UNSIGNED NOT NULL,
		autoship_status TINYINT(1) DEFAULT {$status_active},
		last_order_date DATE NOT NULL,
		next_order_date DATE NOT NULL,
		shipping_method_id VARCHAR(50) DEFAULT NULL,
		payment_token_id VARCHAR(50) DEFAULT NULL,
		coupon VARCHAR(255) DEFAULT NULL,
		created_time DATETIME NOT NULL,
		modified_time DATETIME NOT NULL,
		PRIMARY KEY  (id)
		);";
	dbDelta( $create_sql );

	// Schedule items
	$create_sql =
		"CREATE TABLE {$prefix}schedule_items (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		schedule_id BIGINT(20) UNSIGNED NOT NULL,
		product_id BIGINT(20) UNSIGNED NOT NULL,
		variation_id BIGINT(20) UNSIGNED DEFAULT NULL,
		qty TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
		created_time DATETIME NOT NULL,
		modified_time DATETIME NOT NULL,
		PRIMARY KEY  (id)
		);";
	dbDelta( $create_sql );

	// Autoship log
	$create_sql =
		"CREATE TABLE {$prefix}log (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		user_id BIGINT(20) UNSIGNED NOT NULL,
		action_time DATETIME NOT NULL,
		action_type VARCHAR(50) NOT NULL,
		action_customer_id BIGINT(20) UNSIGNED DEFAULT NULL,
		action_schedule_id BIGINT(20) UNSIGNED DEFAULT NULL,
		action_schedule_item_id BIGINT(20) UNSIGNED DEFAULT NULL,
		action_value VARCHAR(255) DEFAULT NULL,
		action_description VARCHAR(255) NOT NULL,
		action_url VARCHAR(255) NOT NULL,
		PRIMARY KEY  (id)
		);";
	dbDelta( $create_sql );

	// Autoship cache
	$create_sql =
		"CREATE TABLE {$prefix}cache (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		cache_time DATETIME NOT NULL,
		cache_type VARCHAR(50) NOT NULL,
		cache_value TEXT NOT NULL,
		cache_customer_id BIGINT(20) UNSIGNED DEFAULT NULL,
		cache_schedule_id BIGINT(20) UNSIGNED DEFAULT NULL,
		cache_schedule_item_id BIGINT(20) UNSIGNED DEFAULT NULL,
		PRIMARY KEY  (id)
		);";
	dbDelta( $create_sql );

	$wpdb->show_errors();

	update_option( 'wc_autoship_db_version', WC_AUTOSHIP_VERSION );
}

function wc_autoship_schedule_autoship() {
	wp_schedule_event(
		time() + 60,
		'wc_autoship_batch',
		'wc_autoship_create_autoship_orders'
	);

	wp_schedule_event(
		time() + 60,
		'daily',
		'wc_autoship_notify_10day'
	);

	wp_schedule_event(
		time() + 60,
		'daily',
		'wc_autoship_notify_1day'
	);

	wp_schedule_event(
		time() + 60,
		'daily',
		'wc_autoship_update_license_key_status'
	);

	update_option( 'wc_autoship_cron_version', WC_AUTOSHIP_VERSION );
}

function wc_autoship_unschedule_autoship() {
	wp_clear_scheduled_hook( 'wc_autoship_create_autoship_orders' );
	wp_unschedule_event( wp_next_scheduled( 'wc_autoship_create_autoship_orders' ), 'wc_autoship_create_autoship_orders' );

	wp_clear_scheduled_hook( 'wc_autoship_updated' );
	wp_unschedule_event( wp_next_scheduled( 'wc_autoship_updated' ), 'wc_autoship_updated' );

	wp_clear_scheduled_hook( 'wc_autoship_notify_10day' );
	wp_unschedule_event( wp_next_scheduled( 'wc_autoship_notify_10day' ), 'wc_autoship_notify_10day' );

	wp_clear_scheduled_hook( 'wc_autoship_notify_1day' );
	wp_unschedule_event( wp_next_scheduled( 'wc_autoship_notify_1day' ), 'wc_autoship_notify_1day' );

	// Deprecated legacy event
	wp_clear_scheduled_hook( 'wc_autoship_export_data' );
	wp_unschedule_event( wp_next_scheduled( 'wc_autoship_export_data' ), 'wc_autoship_export_data' );

	wp_clear_scheduled_hook( 'wc_autoship_update_license_key_status' );
	wp_unschedule_event( wp_next_scheduled( 'wc_autoship_update_license_key_status' ), 'wc_autoship_update_license_key_status' );

	delete_option( 'wc_autoship_cron_version' );
}

function wc_autoship_upgrade_db() {
	wc_autoship_upgrade_db_3_0_0();
	wc_autoship_upgrade_db_3_1_11();
	wc_autoship_upgrade_db_3_1_17();
	wc_autoship_upgrade_db_3_2_0();
	wc_autoship_upgrade_db_3_2_2();
	wc_autoship_upgrade_db_4_0_0();
	wc_autoship_upgrade_db_4_0_1();
	wc_autoship_upgrade_db_4_0_3();
}

function wc_autoship_upgrade_db_is_required( $target_db_version ) {
	$current_db_version = get_option( 'wc_autoship_db_version' );
	if ( ! empty( $current_db_version ) && version_compare( $current_db_version, $target_db_version, '>=' ) ) {
		return false;
	}
	return true;
}

function wc_autoship_upgrade_db_3_0_0() {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$wpdb->hide_errors();

	$target_db_version = '3.0.0';
	if ( ! wc_autoship_upgrade_db_is_required( $target_db_version ) ) {
		return;
	}

	// Drop keys
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_schedules DROP INDEX wc_autoship_user_option" );

	// Add columns
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_schedules ADD COLUMN autoship_frequency INT UNSIGNED NOT NULL" );

	// Migrate autoship options into existing products
	$autoship_options = wc_autoship_get_option( 'autoship_options' );
	if ( ! empty( $autoship_options ) ) {
		// Find min and max frequencies
		$autoship_min_frequency = 0;
		$autoship_max_frequency = 0;
		foreach ( $autoship_options as $option_id => $option ) {
			if ( $option['frequency'] > 6 && $option['frequency'] < 366 ) {
				if ( $autoship_min_frequency < 7 ) {
					$autoship_min_frequency = $option['frequency'];
				}
				if ( $autoship_max_frequency < 7 ) {
					$autoship_max_frequency = $option['frequency'];
				}
				$autoship_min_frequency = min( $autoship_min_frequency, $option['frequency'] );
				$autoship_max_frequency = max( $autoship_max_frequency, $option['frequency'] );
			}
		}
		if ( $autoship_min_frequency < 7 ) {
			$autoship_min_frequency = 7;
		}
		if ( $autoship_max_frequency < 7 ) {
			$autoship_max_frequency = 7;
		}
		// Get existing products
		$products_query_args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => '_wc_autoship_enable_autoship',
					'compare' => 'NOT EXISTS'
				)
			)
		);
		$products_result = get_posts( $products_query_args );
		foreach ( $products_result as $post ) {
			// Enable autoship for product
			add_post_meta( $post->ID, '_wc_autoship_enable_autoship', 'yes', true );
			// Set min and max frequencies
			add_post_meta( $post->ID, '_wc_autoship_min_frequency', $autoship_min_frequency, true );
			add_post_meta( $post->ID, '_wc_autoship_max_frequency', $autoship_max_frequency, true );
		}

		// Migrate autoship schedules
		$schedules_result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wc_autoship_schedules" );
		foreach ( $schedules_result as $schedule_row ) {
			if ( isset( $autoship_options[ $schedule_row->autoship_option_id ] ) ) {
				$wpdb->update(
					"{$wpdb->prefix}wc_autoship_schedules",
					array( 'autoship_frequency' => $autoship_options[ $schedule_row->autoship_option_id ]['frequency'] ),
					array( 'id' => $schedule_row->id )
				);
			}
		}
	}

	// Drop columns
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_customers DROP COLUMN shipping_first_name" );
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_customers DROP COLUMN shipping_last_name" );
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_customers DROP COLUMN shipping_company" );
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_customers DROP COLUMN shipping_address_1" );
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_customers DROP COLUMN shipping_address_2" );
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_customers DROP COLUMN shipping_city" );
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_customers DROP COLUMN shipping_postcode" );
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_customers DROP COLUMN shipping_country" );
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_customers DROP COLUMN shipping_state" );
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_schedules DROP COLUMN autoship_option_id" );
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_schedule_items DROP COLUMN customer_label" );

	// Add keys
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_schedules ADD UNIQUE KEY wc_autoship_customer_frequency (customer_id, autoship_frequency)" );

	$wpdb->show_errors();

	// Update database version
	update_option( 'wc_autoship_db_version', $target_db_version );
}

function wc_autoship_upgrade_db_3_1_11() {
	$target_db_version = '3.1.11';
	if ( ! wc_autoship_upgrade_db_is_required( $target_db_version ) ) {
		return;
	}

	wc_autoship_create_tables();
}

function wc_autoship_upgrade_db_3_1_17() {
	$target_db_version = '3.1.17';
	if ( ! wc_autoship_upgrade_db_is_required( $target_db_version ) ) {
		return;
	}

	wc_autoship_create_tables();
}

function wc_autoship_upgrade_db_3_2_0() {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$wpdb->hide_errors();

	$target_db_version = '3.2.0';
	if ( ! wc_autoship_upgrade_db_is_required( $target_db_version ) ) {
		return;
	}

	// Drop unique keys
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_schedules DROP INDEX wc_autoship_customer_frequency" );
	// Drop legacy unique key
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_schedules DROP INDEX wc_autoship_user_option" );

	// Update database version
	update_option( 'wc_autoship_db_version', $target_db_version );
}

function wc_autoship_upgrade_db_3_2_2() {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$wpdb->hide_errors();

	$target_db_version = '3.2.2';
	if ( ! wc_autoship_upgrade_db_is_required( $target_db_version ) ) {
		return;
	}

	// Add columns
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_schedules ADD COLUMN coupon VARCHAR(255) DEFAULT NULL" );

	// Update database version
	update_option( 'wc_autoship_db_version', $target_db_version );
}

function wc_autoship_upgrade_db_4_0_0() {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$wpdb->hide_errors();

	$target_db_version = '4.0.0';
	if ( ! wc_autoship_upgrade_db_is_required( $target_db_version ) ) {
		return;
	}

	// Add columns
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_schedules ADD COLUMN payment_token_id VARCHAR(50) DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_schedules CHANGE shipping_method shipping_method_id VARCHAR(50) DEFAULT NULL" );

	// Update database version
	update_option( 'wc_autoship_db_version', $target_db_version );
}

function wc_autoship_upgrade_db_4_0_1() {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$wpdb->hide_errors();

	$target_db_version = '4.0.1';
	if ( ! wc_autoship_upgrade_db_is_required( $target_db_version ) ) {
		return;
	}

	// Change columns
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_autoship_log CHANGE action_value action_value VARCHAR(255) DEFAULT NULL" );

	// Update database version
	update_option( 'wc_autoship_db_version', $target_db_version );
}

function wc_autoship_upgrade_db_4_0_3() {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$wpdb->hide_errors();

	$target_db_version = '4.0.3';
	if ( ! wc_autoship_upgrade_db_is_required( $target_db_version ) ) {
		return;
	}

	// Autoship cache
	$create_sql =
		"CREATE TABLE {$wpdb->prefix}wc_autoship_cache (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		cache_time DATETIME NOT NULL,
		cache_type VARCHAR(50) NOT NULL,
		cache_value TEXT NOT NULL,
		cache_customer_id BIGINT(20) UNSIGNED DEFAULT NULL,
		cache_schedule_id BIGINT(20) UNSIGNED DEFAULT NULL,
		cache_schedule_item_id BIGINT(20) UNSIGNED DEFAULT NULL,
		PRIMARY KEY  (id)
		);";
	dbDelta( $create_sql );

	// Update database version
	update_option( 'wc_autoship_db_version', $target_db_version );
}

function wc_autoship_get_system_user() {
	$user_id = get_option( 'wc_autoship_system_user_id', null );
	if ( $user_id == null ) {
		return null;
	}
	$user = new WP_User( $user_id );
	if ( $user->exists() ) {
		return $user;
	}
	return null;
}

function wc_autoship_create_system_user() {
	// Get role
	$role_name = 'administrator';

	// Create user
	$username = 'wc_autoship_system_' . time();
	$password = wp_generate_password( 32, true, false );
	$user_id = wp_create_user( $username, $password );
	if ( ! is_wp_error( $user_id ) ) {
		// Store user
		update_option( 'wc_autoship_system_user_id', $user_id );
		// Add user to role
		wp_update_user( array(
			'ID' => $user_id,
			'role' => $role_name
		) );
		// Create API keys
		wc_autoship_create_api_keys( $user_id );
	}

	return $user_id;
}

function wc_autoship_get_api_keys() {
	return get_option( 'wc_autoship_system_user_api_keys' );
}

function wc_autoship_create_api_keys( $user_id ) {
	global $wpdb;

	$description = sprintf( __( 'WC Autoship - API (created on %s at %s).', 'wc-autoship' ), date_i18n( wc_date_format() ), date_i18n( wc_time_format() ) );

	// Created API keys.
	$permissions     = 'read_write';
	$consumer_key    = 'ck_' . wc_rand_hash();
	$consumer_secret = 'cs_' . wc_rand_hash();

	$wpdb->insert(
		$wpdb->prefix . 'woocommerce_api_keys',
		array(
			'user_id'         => $user_id,
			'description'     => $description,
			'permissions'     => $permissions,
			'consumer_key'    => wc_api_hash( $consumer_key ),
			'consumer_secret' => $consumer_secret,
			'truncated_key'   => substr( $consumer_key, -7 )
		),
		array(
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s'
		)
	);

	$api_keys = array(
		'key_id'          => $wpdb->insert_id,
		'user_id'         => $user_id,
		'consumer_key'    => $consumer_key,
		'consumer_secret' => $consumer_secret,
		'key_permissions' => $permissions
	);
	update_option( 'wc_autoship_system_user_api_keys', $api_keys );
	return $api_keys;
}

function wc_autoship_get_pipey_key() {
	return get_option( 'wc_autoship_pipey_key', null );
}

function wc_autoship_create_pipey_key() {
	// Generate key
	$key = wp_generate_password( 32, true, false );
	// Store key
	update_option( 'wc_autoship_pipey_key', $key );
	// Return key
	return $key;
}

/**
 * @deprecated use wc_autoship_get_pipey_ip_list()
 */
function wc_autoship_get_pipey_ip() {
	if ( strlen( WC_AUTOSHIP_PIPEY_IP ) > 0 ) {
		return WC_AUTOSHIP_PIPEY_IP;
	}
	
	return get_option( 'wc_autoship_pipey_ip', null );
}

function wc_autoship_get_pipey_ip_list() {
	$ip_string = wc_autoship_get_pipey_ip();
	if ( $ip_string == null ) {
		return array();
	} else {
		return explode( ",", $ip_string );
	}
}

function wc_autoship_get_licensing_url() {
	return 'https://wooautoship.com';
}

