<table id="wc_autoship_options_table">
	<thead>
		<tr>
			<th><?php echo __( 'ID' ); ?></th>
			<th><?php echo __( 'Name' ); ?></th>
			<th><?php echo __( 'Frequency(days)' ); ?></th>
			<th><?php echo __( 'Actions' ); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="5">
				<a href="javascript:;" 
					id="wc_autoship_add_autoship_item"><?php 
					echo __( 'Add autoship item' );
				?></a>
			</td>
		</tr>
	</tfoot>
	<tbody>
		<?php if ( is_array( $options ) ): ?>
			<?php foreach ( $options as $item ): ?>
				<tr>
					<td><input type="text" 
						class="wc-autoship-autoship-field wc-autoship-autoship-field-id"
						name="wc_autoship_autoship_options[<?php echo esc_html( $item['id'] ); ?>][id]" 
						value="<?php echo esc_html( $item['id'] ); ?>" 
						/></td>
					<td><input type="text" 
						class="wc-autoship-autoship-field wc-autoship-autoship-field-name"
						name="wc_autoship_autoship_options[<?php echo esc_html( $item['id'] ); ?>][name]"
						value="<?php echo esc_html( $item['name'] ); ?>"
						/></td>
					<td><input type="text" 
						class="wc-autoship-autoship-field wc-autoship-autoship-field-frequency" 
						name="wc_autoship_autoship_options[<?php echo esc_html( $item['id'] ); ?>][frequency]" 
						value="<?php echo esc_html( $item['frequency'] ); ?>"
						/></td>
					<td>
						<a href="javascript:;" class="remove-autoship-item"><?php 
							echo __( 'Remove' );
						?></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<script>
// <![CDATA[ 
(function($) {
	var id_field_keyup_handler = function() {
		var id = this.value.replace(/[^\w]+/, '_');
		this.value = id;
		$(this).parents('tr').find('.wc-autoship-autoship-field').each(function() {
			this.name = this.name.replace(
				/^wc_autoship_autoship_options\[[^\]]*\]/,
				'wc_autoship_autoship_options[' + id + ']'
			);
		});
	};
	
	var remove_autoship_item_handler = function() {
		$(this).parents('tr').remove();
	};
	
	$(function() {
		$('.wc-autoship-autoship-field-id').keyup(id_field_keyup_handler);
		$('.remove-autoship-item').click(remove_autoship_item_handler);

		$('#wc_autoship_add_autoship_item').click(function() {
			var $row = $('<tr></tr>');
			var $row_num = $(this).find('tr').length;
			$row.append('<td><input type="text" '
				+ 'class="wc-autoship-autoship-field wc-autoship-autoship-field-id" '
				+ 'name="wc_autoship_autoship_options[-][id]" ' 
				+ '/></td>'
			);
			$row.append('<td><input type="text" '
				+ 'class="wc-autoship-autoship-field wc-autoship-autoship-field-name" '
				+ 'name="wc_autoship_autoship_options[-][name]" ' 
				+ '/></td>'
			);
			$row.append('<td><input type="text" '
				+ 'class="wc-autoship-autoship-field wc-autoship-autoship-field-frequency" '
				+ 'name="wc_autoship_autoship_options[-][frequency]" ' 
				+ '/></td>'
			);
			var $remove_link = $('<a href="javascript:;"><?php echo __( 'Remove' ); ?></a>')
				.click(remove_autoship_item_handler);
			$row.append($('<td></td>').append($remove_link));
			$('#wc_autoship_options_table').find('tbody').append($row);
			$row.find('.wc-autoship-autoship-field-id').keyup(id_field_keyup_handler);
		});
	});
})(jQuery);
// ]]>
</script>