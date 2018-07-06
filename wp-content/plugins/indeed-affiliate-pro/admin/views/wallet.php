<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Wallet', 'uap');?></h3>
		<div class="inside">
			<div class="row">
				<div class="col-xs-8">
					<h3><?php _e('Activate/Hold Wallet', 'uap');?></h3>
					<p><?php _e('Affiliates will have the options to spend their Earnings directly into the Website during further purchases based on Generated Coupons with specific flat discount.', 'uap');?></p>
					<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<?php $checked = ($data['metas']['uap_wallet_enable']) ? 'checked' : '';?>
						<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_wallet_enable');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="uap_wallet_enable" value="<?php echo $data['metas']['uap_wallet_enable'];?>" id="uap_wallet_enable" /> 
				</div>
			</div>
			<div class="row" style="margin-top: 30px;">
				<div class="col-xs-8">
					<p><?php _e('Establish a minimum amount available for Affiliate to be able to move his Earnings from his account into his Wallet. Only Referrals that are Verified but not Paid yet can be available for converting into Coupons into Affiliate Wallet', 'uap');?></p>
				</div>
			</div>	
			<div class="row" style="margin-bottom: 20px;">
				<div class="col-xs-4">			
					<div class="input-group" style="margin-top: 10px;">
						<label class="input-group-addon"><?php _e('Minimum Amount', 'uap');?></label>
						<input type="number" class="form-control" step="0.01" name="uap_wallet_minimum_amount" value="<?php echo $data['metas']['uap_wallet_minimum_amount'];?>" />
						
					</div>				
				</div>
			</div>

			<div class="uap-submit-form"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>								
		</div>
	</div>
</form>					