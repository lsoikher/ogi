<?php
$bg = get_option('woocommerce_email_background_color');
$body = get_option('woocommerce_email_body_background_color');
$base = get_option('woocommerce_email_base_color');
$base_text = wc_light_or_dark($base, '#202020', '#ffffff');
$text = get_option('woocommerce_email_text_color');

$bg_darker_10 = wc_hex_darker($bg, 10);
$body_darker_10 = wc_hex_darker($body, 10);
$base_lighter_20 = wc_hex_lighter($base, 20);
$base_lighter_40 = wc_hex_lighter($base, 40);
$text_lighter_20 = wc_hex_lighter($text, 20);

?>


<!--BEGINNGING OF BODY TEMPLATE -->
<p>
	<?php echo apply_filters( 'wc_autoship_schedule_email_message', esc_html( $message ) ); ?>
</p>
<p>
	<?php echo __( '<a href="' . wc_get_account_endpoint_url( 'autoship-schedules' ) . '">Log in</a> if you would like to review or make changes to this order.', 'wc-autoship' ); ?>
</p>
<h2 style="color:<?php echo $base ?>;display:block;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left"><?php echo __( 'Order to be processed on ' . esc_html( $next_ship_date ), 'wc-autoship' ); ?></h2>
<table cellspacing="0" cellpadding="6"
	   style="width:100%; font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif; color:<?php echo $text_lighter_20 ?>; border:1px solid <?php echo $body_darker_10 ?>"
	   border="1">
	<thead>
	<tr>
		<th scope="col"
			style="text-align:left;color:<?php echo $text_lighter_20 ?>;border:1px solid <?php echo $body_darker_10 ?>;padding:12px">
			<?php echo __( 'Product', 'wc-autoship' ); ?>
		</th>
		<th scope="col"
			style="text-align:left;color:<?php echo $text_lighter_20 ?>;border:1px solid <?php echo $body_darker_10 ?>;padding:12px">
			<?php echo __( 'Quantity', 'wc-autoship' ); ?>
		</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $items as $item ): ?>
		<?php $product = $item->get_product(); ?>
		<?php if ( ! empty( $product ) ): ?>
			<tr>
				<td style="text-align:left;vertical-align:middle;border:1px solid #eee;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word;color:<?php echo $text_lighter_20 ?>;padding:12px"><?php echo esc_html( $product->get_title() ); ?>
					<br>
					<small><?php echo __( 'Autoship: Every ' . esc_html( $frequency ) . ' days<br />', 'wc-autoship' ); ?>
						<?php if ( $product->is_type('variation') ) {
							echo "Variation!!!"; // $item->get_product()->get_formatted_variation_attributes();
						} ?>
					</small>
				</td>
				<td style="text-align:left;vertical-align:middle;border:1px solid #eee;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;color:<?php echo $text_lighter_20 ?>;padding:12px"><?php echo esc_html( $item->get_quantity() ); ?></td>
			</tr>
		<?php endif; ?>
	<?php endforeach; ?>

	</tbody>
	<tfoot>
	<tr>
		<th scope="row" colspan="1"
			style="text-align:left;color:<?php echo $text_lighter_20 ?>;border:1px solid <?php echo $body_darker_10 ?>;padding:12px"><?php echo __( 'Shipping Method:', 'wc-autoship' ); ?>
		</th>
		<td style="text-align:left;color:<?php echo $text_lighter_20 ?>;border:1px solid <?php echo $body_darker_10 ?>;padding:12px">
			<span><?php echo apply_filters( 'wc_autoship_schedule_email_shipping_method', esc_html( $shipping_method_id ) ); ?> </span>
		</td>
	</tr>
	<tr>
		<th scope="row" colspan="1"
			style="text-align:left;color:<?php echo $text_lighter_20 ?>;border:1px solid <?php echo $body_darker_10 ?>;padding:12px"><?php echo __( 'Payment Method:', 'wc-autoship' ); ?>
		</th>
		<td style="text-align:left;color:<?php echo $text_lighter_20 ?>;border:1px solid <?php echo $body_darker_10 ?>;padding:12px">
			<?php if ( ! empty( $payment_method ) ): ?>
				<span><?php echo apply_filters( 'wc_autoship_schedule_email_payment_method', esc_html( $payment_method->get_display_name() ) ); ?></span>
			<?php else: ?>
				<?php echo apply_filters( 'wc_autoship_schedule_email_no_payment_method', __( 'No payment method', 'wc-autoship' ) ); ?>
			<?php endif; ?>
		</td>
	</tr>
	</tfoot>
</table>
<h2 style="color:<?php echo $base ?>;display:block;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left"><?php echo __( 'Customer details', 'wc-autoship' ); ?></h2>
<ul>
	<li>
		<strong><?php echo __( 'Email:', 'wc-autoship' ); ?></strong> <span
			style="color:<?php echo $text ?>;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif"><a
				href="mailto:<?php echo esc_attr( $email ); ?>" target="_blank"><?php echo esc_html( $email ); ?></a></span>
	</li>
</ul>
<table cellspacing="0" cellpadding="0" style="width:100%;vertical-align:top" border="0">
	<tbody>
	<tr>
		<td valign="top" width="50%">
			<h3 style="color:<?php echo $base ?>;display:block;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:16px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left"><?php echo __( 'Billing address', 'wc-autoship' ); ?></h3>

			<p style="color:<?php echo $text ?>;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;margin:0 0 16px"><?php echo esc_html( $billing_first_name . " " . $billing_last_name ); ?><br><?php echo esc_html( $billing_address ); ?>
				<?php if ( ! empty( $billing_address2 ) ) {
					echo "<br />" . esc_html( $billing_address2 );
				} ?>
				<br /><?php echo esc_html( $billing_city . ", " . $billing_state . " " . $billing_postcode ); ?>
				<br /><?php echo esc_html( $billing_country ); ?></p>
		</td>
		<td valign="top" width="50%">
			<h3 style="color:<?php echo $base ?>;display:block;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:16px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left"><?php echo __( 'Shipping address', 'wc-autoship' ); ?></h3>

			<p style="color:<?php echo $text ?>;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;margin:0 0 16px"><?php echo esc_html( $shipping_first_name . " " . $shipping_last_name ); ?>
				<br><?php echo $shipping_address; ?>
				<?php if ( ! empty( $shipping_address2 ) ) {
					echo "<br />" . esc_html( $shipping_address2 );
				} ?>
				<br /><?php echo esc_html( $shipping_city . ", " . $shipping_state . " " . $shipping_postcode ); ?>
				<br /><?php echo esc_html( $shipping_country ); ?></p>
		</td>
	</tr>
	</tbody>
</table>
<!--END OF BODY TEMPLATE -->
