<?php do_action( 'wc_autoship_before_no_autoship' ); ?>

<div class="wc-autoship-container">
	<div class="wc-autoship-no-autoship">
		<?php $message = get_option( 'wc_autoship_no_autoship_message' ); ?>
		<?php if ( $message ): ?>
			<?php echo $message; ?>
		<?php else: ?>
			<h3>You have no Autoship orders created.</h3>
			<p>The next time you checkout online <strong>any items you place on Autoship will appear here</strong> as upcoming orders that you can manage.</p>
		<?php endif; ?>
	</div>
</div>

<?php do_action( 'wc_autoship_after_no_autoship' ); ?>