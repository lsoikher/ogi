<?php
/**
 * @var array
 */
$items;
/**
 * @var string
 */
$id;
/**
 * @var string
 */
$title;
/**
 * @var string
 */
$desc;
?>

<tr>
	<td colspan="2">
		<table id="wc_autoship_options_table">
			<thead>
				<tr>
					<th><?php echo __( 'Name' ); ?></th>
					<th><?php echo __( 'Frequency(days)' ); ?></th>
					<th><?php echo __( 'Enabled' ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3">
						<?php echo esc_html( $desc ); ?>
					</td>
				</tr>
			</tfoot>
			<caption>
				<?php echo esc_html( $title ); ?>
			</caption>
			<tbody>
				<?php foreach( $items as $i => $item ): ?>
					<tr>
						<td><input type="text" 
							class="wc-autoship-autoship-field wc-autoship-autoship-field-name"
							name="<?php echo $id; ?>[<?php echo $i; ?>][name]"
							value="<?php echo esc_html( $item['name'] ); ?>"
							/></td>
						<td><input type="text" 
							class="wc-autoship-autoship-field wc-autoship-autoship-field-frequency" 
							name="<?php echo $id; ?>[<?php echo $i; ?>][frequency]" 
							value="<?php echo esc_html( $item['frequency'] ); ?>"
							/></td>
						<td><input type="checkbox" 
							class="wc-autoship-autoship-field wc-autoship-autoship-field-enabled" 
							name="<?php echo $id; ?>[<?php echo $i; ?>][enabled]" 
							<?php echo ($item['enabled'] == 'yes')? ' checked="checked" ': ''; ?>
							value="yes"
							/></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</td>
</tr>