<?php
/* @var $customer WC_Autoship_Models_Customer */
?>

<?php wc_print_notices(); ?>

<?php do_action( 'wc_autoship_before_autoship_billing', $customer->get_id() ); ?>

<div class="wc-autoship-container">
	<div class="wc-autoship-billing">
		<div class="row">
			<!-- card section -->
			<section class="card col-xs-12">
			
				<!-- Breadcrumbs -->
				<ol class="breadcrumb text-center">
					<li><a href="<?php echo get_permalink( get_option( 'wc_autoship_menu_page_id' ) ); ?>"><?php echo __( 'Autoship', 'wc-autoship' ); ?></a></li>
					<li class="active"><a href="<?php echo get_permalink( get_option( 'wc_autoship_billing_page_id' ) ); ?>"><?php echo __( 'Billing', 'wc-autoship' ); ?></a></li>
				</ol>
	
				<hr />
	
				<div class="col-xs-12 col-sm-6">
					<div class="well well-card wc-autoship-current-payment-method">
						<h4>Payment Method</h4>
						<?php $gateway = $customer->get_payment_gateway(); ?>
						<?php if ( $gateway != null ): ?>
							<div class="wc-autoship-payment-method-description">
								<?php echo $gateway->get_payment_method_description( $customer ); ?>
							</div>
						<?php else: ?>
							<span>No payment method found.</span>
						<?php endif; ?>
					</div>
					<a class="btn btn-primary btn-sm center-block"
						data-toggle="wc-autoship-collapse" href="#payment-update" aria-expanded="false"
						aria-controls="payment-update">Edit Payment Method</a>
				</div>
				<!-- payment-update -->
				<div class="col-xs-12 col-sm-6 wc-autoship-collapsed" id="payment-update">
	
	
					<div class="well well-card">
						<h4>Edit Payment Method</h4>
						<form method="post"
							action="<?php echo admin_url( 'admin-ajax.php?action=customer_action_submit_payment_method' ); ?>">
							<div style="display: none">
								<input type="hidden" name="customer_id"
									value="<?php echo esc_html( $customer->get_id() ); ?>" />
							</div>
							<div class="checkout">
								<div id="order_review">
									<div id="payment">
										<ul id="wc-autoship-my-account-payment-methods"
											class="payment_methods methods">
											<?php
											$all_available_gateways = WC ()->payment_gateways->get_available_payment_gateways ();
											$available_gateways = array ();
											foreach ( $all_available_gateways as $gateway ) {
												if ($gateway instanceof WC_Autoship_Payment_Gateway) {
													$available_gateways [] = $gateway;
												}
											}
											if (count ( $available_gateways ) > 0) {
												foreach ( $available_gateways as $gateway ) {
													?>
														<li class="payment_method_<?php echo $gateway->id; ?>"><input
												id="payment_method_<?php echo $gateway->id; ?>" type="radio"
												class="input-radio" name="payment_method"
												value="<?php echo esc_attr( $gateway->id ); ?>"
												<?php checked( $gateway->id == $customer->get( 'payment_gateway_id' ), true ); ?>
												data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
												<label for="payment_method_<?php echo $gateway->id; ?>"><?php echo $gateway->get_title(); ?> <?php echo $gateway->get_icon(); ?></label>
															<?php
													if ($gateway->has_fields () || $gateway->get_description ()) {
														echo '<div class="payment_box payment_method_' . $gateway->id . '" style="display:none;">';
														$gateway->payment_fields ();
														echo '</div>';
													}
													?>
														</li>
														<?php
												}
											} else {
												
												echo '<p>' . __ ( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) . '</p>';
											}
											?>
										</ul>
									</div>
								</div>
							</div>
							<p>
								<button type="submit" id="wc-autoship-update-payment-method-btn"
									class="wc-autoship-update-payment-method-btn btn btn-danger btn-lg ">
									<?php echo __( 'Save Changes', 'wc-autoship' );?>
								</button>
							</p>
						</form>
					</div>
					<div class="clearfix gutter-card"></div>
				</div>
				<!-- end payment-update -->
	
				<div class="col-xs-12 col-sm-6">
					<div class="well well-card wc-autoship-current-billing">
						<h4>Billing Address</h4>
						<p class="wc-autoship-billing-address">
							<?php echo esc_html( $customer->get( 'billing_first_name' ) ); ?> <?php echo esc_html( $customer->get( 'billing_last_name' ) ); ?><br />
							<?php echo esc_html( $customer->get( 'billing_address_1' ) ); ?> <?php echo esc_html( $customer->get( 'billing_address_2' ) ); ?><br />
							<?php echo esc_html( $customer->get( 'billing_city' ) ); ?>, <?php echo esc_html( $customer->get( 'billing_state' ) ); ?> <?php echo esc_html( $customer->get( 'billing_postcode' ) ); ?><br />
							<?php echo esc_html( $customer->get( 'billing_country' ) ); ?><br />
						</p>
					</div>
					<?php $edit_billing_path = get_option( 'permalink_structure' ) ? 'edit-address/billing/' : '&amp;edit-address=billing'; ?>
					<a
						class="wc-autoship-edit-billing-address-link btn btn-primary btn-sm center-block"
						href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ), $edit_billing_path; ?>">Edit
						Billing Address</a>
				</div>
	
			</section>
		</div>
	</div>
</div>

<?php do_action( 'wc_autoship_after_autoship_billing', $customer->get_id() ); ?>