<div class="uap-ap-wrap">

<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>

<?php if (!empty($data['items']) && is_array($data['items'])):?>
	<div>
	<div class="uap-row">
		<div class="uapcol-md-3">
			<div class="uap-account-no-box uap-account-box-green"><div class="uap-account-no-box-inside"><div class="uap-count"><?php echo uap_format_price_and_currency($data['currency'], $data['stats']['verified_referrals_amount']);?></div><div class="uap-detail"><?php _e("Verified Refferal Amount", 'uap');?></div></div></div>
		</div>	
		<div class="uapcol-md-3">
			<div class="uap-account-no-box uap-account-box-blue"><div class="uap-account-no-box-inside"><div class="uap-count"><?php echo uap_format_price_and_currency($data['currency'], $data['stats']['unverified_referrals_amount']);?></div><div class="uap-detail"><?php _e("Unverified Refferal Amount", 'uap');?></div></div></div>
		</div>
		<div class="uapcol-md-3">
			<div class="uap-account-no-box"><div class="uap-account-no-box-inside"><div class="uap-count"><?php echo $data['stats']['referrals'];?></div><div class="uap-detail"><?php _e('Referrals', 'uap');?></div></div></div>
		</div>
	</div>
	<?php echo $data['filter'];?>
		<table class="uap-account-table">
			  <thead>	
				<tr>	
					<th><?php _e("Id", 'uap');?></th>	
					<th><?php _e("Campaign", 'uap');?></th>
					<th><?php _e("Amount", 'uap');?></th>					
					<th><?php _e("From", 'uap');?></th>
					<?php if (!empty($data['print_source_details'])):?>
						<th><?php _e('Source Details', 'uap');?></th>
					<?php endif;?>
					<th><?php _e("Description", 'uap');?></th>
					<th><?php _e("Date", 'uap');?></th>
					<th><?php _e("Status", 'uap');?></th>
				</tr>
			  </thead>
			  <tbody class="uap-alternate">	
			<?php foreach ($data['items'] as $array) : ?>
				<tr>
					<td><?php echo $array['id'];?></td>
					<td><?php 
						if ($array['campaign']) {
							echo $array['campaign'];
						} else {
							echo '-';
						}
					?></td>
					<td style="font-weight:bold; color:#111;"><?php echo uap_format_price_and_currency($array['currency'], $array['amount']);?></td>
					<td><?php echo (empty($array['source'])) ? '' : uap_service_type_code_to_title($array['source']);?></td>
					<?php if (!empty($data['print_source_details'])):?>
						<td><?php 
							if ($indeed_db->referral_has_source_details($array['id'])):
								$url = add_query_arg('reference', $array['id'], $data['source_details_url']);
								?>
								<a href="<?php echo $url;?>" target="_blank"><?php _e('View', 'uap');?></a>
								<?php
							else :
								echo '-';
							endif;
						?></td>
					<?php endif;?>					
					<td><?php echo $array['description'];?></td>
					<td><?php echo uap_convert_date_to_us_format($array['date']);?></td>
					<td class="uap-special-label"><?php 
						if ($array['status']==0){
							_e('Refuse', 'uap');
						} else if ($array['status']==1){
							_e('Unverified', 'uap');
						} else if ($array['status']==2){
							_e('Verified', 'uap');
						}
					?></td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>
	</div>
<?php endif;?>

<?php if (!empty($data['pagination'])):?>
	<?php echo $data['pagination'];?>
<?php endif;?>
</div>