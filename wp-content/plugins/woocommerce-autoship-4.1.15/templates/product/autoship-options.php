<?php
/* @var $product WC_Product */
?>

<?php
$product_id = $product->get_id();
do_action( 'wc_autoship_before_product_autoship_options', $product_id ); ?>

<div class="wc-autoship-container">

	<div class="wc-autoship-options">
		<div class="panel panel-default">
			<div class="panel-body">
				<p class="wc-autoship-selectfrequency"><?php echo __( 'Select an Auto-Ship Frequency to add this item to auto-ship.', 'wc-autoship' ); ?></p>
				<?php if ( ! empty( $autoship_price ) && $product->get_price() != $autoship_price ): ?>
					<h3 class="wc-autoship-price"><?php echo __( 'Auto-Ship price:', 'wc-autoship'); ?> <?php echo wc_price( $autoship_price ); ?></h3>
				<?php endif; ?>
				<p class="wc-autoship-frequency">
					<label for="wc_autoship_frequency_<?php echo esc_attr( $product_id ); ?>"><?php echo __( 'Auto-Ship Frequency:', 'wc-autoship' ); ?></label>
					<select name="wc_autoship_frequency" id="wc_autoship_frequency_<?php echo esc_attr( $product_id ); ?>">
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

<?php do_action( 'wc_autoship_after_product_autoship_options', $product_id ); ?>