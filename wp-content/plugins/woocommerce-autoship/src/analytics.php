<?php

function wc_autoship_analytics_create_menu() {
	//create new top-level menu
	add_menu_page( 'WC Autoship Analytics', 'Autoship Analytics', 'administrator', 'wc_autoship_analytics', 'wc_autoship_analytics_settings_page' );

	//call register settings function
	add_action( 'admin_init', 'register_wc_autoship_analytics_settings' );
}

add_action( 'admin_menu', 'wc_autoship_analytics_create_menu' );

function register_wc_autoship_analytics_settings() {
	//register our settings
	register_setting( 'wc-autoship-analytics-settings-group', 'wc_autoship_analytics_ck_key' );
	register_setting( 'wc-autoship-analytics-settings-group', 'wc_autoship_analytics_cs_key' );
	register_setting( 'wc-autoship-analytics-settings-group', 'wc_autoship_analytics_do_register_site' );
}

function wc_autoship_analytics_settings_page() {
	?>
	<div class="wrap">
		<h1><?php echo __( 'WC Autoship Analytics', 'wc-autoship' ); ?></h1>

		<?php $site_is_registered = get_option( 'wc_autoship_analytics_site_is_registered' ); ?>

		<div class="wc-autoship-admin-card-container">
			<div class="card wc-autoship-admin-card-100">
				<h2><?php echo __( 'Instructions', 'wc-autoship' ); ?></h2>
				<p><?php echo __( 'This site must be publicly accessible over the web via HTTPS/SSL. Your site must be accessible with an https:// URL. <strong>This analytics feature will not work on local development sites.</strong> Data synchronization can take up to an hour to complete.', 'wc-autoship' ); ?>
					<a href="http://support.wooautoship.com/article/228-setting-up-wc-autoship-analytics"><?php echo __( 'Learn more about WC Autoship Analytics requirements.', 'wc-autoship' ); ?></a></p>
				<ol>
					<li>
						<?php echo __( 'Navigate to <a href="' . esc_attr( admin_url( 'admin.php?page=wc-settings&tab=api' ) ) . '">WooCommerce &gt; Settings &gt; API</a>.', 'wc-autoship' ); ?>
					</li>
					<li>
						<?php echo __( 'Click and open the "Keys/Apps" tab.', 'wc-autoship' ); ?>
					</li>
					<li>
						<?php echo __( 'Click the button "Add Key".', 'wc-autoship' ); ?>
					</li>
					<li>
						<?php echo __( 'Select a user with the "admin" role. Set permissions to Read. Click the button "Generate API Key".', 'wc-autoship' ); ?>
					</li>
					<li>
						<?php echo __( 'You will see a new set of API keys. Copy the Consumer Key and Consumer Secret into the corresponding fields on this page.', 'wc-autoship' ); ?>
					</li>
				</ol>
			</div>
		</div>

		<div id="pbi-report-no-data" class="wc-autoship-admin-card-container" style="display: none">
			<div class="card wc-autoship-admin-card-100">
				<h2><?php echo __( 'There is currently no data to display. Data synchronization can take up to 1 hour.', 'wc-autoship' ); ?></h2>
				<img class="pbi-dashboard-placeholder" src="<?php echo esc_attr( plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) ), 'images/dashboard-placeholder.jpg'; ?>" />
			</div>
		</div>

		<?php if ( ! empty( $site_is_registered ) && 'yes' == $site_is_registered ): ?>
			<div id="pbi-report-filters" class="wc-autoship-admin-card-container">
				<div class="card wc-autoship-admin-card-100">
					<h2><?php echo __( 'Filters', 'wc-autoship' ); ?></h2>
					<label for="pbi-start-date"><?php echo __( 'Start Date', 'wc-autoship' ); ?></label><input type="date" id="pbi-start-date" class="wc-autoship-datepicker" value="" />
					<label for="pbi-end-date"><?php echo __( 'End Date', 'wc-autoship' ); ?></label><input type="date" id="pbi-end-date" class="wc-autoship-datepicker" value="" />
				</div>
			</div>
			<div id="pbi-report-dashboard"
			     powerbi-access-token=""
			     powerbi-embed-url=""
			     powerbi-type="report"
			     data-site-url="<?php echo esc_attr( get_site_url() ); ?>"
			     data-license-key="<?php echo esc_attr( get_option( 'wc_autoship_license_key' ) ); ?>"
			     style="height:100vh"></div>

			<div class="wc-autoship-admin-card-container">
				<div class="card wc-autoship-admin-card-100">
					<h2><?php echo __( 'Re-Register Site', 'wc-autoship' ); ?></h2>
					<p><?php echo __( 'Your site has been registered. Use this form to re-register your site with different settings.', 'wc-autoship' ); ?></p>
					<form method="post" action="options.php">
						<?php settings_fields( 'wc-autoship-analytics-settings-group' ); ?>
						<?php do_settings_sections( 'wc-autoship-analytics-settings-group' ); ?>
						<table class="form-table">
							<tr valign="top">
								<th scope="row">WC API Consumer Key</th>
								<td>
									<input type="text" id="wc-api-ck-key" name="wc_autoship_analytics_ck_key"
									       value="<?php echo esc_attr( get_option( 'wc_autoship_analytics_ck_key' ) ); ?>"
									       placeholder="ck_" autocomplete="off"/>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">WC API Consumer Secret</th>
								<td>
									<input type="password" id="wc-api-cs-key" name="wc_autoship_analytics_cs_key"
									       value="<?php echo esc_attr( get_option( 'wc_autoship_analytics_cs_key' ) ); ?>"
									       placeholder="cs_" autocomplete="new-password"/>
								</td>
							</tr>
						</table>
						<input type="hidden" name="wc_autoship_analytics_do_register_site" value="<?php echo time(); ?>"/>

						<?php submit_button( __( 'Re-register Site', 'wc-autoship' ) ); ?>

					</form>
				</div>
			</div>
		<?php else: ?>
			<div class="wc-autoship-admin-card-container">
				<div class="card wc-autoship-admin-card-100">
					<h2 class="pbi-dashboard-welcome-header"><?php echo __( 'Connect to WC Autoship Cloud Analytics', 'wc-autoship' ); ?></h2>
					<p><?php echo __( 'Connect your WooCommerce API to our secure analytics server for a dashboard view of Autoship order and customer data.  Your dashboard is powered by the WooAutoship server so there’s nothing else to install on Wordpress!  What are you waiting for?', 'wc-autoship' ); ?>
						<a href="http://support.wooautoship.com/article/228-setting-up-wc-autoship-analytics"><?php echo __( 'Learn more about WC Autoship Analytics', 'wc-autoship' ); ?></a></p>
					<form method="post" action="options.php">
						<?php settings_fields( 'wc-autoship-analytics-settings-group' ); ?>
						<?php do_settings_sections( 'wc-autoship-analytics-settings-group' ); ?>
						<table class="form-table">
							<tr valign="top">
								<th scope="row">WC API Consumer Key</th>
								<td>
									<input type="text" id="wc-api-ck-key" name="wc_autoship_analytics_ck_key"
									       value="<?php echo esc_attr( get_option( 'wc_autoship_analytics_ck_key' ) ); ?>"
									       placeholder="ck_" autocomplete="off"/>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">WC API Consumer Secret</th>
								<td>
									<input type="password" id="wc-api-cs-key" name="wc_autoship_analytics_cs_key"
									       value="<?php echo esc_attr( get_option( 'wc_autoship_analytics_cs_key' ) ); ?>"
									       placeholder="cs_" autocomplete="new-password"/>
								</td>
							</tr>
						</table>
						<input type="hidden" name="wc_autoship_analytics_do_register_site" value="<?php echo time(); ?>"/>

						<?php submit_button( __( 'Connect to WC Autoship Analytics', 'wc-autoship' ) ); ?>

						<img class="pbi-dashboard-placeholder" src="<?php echo esc_attr( plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) ), 'images/dashboard-placeholder.jpg'; ?>" />

					</form>
				</div>
			</div>
		<?php endif; ?>

	</div>
<?php }

function wc_autoship_analytics_admin_scripts() {
	wp_enqueue_script( 'wc-autoship-analytics-powerbi', plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) . 'js/admin/powerbi.min.js', array(), WC_AUTOSHIP_VERSION );
	wp_enqueue_script( 'wc-autoship-analytics-reports', plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) . 'js/admin/reports.js', array(), WC_AUTOSHIP_VERSION );
}
add_action( 'admin_enqueue_scripts', 'wc_autoship_analytics_admin_scripts' );

function wc_autoship_analytics_register_site() {
	$site_data = array(
		'method' => 'POST',
		'body' => array(
			'Url' => get_site_url(),
			'LicenseKey' => get_option( 'wc_autoship_license_key' ),
			'Name' => get_bloginfo( 'name' ),
			'WcConsumerKey' => get_option( 'wc_autoship_analytics_ck_key' ),
			'WcConsumerSecret' => get_option( 'wc_autoship_analytics_cs_key' ),
			'Email' => get_option( 'admin_email' )
		)
	);
	$response = wp_remote_post( 'https://analytics.wooautoship.com:9000/api/Sites', $site_data );
	if ( is_wp_error( $response ) ) {
		update_option( 'wc_autoship_analytics_site_is_registered', 'no' );
		wc_autoship_add_message( 'There was an error registering this site!' );
		return false;
	} else if ( 409 == $response['response']['code'] ) {
		$site_data['method'] = 'PUT';
		$response = wp_remote_post( 'https://analytics.wooautoship.com:9000/api/Sites', $site_data );
		if ( is_wp_error( $response ) ) {
			update_option( 'wc_autoship_analytics_site_is_registered', 'no' );
			wc_autoship_add_message( 'There was an error registering this site!' );
			return false;
		}
	}
	if ( 200 == $response['response']['code'] || 201 == $response['response']['code'] ) {
		update_option( 'wc_autoship_analytics_site_is_registered', 'yes' );
		wc_autoship_add_message( 'Your site was registered!' );
		return true;
	}

	update_option( 'wc_autoship_analytics_site_is_registered', 'no' );
	wc_autoship_add_message( 'Unkown error registering your site!' );
	return false;
}
add_action( 'update_option_wc_autoship_analytics_do_register_site', 'wc_autoship_analytics_register_site', 10, 0 );
add_action( 'add_option_wc_autoship_analytics_do_register_site', 'wc_autoship_analytics_register_site', 10, 0 );

