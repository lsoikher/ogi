<?php

function wc_autoship_add_settings_tab( $tabs ) {
	$tabs['wc_autoship'] = __( 'WC Autoship', 'wc-autoship' );
	$tabs['wc_autoship_addons'] = __( 'WC Autoship Add-ons', 'wc-autoship' );
	return $tabs;
}
add_filter( 'woocommerce_settings_tabs_array', 'wc_autoship_add_settings_tab', 50, 1 );

function wc_autoship_get_settings() {
	require_once( 'Models/Schedule.php' );
	$pending_autoship_count = WC_Autoship_Models_Schedule::get_pending_autoship_schedules_count();
	$autoship_settings = array(
		array(
			'name' => __( 'Autoship Settings', 'wc-autoship' ),
			'type' => 'title',
			'desc' => __( 'Enter general system settings for autoship.', 'wc-autoship' ),
			'id' => 'wc_autoship_general_settings'
		),
		array(
			'name' => __( 'License Key', 'wc-autoship' ),
			'desc' => __( 'Enter your software license key issued after purchase.', 'wc-autoship' ),
			'desc_tip' => true,
			'type' => 'text',
			'id' => 'wc_autoship_license_key'
		),
		array(
			'name' => __( 'Management Page Message (HTML)', 'wc-autoship' ),
			'desc' => __( 'A custom message to display on Autoship management pages.', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'textarea',
			'id' => 'wc_autoship_management_page_message'
		),
		array(
			'name' => __( 'No Autoship Message (HTML)', 'wc-autoship' ),
			'desc' => __( 'A custom message to display for customers with no autoship schedules.', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'textarea',
			'id' => 'wc_autoship_no_autoship_message'
		),
		array(
			'name' => __( 'No Autoship Option Name', 'wc-autoship' ),
			'desc' => __( 'Message to display for "No Autoship" option on product page.', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'text',
			'id' => 'wc_autoship_no_autoship_option_name',
			'placeholder' => wc_autoship_get_no_autoship_option_name()
		),
		array(
			'name' => __( 'Enable Coupon Field', 'wc-autoship' ),
			'desc' => __( "Select to enable the Coupon field on the Autoship Schedules page.", 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'checkbox',
			'id' => 'wc_autoship_schedules_enable_coupon_field',
			'default' => 'yes'
		),
		array(
			'name' => __( 'Configuration', 'wc-autoship' ),
			'desc' => __( "Click to reset the WC Autoship configuration after making changes to the hosting environment.", 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'wc_autoship_button_type',
			'id' => 'wc_autoship_reset_configuration',
			'text' => 'Reset WC Autoship Configuration',
			'link' => admin_url( '/admin-ajax.php?action=wc_autoship_reset_configuration' )
		),
		array(
			'name' => __( 'Cache', 'wc-autoship' ),
			'desc' => __( "Click to delete the WC Autoship cache.", 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'wc_autoship_button_type',
			'id' => 'wc_autoship_delete_cache',
			'text' => 'Delete Cache',
			'link' => admin_url( '/admin-ajax.php?action=wc_autoship_delete_cache' )
		),
		array(
			'name' => __( 'Cron', 'wc-autoship' ),
			'desc' => __( 'Click to reset WC Autoship cron events.', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'wc_autoship_button_type',
			'id' => 'wc_autoship_reset_cron',
			'text' => __( 'Reset Cron', 'wc-autoship' ),
			'link' => admin_url( '/admin-ajax.php?action=wc_autoship_reset_cron' )
		),
		array(
			'name' => __( 'Enable Reporting', 'wc-autoship' ),
			'desc' => __( "Enable reporting to help us understand how Autoship is being used on your site. This will allow us to track errors and usage to help improve our product with future updates.", 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'checkbox',
			'id' => 'wc_autoship_reporting_enabled'
		),
		array(
			'type' => 'sectionend',
			'id' => 'wc_autoship_general_section_end'
		),
		array(
			'name' => __( 'Order Processing', 'wc-autoship' ),
			'type' => 'title',
			'id' => 'wc_autoship_order_processing_settings'
		),
		array(
			'name' => __( 'Process Autoship Orders', 'wc-autoship' ),
			'desc' => __( "Select to enable the autoship orders cron. De-select to halt all autoship orders. <strong>There are {$pending_autoship_count} orders scheduled to process today.</strong>", 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'checkbox',
			'id' => 'wc_autoship_process_autoship_orders',
			'default' => 'yes'
		),
		array(
			'name' => __( 'Processing Start Time (24hr)', 'wc-autoship' ),
			'desc' => __( 'The time of day when autoship orders will begin processing. (' . get_option( 'timezone_string' ) . ')', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'text',
			'id' => 'wc_autoship_processing_start_time',
			'placeholder' => '00:00:00'
		),
		array(
			'name' => __( 'Processing End Time (24hr)', 'wc-autoship' ),
			'desc' => __( 'The time of day when autoship orders will stop processing. (' . get_option( 'timezone_string' ) . ')', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'text',
			'id' => 'wc_autoship_processing_end_time',
			'placeholder' => '23:59:59'
		),
		array(
			'type' => 'sectionend',
			'id' => 'wc_autoship_order_processing_section_end'
		),
		array(
			'name' => __( 'Autoship Shipping', 'wc-autoship' ),
			'type' => 'title',
			'id' => 'wc_autoship_shipping_settings'
		),
		array(
			'name' => __( 'Enable Free Shipping', 'wc-autoship' ),
			'desc' => __( "Select to enable the Autoship Free Shipping method. You may need to Delete Autoship Cache for the changes to take effect.", 'wc-autoship' ),
			'desc_tip' => true,
			'type' => 'radio',
			'id' => 'wc_autoship_free_shipping',
			'options' => array(
				'' => __( 'Disable Autoship Free Shipping. You may need to <a href="' . admin_url( '/admin-ajax.php?action=wc_autoship_delete_cache' ) . '">Delete Autoship Cache</a> for the changes to take effect.', 'wc-autoship' ),
				'checkout+autoship' => __( 'Add the Autoship Free Shipping method to both <strong>Checkout</strong> and recurring <strong>Autoship</strong> orders. You may need to <a href="' . admin_url( '/admin-ajax.php?action=wc_autoship_delete_cache' ) . '">Delete Autoship Cache</a> for the changes to take effect.', 'wc-autoship' ),
				'autoship' => __( 'Add the Autoship Free Shipping method to recurring <strong>Autoship</strong> orders only. You may need to <a href="' . admin_url( '/admin-ajax.php?action=wc_autoship_delete_cache' ) . '">Delete Autoship Cache</a> for the changes to take effect.', 'wc-autoship' ),
			),
            'default' => ''
		),
		array(
			'type' => 'sectionend',
			'id' => 'wc_autoship_shipping_section_end'
		),
		array(
			'name' => __( 'Autoship Emails', 'wc-autoship' ),
			'type' => 'title',
			'id' => 'wc_autoship_email_settings'
		),
		array(
			'name' => __( 'Send Autoship Error Email', 'wc-autoship' ),
			'desc' => __( 'Enable notification emails to be sent to customers when their autoship orders fail.', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'checkbox',
			'id' => 'wc_autoship_send_error_email',
			'default' => 'yes'
		),
		array(
			'name' => __( 'Send Autoship Schedule Updated Email', 'wc-autoship' ),
			'desc' => __( 'Enable notification emails to be sent to customers when their Autoship schedules have been updated.', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'checkbox',
			'id' => 'wc_autoship_send_schedule_updated_email',
			'default' => 'yes'
		),
		array(
			'name' => __( 'Send Autoship One Day Notice Email', 'wc-autoship' ),
			'desc' => __( 'Enable notification emails to be sent to customers when they have an Autoship schedule with an order scheduled for the next day.', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'checkbox',
			'id' => 'wc_autoship_send_1day_email',
			'default' => 'yes'
		),
		array(
			'name' => __( 'Send Autoship Ten Day Notice Email', 'wc-autoship' ),
			'desc' => __( 'Enable notification emails to be sent to customers when they have an Autoship schedule with an order scheduled for ten days from now.', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'checkbox',
			'id' => 'wc_autoship_send_10day_email',
			'default' => 'yes'
		),
		array(
			'type' => 'sectionend',
			'id' => 'wc_autoship_emails_section_end'
		),
		array(
			'name' => __( 'Autoship Hooks', 'wc-autoship' ),
			'type' => 'title',
			'id' => 'wc_autoship_hooks_settings'
		),
		array(
			'name' => __( 'Product Page Options Hook', 'wc-autoship' ),
			'desc' => __( 'Enter a custom hook to display the product page options', 'wc-autoship' ),
			'desc_tip' => true,
			'type' => 'text',
			'id' => 'wc_autoship_product_page_options_hook',
			'placeholder' => 'woocommerce_before_add_to_cart_button',
			'css' => 'min-width: 300px'
		),
		array(
			'name' => __( 'Variable Product Page Options Hook', 'wc-autoship' ),
			'desc' => __( 'Enter a custom hook to display the variable product page options', 'wc-autoship' ),
			'desc_tip' => true,
			'type' => 'text',
			'id' => 'wc_autoship_variable_product_page_options_hook',
			'placeholder' => 'woocommerce_before_single_variation',
			'css' => 'min-width: 300px'
		),
		array(
			'type' => 'sectionend',
			'id' => 'wc_autoship_hooks_section_end'
		),
		array(
			'name' => __( 'Error Handling', 'wc-autoship' ),
			'type' => 'title',
			'id' => 'wc_autoship_error_handling_settings'
		),
		array(
			'name' => __( 'Free Shipping on Shipping Error', 'wc-autoship' ),
			'desc' => __( 'Select to fall back to Free Shipping when no other shipping methods are available. You may need to <a href="' . admin_url( '/admin-ajax.php?action=wc_autoship_delete_cache' ) . '">Delete Autoship Cache</a> for the changes to take effect.', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'checkbox',
			'id' => 'wc_autoship_free_shipping_on_shipping_error',
			'default' => 'no'
		),
		array(
			'type' => 'sectionend',
			'id' => 'wc_autoship_error_handling_section_end'
		),
		array(
			'name' => __( 'Site Access', 'wc-autoship' ),
			'type' => 'title',
			'id' => 'wc_autoship_site_access_settings',
			'desc' => __(
				'If your site is protected with Basic Auth credentials, enter the username and password into the fields below. Otherwise, leave these fields empty.'
				. ( strlen( WC_AUTOSHIP_PIPEY_AUTH ) > 0 ? '<br /><strong>Override is in effect in wp-config.php: WC_AUTOSHIP_PIPEY_AUTH</strong>' : '' ),
				'wc-autoship'
			)
		),
		array(
			'name' => __( 'Username', 'wc-autoship' ),
			'desc' => __( 'If your site is protected with Basic Auth credentials, enter the username here.', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'text',
			'id' => 'wc_autoship_pipey_username',
			'custom_attributes' => array(
				'autocomplete' => 'off',
				( strlen( WC_AUTOSHIP_PIPEY_AUTH ) > 0  ? '' : 'data-not-' ) . 'disabled' => 'disabled'
			),
			'default' => ''
		),
		array(
			'name' => __( 'Password', 'wc-autoship' ),
			'desc' => __( 'If your site is protected with Basic Auth credentials, enter the password here.', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'password',
			'id' => 'wc_autoship_pipey_password',
			'custom_attributes' => array(
				'autocomplete' => 'new-password',
				( strlen( WC_AUTOSHIP_PIPEY_AUTH ) > 0  ? '' : 'data-not-' ) . 'disabled' => 'disabled'
			),
			'default' => ''
		),
		array(
			'name' => __( 'Client IP', 'wc-autoship' ),
			'desc' => __( 'Autoship client IP. If you do not know what this means, do not change it!', 'wc-autoship' ),
			'desc_tip' => false,
			'type' => 'text',
			'id' => 'wc_autoship_pipey_ip',
			'custom_attributes' => array(
				'autocomplete' => 'off',
				( strlen( WC_AUTOSHIP_PIPEY_IP ) > 0  ? '' : 'data-not-' ) . 'disabled' => 'disabled'
			),
			'default' => ''
		),
		array(
			'type' => 'sectionend',
			'id' => 'wc_autoship_site_access_section_end'
		),
	);
	$settings = apply_filters( 'wc_autoship_settings', $autoship_settings );
	return $settings;
}

function wc_autoship_settings_button_type( $value ) {
	// Description handling
	$field_description = WC_Admin_Settings::get_field_description( $value );
	extract( $field_description );

	?><tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
		<?php echo $tooltip_html; ?>
	</th>
	<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
		<button type="button" class="button-secondary" onclick="window.location = <?php echo esc_attr( json_encode( $value['link'] ) ); ?>"><?php echo esc_html( $value['text'] ); ?></button>
		<br /><?php echo $description; ?>
	</td>
	</tr><?php
}
add_action( 'woocommerce_admin_field_wc_autoship_button_type', 'wc_autoship_settings_button_type' );

function wc_autoship_settings_tab() {
	woocommerce_admin_fields( wc_autoship_get_settings() );
}
add_action( 'woocommerce_settings_tabs_wc_autoship', 'wc_autoship_settings_tab' );

function wc_autoship_get_settings_addons() {
	$autoship_settings = array(
		array(
			'name' => __( 'Autoship Add-ons Settings', 'wc-autoship' ),
			'type' => 'title',
			'desc' => __( '', 'wc-autoship' ),
			'id' => 'wc_autoship_addons_settings'
		),
		array(
			'type' => 'sectionend',
			'id' => 'wc_autoship_addons_section_end'
		)
	);
	$settings = apply_filters( 'wc_autoship_addons_settings', $autoship_settings );
	return $settings;
}

function wc_autoship_settings_tab_addons() {
	woocommerce_admin_fields( wc_autoship_get_settings_addons() );
}
add_action( 'woocommerce_settings_tabs_wc_autoship_addons', 'wc_autoship_settings_tab_addons' );

function wc_autoship_update_options() {
	woocommerce_update_options( wc_autoship_get_settings() );
}
add_action( 'woocommerce_update_options_wc_autoship', 'wc_autoship_update_options' );

function wc_autoship_update_options_addons() {
	woocommerce_update_options( wc_autoship_get_settings_addons() );
}
add_action( 'woocommerce_update_options_wc_autoship_addons', 'wc_autoship_update_options_addons' );

function wc_autoship_update_option( $value ) {
	if ( $value['id'] == 'wc_autoship_license_key' ) {
		$new_license_key = $_POST[ $value['id'] ];
		$license_key = get_option( 'wc_autoship_license_key' );
		$license_key_expires = strtotime( get_option( 'wc_autoship_license_key_expires' ) );
		if ( $new_license_key != $license_key
			|| ( ! empty( $license_key ) && $license_key_expires < time() ) ) {
			$result = wc_autoship_activate_license_key( $new_license_key, 'WC Autoship' );
			if ( $result == NULL ) {
				update_option( 'wc_autoship_license_key_validated', '0' );
				update_option( 'wc_autoship_license_key_expires', '0' );
				wc_autoship_add_message( __( 'Your WC Autoship license key is invalid!', 'wc-autoship' ), 'error' );
				$log_description = __( "License key activation for '$new_license_key' returned NULL response.", 'wc-autoship' );
				wc_autoship_log_action( get_current_user_id(), 'license_key_invalid', $log_description, $_SERVER['REQUEST_URI'], NULL, NULL, NULL, json_encode( $result ) );
			} elseif ( $result->success ) {
				update_option( 'wc_autoship_license_key_validated', '1' );
				update_option( 'wc_autoship_license_key_expires', $result->expires );
				wc_autoship_add_message( __( 'Your WC Autoship license key is valid!', 'wc-autoship' ) );
				$log_description = __( "License key '$new_license_key' validated", 'wc-autoship' );
				wc_autoship_log_action( get_current_user_id(), 'license_key_valid', $log_description, $_SERVER['REQUEST_URI'], NULL, NULL, NULL, json_encode( $result ) );
			} elseif ( ! $result->success ) {
				if ( $result->error == 'expired' ) {
					update_option( 'wc_autoship_license_key_validated', '1' );
					update_option( 'wc_autoship_license_key_expires', $result->expires );
					wc_autoship_add_message( __( 'Your WC Autoship license key is expired!', 'wc-autoship' ), 'error' );
					$log_description = __( "License key '$new_license_key' is expired.", 'wc-autoship' );
					wc_autoship_log_action( get_current_user_id(), 'license_key_invalid', $log_description, $_SERVER['REQUEST_URI'], NULL, NULL, NULL, json_encode( $result ) );
				} else {
					update_option( 'wc_autoship_license_key_validated', '0' );
					update_option( 'wc_autoship_license_key_expires', '0' );
					wc_autoship_add_message( __( 'Your WC Autoship license key is invalid!', 'wc-autoship' ), 'error' );
					$log_description = __( "License key '$new_license_key' is invalid.", 'wc-autoship' );
					wc_autoship_log_action( get_current_user_id(), 'license_key_invalid', $log_description, $_SERVER['REQUEST_URI'], NULL, NULL, NULL, json_encode( $result ) );
				}
			}
		}
	} else if ( $value['id'] == 'wc_autoship_process_autoship_orders' ) {
		if ( isset( $_POST[ $value['id'] ] ) ) {
			update_option( 'wc_autoship_process_autoship_orders_site', site_url() );
		} else {
			$user_id = get_current_user_id();
			$log_description = sprintf( 'Autoship Order Processing has been disabled by user %d.', $user_id );
			wc_autoship_log_action( $user_id, 'process_autoship_orders_disabled', $log_description, $_SERVER['REQUEST_URI'], 0, null, null, null );
		}
	}

	// Addons
	$addon_license_keys = apply_filters( 'wc_autoship_addon_license_keys', array() );
	if ( ! empty( $addon_license_keys[ $value['id'] ] ) ) {
		$new_license_key = $_POST[ $value['id'] ];
		$license_key = trim( get_option( $value['id'] ) );
		if ( empty( $license_key ) || $new_license_key != $license_key ) {
			$item_name = $addon_license_keys[ $value['id'] ]['item_name'];
			$result = wc_autoship_activate_license_key( $new_license_key, $item_name );
			if ( $result == NULL ) {
				wc_autoship_add_message( __( "Your {$item_name} license key is invalid!", 'wc-autoship' ), 'error' );
			} elseif ( $result->success ) {
				wc_autoship_add_message( __( "Your {$item_name} license key is valid!", 'wc-autoship' ) );
			} elseif ( ! $result->success ) {
				if ( $result->error == 'expired' ) {
					wc_autoship_add_message( __( "Your {$item_name} license key is expired!", 'wc-autoship' ), 'error' );
				} else {
					wc_autoship_add_message( __( "Your {$item_name} license key is invalid!", 'wc-autoship' ), 'error' );
				}
			}
		}
	}
}
add_action( 'woocommerce_update_option', 'wc_autoship_update_option' );

function wc_autoship_activate_license_key( $license_key, $item_name ) {
	$api_params = array(
		'edd_action' => 'activate_license',
		'item_name' => urlencode( $item_name ),
		'license' => $license_key,
		'url' => home_url()
	);
	$log_description = __( "Set license key '$license_key'.", 'wc-autoship' );
	wc_autoship_log_action( get_current_user_id(), 'set_license_key', $log_description, $_SERVER['REQUEST_URI'], NULL, NULL, NULL, json_encode( $api_params ) );

	$url = wc_autoship_get_licensing_url() . '?' . http_build_query( $api_params );

	$response = wp_remote_get( $url, array(
		'timeout' => 15, 'sslverify' => false
	) );

	if ( is_wp_error( $response ) ) {
		return NULL;
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );
	return $license_data;
}

function wc_autoship_get_default_country() {
	$default_country = get_option( 'woocommerce_default_country' );
	if ( ! empty( $default_country ) && false !== strpos( $default_country, ':' ) ) {
		$country_state = explode( ':', $default_country );
		$default_country = $country_state[0];
	}
	return $default_country;
}

function wc_autoship_get_no_autoship_option_name() {
	$no_autoship_option_name = get_option( 'wc_autoship_no_autoship_option_name', false );
	if ( empty( $no_autoship_option_name ) ) {
		$no_autoship_option_name = 'No Autoship';
	}
	return apply_filters( 'wc_autoship_no_autoship_option_name', $no_autoship_option_name );
}