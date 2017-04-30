<?php
/* @var $product WC_Product */
?>

<?php do_action( 'wc_autoship_before_product_autoship_options', $product->id ); ?>

<div class="wc-autoship-container">

	<div class="wc-autoship-options">
		<?php 
		// Frequency options
		$frequency_options = get_option( 'wc_autoship_product_page_frequency_options' );
		?>
	
		<div class="panel panel-default">
			<div class="panel-body">
				<p class="wc-autoship-select-frequency"><?php echo wc_autoship_product_page_get_description(); ?></p>
				<?php if ( ! empty( $autoship_price ) && $product->get_price() != $autoship_price ): ?>
					<h3 class="wc-autoship-price"><?php echo __( 'Auto-Ship price:', 'wc-autoship-product-page'); ?> <?php echo wc_price( $autoship_price ); ?></h3>
				<?php endif; ?>				
				<div class="wc-autoship-frequency wc-autoship-frequency-radio-options">
					<?php if ( ! empty( $frequency_options ) ): ?>
						<?php foreach ( $frequency_options as $days => $name ): ?>
							<?php if ( $days < $autoship_min_frequency || $days > $autoship_max_frequency ) continue; ?>
							<div class="wc-autoship-frequency-radio radio">
								<label for="wc_autoship_frequency_<?php echo esc_attr( $product->id ); ?>_<?php echo esc_html( $days ); ?>">
									<input type="radio" name="wc_autoship_frequency" class="wc-autoship-frequency-input-radio"
										id="wc_autoship_frequency_<?php echo esc_attr( $product->id ); ?>_<?php echo esc_html( $days ); ?>"
										value="<?php echo esc_html( $days ); ?>"
										<?php checked( $days, $autoship_default_frequency ); ?> />
									<?php echo esc_html( $name ), ' ', __( "(Every $days days)", 'wc-autoship-product-page' ); ?>
								</label>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
					<div class="wc-autoship-frequency-radio radio">
						<label for="wc_autoship_frequency_<?php echo esc_attr( $product->id ); ?>_no_autoship">
							<input type="radio" name="wc_autoship_frequency" class="wc-autoship-frequency-input-radio"
								id="wc_autoship_frequency_<?php echo esc_attr( $product->id ); ?>_no_autoship"
								value=""
								<?php checked( true, empty( $autoship_default_frequency ) ); ?> />
							<?php echo __( "No auto-ship. Make this a one-time purchase.", 'wc-autoship-product-page' ); ?>
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

<?php do_action( 'wc_autoship_after_product_autoship_options', $product->id ); ?>
