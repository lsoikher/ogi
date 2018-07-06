			<form action="" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('ReWrite Refferals', 'uap');?></h3>
					<div class="inside">
					<div class="row">
						<div class="col-xs-8">
							<h3><?php _e('Activate/Hold ReWrite Refferals', 'uap');?></h3>
							<p><?php _e('Decides if new Customer is re-assigned on last or first linked Affiliate. If the same Customer is referred with another Affiliate than the first one, you can decide if the referrence will be changed or not. ', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_rewrite_referrals_enable']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_rewrite_referrals_enable');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_rewrite_referrals_enable" value="<?php echo $data['metas']['uap_rewrite_referrals_enable'];?>" id="uap_rewrite_referrals_enable" /> 
						</div>
						</div>	
						<div class="uap-line-break"></div>				
						
						<div class="uap-submit-form" style="margin-top:40px;"> 
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>		
					</div>
				</div>
			</form>