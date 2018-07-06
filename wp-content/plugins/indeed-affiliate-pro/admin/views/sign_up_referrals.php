			<form action="" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('SignUp Referrals', 'uap');?></h3>
					<div class="inside">	
					<div class="row">
						<div class="col-xs-5">
							<h3><?php _e('Activate/Hold SignUp Referrals', 'uap');?></h3>
							<p><?php _e('You can activate this option to take in place into your Affiliate system.', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_sign_up_referrals_enable']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_sign_up_referrals_enable');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_sign_up_referrals_enable" value="<?php echo $data['metas']['uap_sign_up_referrals_enable'];?>" id="uap_sign_up_referrals_enable" /> 
						</div>
					</div>	
					<div class="uap-line-break"></div>					
					<div class="uap-inside-item">
						<div class="row">
							<div class="col-xs-5">
								<h3><?php _e('Default Amount', 'uap');?></h3>
								<p><?php _e('Set the default Flat Amount that will be used when no special amount is set for certain Rank', 'uap');?></p>
								<div class="input-group">
								<span class="input-group-addon" id="basic-addon1">Amount</span>
									 <input type="number" class="form-control" min="0" step="0.01" class="uap-input-number" value="<?php echo $data['metas']['uap_sign_up_amount_default'];?>" name="uap_sign_up_amount_default" />
									 <div class="input-group-addon"><?php echo $data['amount_types']['flat'];?></div>	
								</div>
							</div>
						</div>							
						<div class="row" style="margin-bottom: 15px;">
							<div class="col-xs-5">
								<h3><?php _e('Default Referral Status', 'uap');?></h3>
								<select name="uap_sign_up_default_referral_status" class="form-control m-bot15"><?php
									foreach (array(1 => __('Unverified', 'uap'), 2 => __('Verified', 'uap')) as $k=>$v){
										$selected = ($data['metas']['uap_sign_up_default_referral_status']==$k) ? 'selected' : '';
										?>
										<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
										<?php									
									}
								?></select>
							</div>
						</div>	
					</div>
					<div class="uap-line-break"></div>					
					
					<div class="row">
						<div class="col-xs-4">
						<?php if (!empty($data['rank_list'])) :?>
						<h3><?php _e('Amount For Each Rank', 'uap');?></h3>
							<p><?php _e('Set a special SignUp Amount for each Rank. This option will become available into Rank Settings page also.', 'uap');?></p>
						<?php foreach ($data['rank_list'] as $id=>$label) :?>
								<div class="input-group" style="margin-bottom:20px;">
									<span class="input-group-addon" id="basic-addon1"><?php echo $label;?></span>
										<?php $value = ($data['rank_value_array'][$id]==-1) ? '' : $data['rank_value_array'][$id];?>
								 		<input type="number" class="form-control" min="0" step="0.01" class="uap-input-number" value="<?php echo $value;?>" name="<?php echo "signup_ranks_value[$id]";?>" />
								 		<div class="input-group-addon"><?php echo $data['amount_types']['flat'];?></div>	
									</div>						
							<?php endforeach;?>
						<?php endif;?>
						</div>
					</div>	
					<div class="uap-line-break"></div>
					<div class="row">
						<div class="col-xs-4">
						</div>
					</div>			
						
						<div class="uap-submit-form"> 
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>		
					</div>
				</div>
			</form>