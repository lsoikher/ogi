<?php
/* @var $customer WC_Autoship_Models_Customer */
/* @var $shipping_methods WC_Shipping_Method[] */
?>

<?php wc_print_notices(); ?>

<?php do_action( 'wc_autoship_before_autoship_shipping', $customer->get_id() ); ?>

<?php WC_Autoship::include_template( 'autoship-customer-admin-menu', array( 'customer' => $customer ) ); ?>

<div class="wc-autoship-container">

	<form method="post" action="<?php echo admin_url( 'admin-ajax.php?action=customer_action_submit_shipping_method' ); ?>" class="wc-autoship-shipping wc-autoship-shipping-admin">

		<div class="row">
			<!-- card section -->
			<section class="card col-xs-12">
				<!-- Breadcrumbs -->
				<ol class="breadcrumb text-center">
					<li><a href="<?php echo add_query_arg( array( 'customer_id' => $customer->get_id() ), get_permalink( get_option( 'wc_autoship_menu_page_id' ) ) ); ?>"><?php echo __( 'Manage Autoship', 'wc-autoship' ); ?></a></li>
					<li class="active"><a href="<?php echo add_query_arg( array( 'customer_id' => $customer->get_id() ), get_permalink( get_option( 'wc_autoship_shipping_page_id' ) ) ); ?>"><?php echo __( 'Shipping &amp; Delivery', 'wc-autoship' ); ?></a></li>
				</ol>
				<hr />
	
				<div class="col-xs-12 col-sm-6">
					<div class="well well-card wc-autoship-current-shipping">
						<h4><?php echo __( 'Shipping Address', 'wc-autoship' ); ?></h4>
	
						<p class="wc-autoship-shipping-address">
							<?php echo esc_html( $customer->get( 'shipping_first_name' ) ); ?> <?php echo esc_html( $customer->get( 'shipping_last_name' ) ); ?><br />
							<?php echo esc_html( $customer->get( 'shipping_address_1' ) ); ?> <?php echo esc_html( $customer->get( 'shipping_address_2' ) ); ?><br />
							<?php echo esc_html( $customer->get( 'shipping_city' ) ); ?>, <?php echo esc_html( $customer->get( 'shipping_state' ) ); ?> <?php echo esc_html( $customer->get( 'shipping_postcode' ) ); ?><br />
							<?php echo esc_html( $customer->get( 'shipping_country' ) ); ?>
						</p>
					</div>
					<?php $edit_shipping_path = get_option( 'permalink_structure' ) ? 'edit-address/shipping/' : '&amp;edit-address=shipping'; ?>
					<a
						class="wc-autoship-edit-shipping-address-link btn btn-primary btn-sm center-block"
						href="<?php echo admin_url( '/user-edit.php' ) . '?user_id=' . esc_attr( $customer->get_id() ); ?>"><?php echo __( 'Edit Customer', 'wc-autoship' ); ?></a>
				</div>
	
				<div class="col-xs-12 col-sm-6">
					<div class="well well-card wc-autoship-current-shipping">
						<h4><?php echo __( 'Shipping Method', 'wc-autoship' ); ?></h4>
	
						<div style="display: none">
							<input type="hidden" name="customer_id"
								value="<?php echo esc_html( $customer->get_id() ); ?>" />
						</div>
						<?php foreach ( $shipping_methods as $m => $method ): ?>
							<?php $rates = $method->rates; ?>
							<?php foreach( $rates as $rate ): ?>
								<div class="wc-autoship-customer-shipping-method radio">
									<label
										for="wc-autoship-customer-shipping-method-input-<?php echo $rate->id; ?>">
										<input type="radio"
										id="wc-autoship-customer-shipping-method-input-<?php echo $rate->id; ?>"
										class="wc-autoship-customer-shipping-method-input"
										name="shipping_method"
										<?php checked( $customer->get_shipping_method(), $rate->id ); ?>
										value="<?php echo esc_attr( $rate->id ); ?>" /> 
											&nbsp;<?php echo esc_html( $rate->label ); ?>
										</label>
								</div>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</div>
					<button type="submit" id="wc-autoship-update-shipping-method-btn"
						class="wc-autoship-update-shipping-method-btn btn btn-primary btn-sm btn-block center-block"><?php echo __( 'Update shipping method', 'wc-autoship' );?></button>
				</div>
	
			</section>
		</div>
		
	</form>

</div>

<?php do_action( 'wc_autoship_after_autoship_shipping', $customer->get_id() ); ?>