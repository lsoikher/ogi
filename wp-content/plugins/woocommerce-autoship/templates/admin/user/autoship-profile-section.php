<?php
/* @var $user WP_User */
?>
<table class="form-table">
	<tbody>
		<tr class="wc-autoship-wrap">
			<th><?php echo __( 'Autoship', 'wc-autoship' ); ?></th>
			<td>
				<a href="<?php echo add_query_arg( array( 'customer_id' => $user->ID ), get_permalink( get_option( 'wc_autoship_menu_page_id' ) ) ); ?>" class="button button-secondary">
					<?php echo __( 'Manage Autoship', 'wc-autoship' ); ?>
				</a>
			</td>
		</tr>
	</tbody>
</table>