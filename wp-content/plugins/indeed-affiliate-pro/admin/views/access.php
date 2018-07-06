<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Roles allowed to enter into WordPress Admin Dashboard:', 'uap');?></h3>
		<div class="inside">		
			<div style="width: 49%; vertical-align: top; display: inline-block;">		
				<div class="uap-form-line" style="opacity: 0.7;">
					<span style="font-weight:bold; display:inline-block; width: 25%;"><?php _e('Administrator', 'uap');?></span>
					<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<input type="checkbox" class="uap-switch" onClick="" checked disabled/>
						<div class="switch" style="display:inline-block;"></div>
					</label>			
				</div>
				<?php 
					$roles = get_editable_roles();
					if (!empty($roles['administrator'])){
						unset($roles['administrator']);
					}
					if (!empty($roles['pending_user'])){
						unset($roles['pending_user']);	
					}							
					$count = count($roles) + 1;
					$break = ceil($count/2);
					$i = 1;
					foreach ($roles as $role=>$arr){
					?>
						<div class="uap-form-line">
							<span style="font-weight:bold; display:inline-block; width: 25%;"><?php echo $arr['name'];?></span>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = (in_array($role, $meta_values)) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uap_make_inputh_string(this, '<?php echo $role;?>', '#uap_dashboard_allowed_roles');" <?php echo $checked;?>/>
								<div class="switch" style="display:inline-block;"></div>
							</label>			
						</div>							
					<?php 	
					$i++;
						if ($count>7 && $i==$break){
						?>
						</div>
						<div style="width: 49%; vertical-align: top; display: inline-block;">	
						<?php 	
						}
					}///end of foreach
				?>
			</div>
			<input type="hidden" name="uap_dashboard_allowed_roles" id="uap_dashboard_allowed_roles" value="<?php echo $meta_value;?>" />
			<div class="uap-wrapp-submit-bttn iump-submit-form">
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>				
		</div>
	</div>
</div>