<?php
/* @var $product WC_Product */
?>

<?php
$wc_3 = false;
if ( function_exists( 'WC' ) ) {
	$version = WC()->version;
	if ( version_compare( $version, '3.0.0' ) >= 0 ) { // true if we are running WC 3+
		$wc_3 = true;
	}
}
$product_id = $wc_3 ? $product->get_id() : $product->variation_id;
do_action( 'wc_autoship_before_product_autoship_options_variable', $product_id );

?>

<div class="wc-autoship-container">

	<div class="wc-autoship-options">
		<div class="panel panel-default">
			<div class="panel-body">
				<p class="wc-autoship-select-frequency"><?php echo __( 'Select an Auto-Ship Frequency to add this item to auto-ship.', 'wc-autoship' ); ?></p>
				<h3 class="wc-autoship-price" <?php if ( empty( $autoship_price ) ) echo 'style="display:none"'; ?>><?php echo __( 'Auto-Ship price:', 'wc-autoship'); ?> <?php echo wc_price( $autoship_price ); ?></h3>
				<p class="wc-autoship-frequency">
					<label for="wc_autoship_frequency_<?php echo esc_attr( $product_id ); ?>"><?php echo __( 'Auto-Ship Frequency:', 'wc-autoship' ); ?></label>
					<select name="wc_autoship_frequency" id="wc_autoship_frequency_<?php echo esc_attr( $product_id ); ?>">
						?>
						<option value="">&mdash;<?php echo esc_html( __( wc_autoship_get_no_autoship_option_name(), 'wc-autoship' ) ); ?>&mdash;</option>
						<?php for ( $d = $autoship_min_frequency; $d <= $autoship_max_frequency; $d++ ): ?>
							<option value="<?php echo esc_html( $d ); ?>" <?php echo selected( $d, $autoship_default_frequency ); ?>><?php echo esc_html( __( "Every $d days", 'wc-autoship' ) ); ?></option>
						<?php endfor; ?>
					</select>
				</p>
			</div>
		</div>
	</div>
	
</div>

<?php do_action( 'wc_autoship_after_product_autoship_options_variable', $product_id ); ?>