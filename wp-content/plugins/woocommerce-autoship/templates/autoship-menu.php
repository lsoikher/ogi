<?php
/* @var $customer WC_Autoship_Models_Customer */
/* @var $schedules WC_Autoship_Models_Schedule[] */
?>

<?php do_action( 'wc_autoship_before_autoship_menu', $customer->get_id() ); ?>

<div class="wc-autoship-container">
	<div class="row">
		<!-- card section -->
		<section class="card col-xs-12">
			<!-- Breadcrumbs -->
			<ol class="breadcrumb text-center">
				<li><a href="<?php echo get_permalink( get_option( 'wc_autoship_menu_page_id' ) ); ?>"><?php echo __( 'Autoship', 'wc-autoship' ); ?></a></li>
			</ol>
			<hr />
			
			<!-- autship menu -->
			<div class="wc-autoship-menu">
				<div class="row">
					<div class="col-xs-12 col-sm-10 col-sm-offset-1">
						<a class="wc-autoship-menu-link btn btn-block btn-xl btn-card"
							href="<?php echo get_permalink( get_option( 'wc_autoship_schedules_page_id' ) ); ?>"> 
							<span class="wc-autoship-number text-center"><?php echo count( $schedules ); ?></span> 
							<?php echo __( 'Autoship Schedules', 'wc-autoship' ); ?>
							<span class="glyphicon glyphicon-chevron-right" style="float: right"></span>
						</a>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-10 col-sm-offset-1">
						<a class="wc-autoship-menu-link btn btn-block btn-xl btn-card"
							href="<?php echo get_permalink( get_option( 'wc_autoship_billing_page_id' ) ); ?>"> 
							<span class="glyphicon glyphicon-credit-card wc-autoship-menu-icon text-center"></span>
							<?php echo __( 'Autoship Billing', 'wc-autoship' ); ?>
							<span class="glyphicon glyphicon-chevron-right" style="float: right"></span>
						</a>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-10 col-sm-offset-1">
						<a class="wc-autoship-menu-link btn btn-block btn-xl btn-card"
							href="<?php echo get_permalink( get_option( 'wc_autoship_shipping_page_id' ) ); ?>"> 
							<i class="fa fa-truck wc-autoship-menu-icon text-center"></i>
							<?php echo __( 'Autoship Shipping', 'wc-autoship' ); ?>
							<span class="glyphicon glyphicon-chevron-right" style="float: right"></span>
						</a>
					</div>
				</div>
			</div>
		</section>
		<!-- end card section -->
	</div>
</div>

<?php do_action( 'wc_autoship_after_autoship_menu', $customer->get_id() ); ?>