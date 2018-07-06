<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Stripe v2 - Payouts', 'uap');?></h3>
		<div class="inside">
				<div class="row">
						<div class="col-xs-7">
							<h3><?php _e('Activate/Hold Stripe Gateway', 'uap');?></h3>
							<p><?php _e('Once is activated you can proceed payments to your Affiliate users via Stripe directly from Affiliate System', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
							<?php $checked = ($data['metas']['uap_stripe_v2_enable']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_stripe_v2_enable');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_stripe_v2_enable" value="<?php echo $data['metas']['uap_stripe_v2_enable'];?>" id="uap_stripe_v2_enable" /> 
							<p style="font-weight:bold;color: #9b4449;">This Payout option is on Beta Mode. If for some reason the new Stripe Payout system is not working properly use the first Stripe Payout system</p>
						</div>
				</div>
				<div class="uap-line-break"></div>	
				<div class="row">
					<div class="col-xs-4">
						<h4><?php _e('Sandbox', 'uap');?></h4>
						<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<?php $checked = ($data['metas']['uap_stripe_v2_sandbox']) ? 'checked' : '';?>
						<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_stripe_v2_sandbox');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
						</label>
						<input type="hidden" name="uap_stripe_v2_sandbox" value="<?php echo $data['metas']['uap_stripe_v2_sandbox'];?>" id="uap_stripe_v2_sandbox" /> 	
					</div>
				</div>	
				<div class="uap-line-break"></div>		
				<div class="row">
					<div class="col-xs-6">	
						<div class="uap-form-line">
							<label class="uap-label"><?php _e('Sandbox Secret Key', 'uap');?></label>
							<div>
								<input type="text" name="uap_stripe_v2_sandbox_secret_key" value="<?php echo $data['metas']['uap_stripe_v2_sandbox_secret_key'];?>" />	
							</div>							
						</div>
						<div class="uap-form-line">
							<label class="uap-label"><?php _e('Sandbox Publishable Key', 'uap');?></label>
							<div>
								<input type="text" name="uap_stripe_v2_sandbox_publishable_key" value="<?php echo $data['metas']['uap_stripe_v2_sandbox_publishable_key'];?>" />
							</div>
						</div>								
						<div class="uap-form-line">
							<label class="uap-label"><?php _e('Live Secret Key', 'uap');?></label>
							<div>
								<input type="text" name="uap_stripe_v2_secret_key" value="<?php echo $data['metas']['uap_stripe_v2_secret_key'];?>" />
							</div>
						</div>
						<div class="uap-form-line">
							<label class="uap-label"><?php _e('Live Publishable Key', 'uap');?></label>
							<div>
								<input type="text" name="uap_stripe_v2_publishable_key" value="<?php echo $data['metas']['uap_stripe_v2_publishable_key'];?>" />
							</div>
						</div>
					</div>
				</div>
				
				<div style="font-size: 11px; color: #333; padding-left: 10px;">
					<ul class="uap-info-list">
						<?php 
							//$notification_url = UAP_URL . 'public/stripe-webhook.php';
							$notification_url = site_url();
							$notification_url = trailingslashit($notification_url);
							$notification_url = add_query_arg('uap_act', 'stripe_payout', $notification_url);
						?>
						<li><?php _e('1. Go to', 'uap');?> <a href="http://stripe.com" target="_blank">http://stripe.com</a> <?php _e('and login with Username and password.', 'uap');?></li>
						<li><?php _e('2. After that click on "Dashboard", and then select "Your account" - "Account settings".', 'uap');?></li>
						<li><?php _e('3. A popup will appear and You must go to API Keys, here You will find the Secret Key and Publishable Key.', 'uap');?></li>
						<li><?php echo __('4. Don\'t forget to set Your Webhook at: ', 'uap') . '<b>' . $notification_url . '</b>';?></li>
						<li><?php _e('5. Be sure You use the same currency in Stripe account and in Ultimate Affiliate Pro settings.', 'uap');?></li>
						<li><?php echo __('For testing purpose You can find card credentials on: ', 'uap') . '<a href="https://stripe.com/docs/testing#test-debit-card-numbers" target="_blank">https://stripe.com/docs/testing#test-debit-card-numbers</a>';?></li>
					</ul>			
				</div>
				
				<div class="uap-submit-form"> 
					<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
				</div>								
		</div>
	</div>
</form>	