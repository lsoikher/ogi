<?php
/* @var $product WC_Product */
?>

<?php
$wc_3 = false;
if ( function_exists( 'WC' ) ) {
	$version = WC()->version;
	if ( version_compare( $version, '3.0.0', '>=' ) ) { // true if we are running WC 3+
		$wc_3 = true;
	}
}
$product_id = $wc_3 ? $product->get_id() : $product->variation_id;

do_action( 'wc_autoship_before_product_autoship_options_variable', $product_id );

?>

<div class="wc-autoship-container">

	<div class="wc-autoship-options">
		<?php
		// Frequency options
		$frequency_options = get_option( 'wc_autoship_product_page_frequency_options' );
		?>
	
		<div class="panel panel-default">
			<div class="panel-body">
				<p class="wc-autoship-selectfrequency"><?php echo wc_autoship_product_page_get_description(); ?></p>
				<h3 class="wc-autoship-price" <?php if ( empty( $autoship_price ) ) echo 'style="display:none"'; ?>><?php echo __( 'Auto-Ship price:', 'wc-autoship-product-page'); ?> <?php echo wc_price( $autoship_price ); ?></h3>			
				<p class="wc-autoship-frequency">
					<label for="wc_autoship_frequency_<?php echo esc_attr( $product_id ); ?>"><?php echo __( 'Auto-Ship Frequency:', 'wc-autoship-product-page' ); ?></label>
					<select name="wc_autoship_frequency" id="wc_autoship_frequency_<?php echo esc_attr( $product_id ); ?>">
						<option value="">&mdash;<?php echo __( 'SELECT', 'wc-autoship-product-page' ); ?>&mdash;</option>
						<?php if ( ! empty( $frequency_options ) ): ?>
							<?php foreach ( $frequency_options as $days => $name ): ?>
								<?php if ( $days < $autoship_min_frequency || $days > $autoship_max_frequency ) continue; ?>
								<option value="<?php echo esc_html( $days ); ?>" <?php echo selected( $days, $autoship_default_frequency ); ?>><?php echo esc_html( $name ), ' ', __( "(Every $days days)", 'wc-autoship-product-page' ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</p>
			</div>
		</div>
	</div>
	
</div>

<?php do_action( 'wc_autoship_after_product_autoship_options_variable', $product_id ); ?>
