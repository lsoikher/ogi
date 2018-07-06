<?php foreach ( $messages as $message ): ?>
<div class="notice is-dismissible <?php echo esc_attr( $message['type'] ); ?>">
	<p><?php echo $message['message']; ?></p>
</div>
<?php endforeach; ?>
