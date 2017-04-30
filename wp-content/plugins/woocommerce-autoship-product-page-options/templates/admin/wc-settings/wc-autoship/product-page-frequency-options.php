<tr valign="top">
<th scope="row" class="titledesc">
<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
		<?php echo $description['tooltip_html']; ?>
	</th>
	<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
		<input type="hidden" name="wc_autoship_product_page_frequency_options" value="yes" />
		<table id="wc-autoship-product-page-frequency-options-table">
			<thead>
				<tr>
					<th><?php echo __( 'Frequency', 'wc-autoship-product-page' ); ?></th>
					<th><?php echo __( 'Name', 'wc-autoship-product-page' ); ?></th>
					<th>&times;</th>
				</tr>
			</thead>
			<tfoot>
				<td><input type="text" id="wc-autoship-product-page-frequency" placeholder="<?php echo __( 'Enter Frequency', 'wc-autoship-product-page' ); ?>" /></td>
				<td><input type="text" id="wc-autoship-product-page-frequency-name" placeholder="<?php echo __( 'Enter Name', 'wc-autoship-product-page' ); ?>" /></td>
				<td><button type="button" id="wc-autoship-product-page-frequency-button"><?php echo __( 'Add', 'wc-autoship-product-page' ); ?></button></td>
			</tfoot>
			<tbody id="wc-autoship-product-page-frequency-options-body">
				<?php if ( ! empty( $frequency_options ) ): ?>
					<?php foreach ( $frequency_options as $frequency => $name ): ?>
						<tr id="wc-autoship-frequency-option-<?php echo esc_attr( $frequency ); ?>" class="wc-autoship-frequency-option" data-frequency="<?php echo esc_attr( $frequency ); ?>">
							<td class="wc-autoship-frequency-option-frequency-column">
								<input type="hidden" name="wc_autoship_product_page_frequency_options_array[<?php echo esc_attr( $frequency ); ?>]"
									value="<?php echo esc_attr( $name ); ?>" />
								<span><?php echo esc_html( $frequency ); ?></span>
							</td>
							<td class="wc-autoship-frequency-option-name-column"><?php echo esc_html( $name ); ?></td>
							<td class="wc-autoship-frequency-option-delete-column"><button class="wc-autoship-frequency-option-delete">&times;</button></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</td>
</tr>