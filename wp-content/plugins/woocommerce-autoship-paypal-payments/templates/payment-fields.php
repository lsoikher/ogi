<div class="wc-autoship-paypal-fields">
	<img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_200x51.png" alt="PayPal" />

	<ul class="woocommerce-SavedPaymentMethods wc-saved-payment-methods">
		<?php foreach ( $payment_tokens as $token ): ?>
			<li class="woocommerce-SavedPaymentMethods-token">
				<input id="wc-wc_autoship_paypal-payment-token-<?php echo esc_attr( $token->get_id() ); ?>" type="radio" name="wc-wc_autoship_paypal-payment-token" value="<?php echo esc_attr( $token->get_id() ); ?>" style="width:auto;" class="woocommerce-SavedPaymentMethods-tokenInput" <?php checked( true, $token->is_default() ); ?> />
				<label for="wc-wc_autoship_paypal-payment-token-<?php echo esc_attr( $token->get_id() ); ?>"><?php echo esc_html( $token->get_display_name() ); ?></label>
			</li>
		<?php endforeach; ?>
		<li class="woocommerce-SavedPaymentMethods-token">
			<input id="wc-wc_autoship_paypal-payment-token-new" type="radio" name="wc-wc_autoship_paypal-payment-token" value="new" style="width:auto;" class="woocommerce-SavedPaymentMethods-tokenInput" <?php checked( true, empty( $payment_tokens ) ); ?> />
			<label for="wc-wc_autoship_paypal-payment-token-new"><?php echo __( 'New payment method', 'wc-autoship' ); ?></label>
		</li>
	</ul>
</div>