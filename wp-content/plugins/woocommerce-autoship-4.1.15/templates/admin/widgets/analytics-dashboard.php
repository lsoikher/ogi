<div ><h3 id="wc-autoship-analytics-dashboard-widget-header"><strong>Connect to Autoship Analytics!</strong></h3></div>
<p>Connect your WooCommerce store to Autoship Analytics in 2 easy steps:</p>

<p>
	<?php echo __( 'Step 1: Add an <a href="' . esc_attr( admin_url( 'admin.php?page=wc-settings&tab=api' ) ) . '">API Key in WooCommerce</a>', 'wc-autoship' ); ?><br />
	<?php echo __( 'Step 2: Copy + paste the API key and API secret into <a href="' . esc_attr( admin_url( 'admin.php?page=wc_autoship_analytics' ) ) . '">Autoship Analytics</a>', 'wc-autoship' ); ?>
</p>

<p>That's it! You'll see a beautiful Autoship Analytics dashboard once your data has been synched.</p>

<p><a href="<?php echo esc_attr( plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) ), 'images/autoship-analytics-easy-steps.gif'; ?>" target="_blank"><img class="wc-autoship-analytics-dashboard-widget-animation" src="<?php echo esc_attr( plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) ), 'images/autoship-analytics-easy-steps.gif'; ?>" /></a></p>