<?php

function wc_autoship_admin_enqueue_css() {
	global $wp_scripts;
	$url_path = plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE );

	if ( isset( $_REQUEST['page'] ) && strpos( $_REQUEST['page'], 'wc_autoship' ) !== false ) {
		$jquery_version = '';
		if ( isset( $wp_scripts->registered['jquery-ui-core']->ver ) ) {
			$jquery_version = $wp_scripts->registered['jquery-ui-core']->ver;
		} else {
			$jquery_version = '1.9.2';
		}
		wp_enqueue_style( 'jquery-ui-style',
			'//ajax.googleapis.com/ajax/libs/jqueryui/'
			. $jquery_version
			. '/themes/smoothness/jquery-ui.css'
		);
	}

	wp_enqueue_style( 'pickaday', $url_path . 'styles/pickaday.css', array(), WC_AUTOSHIP_VERSION );
	// Admin style
	wp_enqueue_style( 'wc-autoship-admin-style', $url_path . 'styles/admin-style.css', array(), WC_AUTOSHIP_VERSION );
}
add_action( 'admin_enqueue_scripts', 'wc_autoship_admin_enqueue_css' );

function wc_autoship_admin_enqueue_js() {
	if ( ! isset( $_REQUEST['page'] ) || strpos( $_REQUEST['page'], 'wc_autoship' ) === false ) {
		return;
	}

	$url_path = plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE );

	// Admin scripts
	wp_enqueue_script( 'moment', $url_path . 'js/moment.js', array(), WC_AUTOSHIP_VERSION );
	wp_enqueue_script( 'pickaday', $url_path . 'js/pickaday.js', array('moment'), WC_AUTOSHIP_VERSION );
	wp_enqueue_script( 'wc-autoship-datepicker', $url_path . 'js/datepicker.js', array( 'pickaday', 'jquery' ), WC_AUTOSHIP_VERSION );
	wp_enqueue_script( 'wc-autoship-admin-scripts', $url_path . 'js/admin/scripts.js', array( 'jquery' ), WC_AUTOSHIP_VERSION );
}
add_action( 'admin_enqueue_scripts', 'wc_autoship_admin_enqueue_js' );

function wc_autoship_admin_menu() {
	// Autoship Analytics Menu
	add_menu_page( 'Autoship', 'Autoship', 'manage_woocommerce', 'wc_autoship', null, 'dashicons-backup' );
	add_submenu_page( 'wc_autoship', 'Autoship Customers', 'Customers', 'manage_woocommerce', 'wc_autoship', 'wc_autoship_admin_render_customers_page' );
	add_submenu_page( 'wc_autoship', 'Autoship Orders', 'Orders', 'manage_woocommerce', 'wc_autoship_orders', 'wc_autoship_admin_render_orders_page' );
	add_submenu_page( 'wc_autoship', 'Autoship Schedules', 'Schedules', 'manage_woocommerce', 'wc_autoship_schedules', 'wc_autoship_admin_render_schedules_page' );
	add_submenu_page( 'wc_autoship', 'Autoship Items', 'Items', 'manage_woocommerce', 'wc_autoship_items', 'wc_autoship_admin_render_items_page' );
	add_submenu_page( 'wc_autoship', 'Autoship Log', 'Log', 'manage_woocommerce', 'wc_autoship_log', 'wc_autoship_admin_render_log_page' );
}
add_action( 'admin_menu', 'wc_autoship_admin_menu' );

function wc_autoship_admin_render_customers_page() {
	echo '<div class="wrap">';

	// Show autoship customers list
	require_once('Admin/CustomersListTable.php');
	$list_table = new WC_Autoship_Admin_CustomersListTable();
	$list_table->prepare_items();
	wc_autoship_include_template( 'admin/autoship-customers/customers-list', array(
		'list_table' => $list_table
	) );

	echo '</div>';
}

function wc_autoship_admin_render_orders_page() {
	echo '<div class="wrap">';

	require_once('Admin/OrdersListTable.php');
	$list_table = new WC_Autoship_Admin_OrdersListTable();
	$list_table->prepare_items();
	wc_autoship_include_template( 'admin/autoship-orders/orders-list', array(
		'list_table' => $list_table
	) );

	echo '</div>';
}

function wc_autoship_admin_render_schedules_page() {
	echo '<div class="wrap">';

	require_once('Admin/SchedulesListTable.php');
	$list_table = new WC_Autoship_Admin_SchedulesListTable();
	$list_table->prepare_items();
	wc_autoship_include_template( 'admin/autoship-schedules/schedules-list', array(
		'list_table' => $list_table
	) );

	echo '</div>';
}

function wc_autoship_admin_render_items_page() {
	echo '<div class="wrap">';

	require_once('Admin/ItemsListTable.php');
	$list_table = new WC_Autoship_Admin_ItemsListTable();
	$list_table->prepare_items();
	wc_autoship_include_template( 'admin/autoship-items/items-list', array(
		'list_table' => $list_table
	) );

	echo '</div>';
}

function wc_autoship_admin_render_log_page() {
	echo '<div class="wrap">';

	require_once('Admin/LogListTable.php');
	$list_table = new WC_Autoship_Admin_LogListTable();
	$list_table->prepare_items();
	wc_autoship_include_template( 'admin/autoship-log/log-list', array(
		'list_table' => $list_table
	) );

	echo '</div>';
}

function wc_autoship_admin_render_user_autoship_profile_section( $profileuser ) {
	if ( user_can( get_current_user_id(), 'manage_woocommerce' ) ) {
		wc_autoship_include_template( 'admin/user/autoship-profile-section', array(
			'user' => $profileuser
		) );
	}
}
add_action( 'edit_user_profile', 'wc_autoship_admin_render_user_autoship_profile_section' );

function wc_autoship_admin_ajax_export_autoship_customers() {
	if ( ! user_can( get_current_user_id(), 'manage_woocommerce' ) ) {
		echo "Action not allowed.";
		die();
	}
	require_once('Admin/CustomersListTable.php');
	$list_table = @new WC_Autoship_Admin_CustomersListTable();
	$list_table->prepare_items();
	wc_autoship_admin_export_items_file( $list_table->items, 'autoship_customers.csv' );
}
add_action( 'wp_ajax_export_autoship_customers', 'wc_autoship_admin_ajax_export_autoship_customers' );

function wc_autoship_admin_ajax_export_autoship_orders() {
	if ( ! user_can( get_current_user_id(), 'manage_woocommerce' ) ) {
		echo "Action not allowed.";
		die();
	}
	require_once('Admin/OrdersListTable.php');
	$list_table = @new WC_Autoship_Admin_OrdersListTable();
	$list_table->prepare_items();
	wc_autoship_admin_export_items_file( $list_table->items, 'autoship_orders.csv' );
}
add_action( 'wp_ajax_export_autoship_orders', 'wc_autoship_admin_ajax_export_autoship_orders' );

function wc_autoship_admin_ajax_export_autoship_schedules() {
	if ( ! user_can( get_current_user_id(), 'manage_woocommerce' ) ) {
		echo "Action not allowed.";
		die();
	}
	require_once('Admin/SchedulesListTable.php');
	$list_table = @new WC_Autoship_Admin_SchedulesListTable();
	$list_table->prepare_items();
	wc_autoship_admin_export_items_file( $list_table->items, 'autoship_schedules.csv' );
}
add_action( 'wp_ajax_export_autoship_schedules', 'wc_autoship_admin_ajax_export_autoship_schedules' );

function wc_autoship_admin_ajax_export_autoship_items() {
	if ( ! user_can( get_current_user_id(), 'manage_woocommerce' ) ) {
		echo "Action not allowed.";
		die();
	}
	require_once('Admin/ItemsListTable.php');
	$list_table = @new WC_Autoship_Admin_ItemsListTable();
	$list_table->prepare_items();
	wc_autoship_admin_export_items_file( $list_table->items, 'autoship_items.csv' );
}
add_action( 'wp_ajax_export_autoship_items', 'wc_autoship_admin_ajax_export_autoship_items' );

function wc_autoship_admin_ajax_export_autoship_log() {
	if ( ! user_can( get_current_user_id(), 'manage_woocommerce' ) ) {
		echo "Action not allowed.";
		die();
	}
	require_once('Admin/LogListTable.php');
	$list_table = @new WC_Autoship_Admin_LogListTable();
	$list_table->prepare_items();
	wc_autoship_admin_export_items_file( $list_table->items, 'autoship_log.csv' );
}
add_action( 'wp_ajax_export_autoship_log', 'wc_autoship_admin_ajax_export_autoship_log' );

function wc_autoship_admin_export_items_file( $items, $filename ) {
	header( 'Content-Type: text/csv' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	$out = fopen( 'php://output', 'r' );
	if ( count( $items ) > 0 ) {
		$first_row = get_object_vars( $items[0] );
		$column_names = array_keys( $first_row );
		fputcsv( $out, $column_names );
		foreach ( $items as $item ) {
			$row = get_object_vars( $item );
			fputcsv( $out, $row );
		}
	}
	fclose( $out );
	die();
}

function wc_autoship_admin_add_dashboard_widgets() {
	wp_add_dashboard_widget(
		'wc_autoship_activity', // Widget slug.
		'Autoship Activity', // Title.
		'wc_autoship_admin_display_autoship_activity_dashboard_widget' // Display function.
	);

	$site_is_registered = get_option( 'wc_autoship_analytics_site_is_registered' );
	if ( empty( $site_is_registered ) || 'yes' != $site_is_registered ) {
		wp_add_dashboard_widget(
			'wc_autoship_analytics', // Widget slug.
			'Autoship Analytics', // Title.
			'wc_autoship_admin_display_analytics_dashboard_widget' // Display function.
		);
	}
}
add_action( 'wp_dashboard_setup', 'wc_autoship_admin_add_dashboard_widgets' );

function wc_autoship_admin_display_autoship_activity_dashboard_widget() {
	global $wpdb;

	$wpdb->show_errors( false );
	$activity_results = $wpdb->get_results(
		"SELECT id, action_time, action_type, action_description, action_value
			FROM {$wpdb->prefix}wc_autoship_log
			ORDER BY action_time DESC
			LIMIT 0, 15"
	);
	$wpdb->show_errors( true );

	if ( ! empty( $activity_results ) ) {
		echo '<ul class="wc-autoship-activity-list">';
		foreach ( $activity_results as $activity ) {
			$log_item_class = preg_replace( '/[^A-Za-z0-9]/', '-', $activity->action_type );
			if ( ! is_null( $activity->action_value ) && strlen( $activity->action_value ) < 20 ) {
				$log_item_class .= ' ' . $log_item_class . '-' . preg_replace( '/[^A-Za-z0-9]/', '-', $activity->action_value );
			}
			echo "<li class=\"wc-autoship-activity-list-item wc-autoship-log-item $log_item_class\">";
			echo '<span class="action-time">', date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $activity->action_time ) ), '</span>';
			echo '&nbsp;&mdash;&nbsp;';
			echo '<span class="action-description">', esc_html( __( $activity->action_description, 'wc-autoship' ) ), '</span>';
			echo '</li>';
		}
		echo '</ul>';
	} else {
		echo '<p>', __( 'No activity, yet.', 'wc-autoship' ), '</p>';
	}
}

function wc_autoship_admin_display_analytics_dashboard_widget() {
	wc_autoship_include_template( 'admin/widgets/analytics-dashboard' );
}

function wc_autoship_get_messages() {
	if ( empty( $_COOKIE['wc_autoship_messages'] ) ) {
		return array();
	}
	$messages = unserialize( base64_decode( $_COOKIE['wc_autoship_messages'] ) );
	return $messages;
}

function wc_autoship_add_message( $message, $type = 'updated' ) {
	if ( is_admin() && did_action( 'admin_notices' ) && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) ) {
		// Admin notices have already been polled, so inject the message into the admin page
		$messages = array( array( 'message' => $message, 'type'    => $type ) );
		wc_autoship_include_template( 'admin-messages-inject', array( 'messages' => $messages ) );
		return;
	}

	$messages = wc_autoship_get_messages();

	if ( count( $messages ) > 4 ) {
		// Message limit has been reached
		$message = __( 'WC Autoship message limit reached. Some messages are not shown.', 'wc-autoship' );
		$type = 'error';
	}

	foreach ( $messages as $existing_message ) {
		if ( $existing_message['message'] == $message && $existing_message['type'] == $type ) {
			// Message already exists
			return;
		}
	}

	$messages[] = array(
		'message' => $message,
		'type'    => $type
	);

	$messages_cookie = base64_encode( serialize( $messages ) );
	setcookie( 'wc_autoship_messages', $messages_cookie, time() + 30 );
	$_COOKIE['wc_autoship_messages'] = $messages_cookie;
}

function wc_autoship_print_messages() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	$messages = wc_autoship_get_messages();
	if ( empty( $messages ) ) {
		return;
	}

	foreach ( $messages as $message ) {
		?>
		<div class="notice is-dismissible <?php echo esc_attr( $message['type'] ); ?>">
			<p><?php echo $message['message']; ?></p>
		</div>
		<?php
	}
	if ( headers_sent() ) {
		echo '<script>wc_autoshipDeleteMessages()</script>';
	} else {
		setcookie( 'wc_autoship_messages', '', time() - 3600 );
	}
	$_COOKIE['wc_autoship_messages'] = '';
}
add_action( 'admin_notices', 'wc_autoship_print_messages' );

/**
 * Get system status messages for warnings
 * @return array<string, string>
 */
function wc_autoship_get_system_warnings() {
	if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
		return;
	}

	$woocommerce = WC();

	$status = array();

	// Check enabled payment gateways
	$wc_autoship_payment_gateway_count = 0;
	foreach ( $woocommerce->payment_gateways->payment_gateways() as $gateway ) {
		if ( $gateway->get_option( 'enabled' ) == 'yes' ) {
			if ( $gateway->supports('tokenization') ) {
				$wc_autoship_payment_gateway_count++;
			}
		}
	}
	if ( $wc_autoship_payment_gateway_count < 1 ) {
		$status['payment_gateway'] = __(
			'No <a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">payment gateways</a> '
			. 'with support for tokens are enabled for WC Autoship.',
			'wc-autoship'
		);
	}

	// Upgrade DB
	if ( wc_autoship_upgrade_db_is_required( '4.0.3' ) ) {
		$status['upgrade_db'] = __(
			'A WC Auto-Ship database upgrade is required! Warning: Please back up your database before proceeding. '
			. '<a href="' . admin_url( 'admin-ajax.php?action=wc_autoship_upgrade_db' ) . '" class="button">Upgrade now</a>',
			'wc-autoship'
		);
	}

	// Pipey key
	$pipey_key = wc_autoship_get_pipey_key();
	if ( empty( $pipey_key ) ) {
		$status['pipey_key'] = __(
			'WC Auto-Ship requires key configuration! '
			. '<a href="' . admin_url( 'admin-ajax.php?action=wc_autoship_create_pipey_key' ) . '" class="button">Configure now!</a>',
			'wc-autoship'
		);
	}

	// Pipey ip
	$pipey_ip = wc_autoship_get_pipey_ip();
	if ( empty( $pipey_ip ) ) {
		$nonce = wp_hash( 'wc_autoship_get_pipey_ip_nonce' . time() );
		update_option( 'wc_autoship_get_pipey_ip_nonce', $nonce );
		$status['pipey_ip'] = __(
			'WC Auto-Ship requires client configuration! '
			. '<a href="' . admin_url( 'admin-ajax.php?action=wc_autoship_create_pipey_ip&nonce=' . urlencode( $nonce ) ) . '" class="button">Configure now!</a>',
			'wc-autoship'
		);
	}

	// License key
	$license_key = get_option( 'wc_autoship_license_key' );
	$license_key_validated = get_option( 'wc_autoship_license_key_validated' );
	$license_key_expires = strtotime( get_option( 'wc_autoship_license_key_expires' ) );
	if ( ! empty( $license_key ) && ! empty( $license_key_validated )
		&& $license_key_expires < time() ) {
		$status['license_key'] = __(
			'Your <a href="' . admin_url( 'admin.php?page=wc-settings&tab=wc_autoship' ) . '">'
			. 'WC Autoship License Key</a> expired on '
			. date( get_option( 'date_format' ), $license_key_expires ) . '. '
			. 'Please visit <a href="http://wooautoship.com">http://wooautoship.com</a> '
			. 'to renew your license and receive core updates.',
			'wc-autoship'
		);
	} elseif ( empty( $license_key ) || empty( $license_key_validated )
		|| empty( $license_key_expires ) ) {
		$status['license_key'] = __(
			'Your <a href="' . admin_url( 'admin.php?page=wc-settings&tab=wc_autoship' ) . '">'
			. 'WC Autoship License Key</a> is invalid. '
			. 'Please visit <a href="http://wooautoship.com">http://wooautoship.com</a> '
			. 'to purchase a license and receive core updates.',
			'wc-autoship'
		);
	}

	// Process autoship orders
	if ( ! wc_autoship_process_autoship_orders_is_ok() ) {
		if ( ! wc_autoship_process_autoship_orders_site_is_ok() ) {
			$settings_url = admin_url( 'admin.php?page=wc-settings&tab=wc_autoship' );
			$status['process_autoship_orders_site'] = __(
				'The URL for this site has changed. '
				. "<a href=\"{$settings_url}\">Autoship Order Processing</a> has been disabled!",
				'wc-autoship'
			);
		} else if ( 'no' == get_option( 'wc_autoship_process_autoship_orders' ) ) {
			$settings_url = admin_url( 'admin.php?page=wc-settings&tab=wc_autoship' );
			$status['process_autoship_orders'] = __(
				"<a href=\"{$settings_url}\">Autoship Order Processing</a> is disabled!",
				'wc-autoship'
			);
		}
	}

	$php_version = phpversion();
	$min_php_version = '5.5.0';
	if ( version_compare( $php_version, $min_php_version, '<' ) ) {
		$status['php_version'] = __( "You are running PHP {$php_version} with WC Autoship. Please upgrade to PHP {$min_php_version} or higher to ensure compatibility.", 'wc-autoship' );
	}

	$cron_warning_dismissed = ( 'yes' == get_option( 'wc_autoship_wp_cron_warning_dismissed' ) );
	if ( defined( 'DISABLE_WP_CRON' ) && true == DISABLE_WP_CRON && ! $cron_warning_dismissed ) {
		$dismiss_url = admin_url( '/admin-ajax.php?action=wc_autoship_dismiss_wp_cron_warning' );
		$status['wp_cron'] = __( "The WP Cron is disabled by the constant DISABLE_WP_CRON in your wp-config.php settings. WC Autoship will not create orders while the WP Cron is disabled. <small><a href='$dismiss_url'>Permanently dismiss this warning.</a></small>", 'wc-autoship' );
	}

	// Check cron schedules
	$wc_autoship_cron_jobs = array(
		'wc_autoship_create_autoship_orders',
		'wc_autoship_notify_10day',
		'wc_autoship_notify_1day',
		'wc_autoship_update_license_key_status',
	);
	foreach( $wc_autoship_cron_jobs as $cron_job ) {
		$scheduled = wp_get_schedule( $cron_job );
		if ( ! $scheduled ) {
			$reset_cron_endpoint = admin_url( '/admin-ajax.php?action=wc_autoship_reset_cron' );
			$cron_misconfigured_message = __( sprintf( 'The WC Autoship Cron is misconfigured. <a href="%s">Reset cron</a>', esc_attr( $reset_cron_endpoint ) ), 'wc-autoship' );
			$status['wp_cron_configuration'] = $cron_misconfigured_message;
			break;
		}
	}

	return $status;
}

/**
 * The site can process autoship orders
 * @return bool
 */
function wc_autoship_process_autoship_orders_is_ok() {
	$process_autoship_orders = get_option( 'wc_autoship_process_autoship_orders' );

	if ( empty( $process_autoship_orders ) ) {
		add_option( 'wc_autoship_process_autoship_orders', 'yes' );
		return wc_autoship_process_autoship_orders_site_is_ok();
	}

	if ( 'yes' == $process_autoship_orders ) {
		return wc_autoship_process_autoship_orders_site_is_ok();
	}

	return false;
}

/**
 * The site has been enabled to process autoship orders
 * @return bool
 */
function wc_autoship_process_autoship_orders_site_is_ok() {
	$process_autoship_orders_site = get_option( 'wc_autoship_process_autoship_orders_site' );
	$site_url = site_url();
	if ( empty( $process_autoship_orders_site ) ) {
		add_option( 'wc_autoship_process_autoship_orders_site', $site_url );
		return true;
	}

	$url_find = '/^https?:\/\//';
	$filtered_process_autoship_orders_site = preg_replace( $url_find, '', $process_autoship_orders_site );
	$filtered_site_url = preg_replace( $url_find, '', $site_url );
	if ( $filtered_process_autoship_orders_site == $filtered_site_url ) {
		return true;
	}

	$autoship_process_orders = get_option( 'wc_autoship_process_autoship_orders' );
	if ( 'no' !== $autoship_process_orders ) {
		update_option( 'wc_autoship_process_autoship_orders', 'no' );
		delete_option( 'wc_autoship_pipey_ip' );
		$log_description = sprintf(
			'The site URL has changed from "%s" to "%s", and autoship order processing has been disabled.',
			$process_autoship_orders_site, $site_url
		);
		wc_autoship_log_action( 1, 'site_url_changed', $log_description, $_SERVER['REQUEST_URI'], 0, null, null, $site_url );
	}
	return false;
}

function wc_autoship_log_action( $user_id, $type, $description, $url, $customer_id = NULL, $schedule_id = NULL, $schedule_item_id = NULL, $value = NULL ) {
	global $wpdb;
	if ( ! empty( $wpdb ) ) {
		$data = array(
			'user_id' => $user_id,
			'action_time' => current_time( 'Y-m-d H:i:s' ),
			'action_type' => $type,
			'action_description' => $description,
			'action_customer_id' => $customer_id,
			'action_schedule_id' => $schedule_id,
			'action_schedule_item_id' => $schedule_item_id,
			'action_value' => substr( $value, 0, 255 ),
			'action_url' => $url
		);
		$wpdb->show_errors( false );
		$wpdb->insert( "{$wpdb->prefix}wc_autoship_log", $data );
		$wpdb->show_errors( true );
	}
}

function wc_autoship_user_is_admin( $user_id ) {
	if ( empty( $user_id ) ) {
		return false;
	}
	return user_can( $user_id, 'manage_woocommerce' );
}

function wc_autoship_load_admin_notices() {
	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		$warnings = wc_autoship_get_system_warnings();
		foreach ( $warnings as $warning ) {
			wc_autoship_add_message( $warning, 'notice-warning' );
		}
	}
}
add_filter( 'admin_notices', 'wc_autoship_load_admin_notices' );