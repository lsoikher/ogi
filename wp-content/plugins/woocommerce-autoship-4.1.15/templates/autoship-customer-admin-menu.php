<?php
/* @var $customer WC_Autoship_Models_Customer */
?>

<div class="wc-autoship-container">
	<div class="wc-autoship-customer-admin-menu">
		<div class="row">
			<section class="card col-xs-12">
				<div class="wc-autoship-customer-admin-menu">
					<div class="row">
						<div class="col-xs-6 col-sm-7 col-md-8 text-left">
							<small><?php echo __( 'Managing Autoship Customer:', 'wc-autoship' ); ?></small>
							<h4>
								<a href="<?php echo admin_url( '/user-edit.php' ) . '?user_id=' . esc_attr( $customer->get_id() ); ?>" title="<?php echo __( 'Edit User', 'wc-autoship' ); ?>">
									<?php echo esc_html( $customer->get( 'billing_first_name' ) ); ?> <?php echo esc_html( $customer->get( 'billing_last_name' ) ); ?> 
									(<?php echo esc_html( $customer->get_id() ); ?>)
									<span class="glyphicon glyphicon-edit"></span>
								</a>
							</h4>
							<small>
								<strong><?php echo __( 'Email:', 'wc-autoship' ); ?></strong> <?php echo esc_html( $customer->get_email() ); ?>
								<br />
								<?php $user = $customer->get_user(); ?>
								<strong><?php echo __( 'Username:', 'wc-autoship' ); ?></strong> <?php echo esc_html( $user->user_login ); ?>
							</small>
						</div>
						<div class="col-xs-6 col-sm-5 col-md-4 text-left">
							<div class="wc-autoship-customer-admin-nav">
								<small><?php echo __( 'View All:', 'wc-autoship' ); ?></small>
								<ul class="nav nav-pills nav-stacked small">
									<li role="presentation"><a href="<?php echo admin_url( 'admin.php?page=wc_autoship' ); ?>">Autoship Customers</a></li>
									<li role="presentation"><a href="<?php echo admin_url( 'admin.php?page=wc_autoship_orders' ); ?>">Autoship Orders</a></li>
									<li role="presentation"><a href="<?php echo admin_url( 'admin.php?page=wc_autoship_schedules' ); ?>">Autoship Schedules</a></li>
								</ul>
							</div> 
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>