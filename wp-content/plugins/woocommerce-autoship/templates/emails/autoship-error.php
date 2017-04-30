<p>Hello <?php echo $name ?>,<br /><br/>
	<?php echo esc_html( $message ); ?></p>
<p><a href="<?php echo wc_get_account_endpoint_url( 'autoship-schedules' ); ?>"><?php echo __( 'Please review your autoship orders.', 'wc-autoship' ); ?></a></p>