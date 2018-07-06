<?php if (!empty($meta_arr['uap_login_custom_css'])): ?>
	<style><?php echo $meta_arr['uap_login_custom_css'];?></style>
<?php endif;?>
<div class="uap-pass-form-wrap <?php echo $meta_arr['uap_login_template'];?>">
	<form action="" method="post" >
		<input name="uapaction" type="hidden" value="reset_pass">
	
	<?php switch ($meta_arr['uap_login_template']){
			 case 'uap-login-template-3': ?>
			<div class="impu-form-line-fr">
				<input type="text" value="" name="email_or_userlogin" placeholder="<?php _e('Username or E-mail');?>" />
			</div>
			<div class="impu-form-submit">
				<input type="submit" value="<?php _e('Get New Password', 'uap');?>" name="Submit" class="button button-primary button-large" />
			</div>
		<?php break;
		case 'uap-login-template-4': ?>
		<div class="impu-form-line-fr">
			<i class="fa-uap fa-username-uap"></i><input type="text" value="" name="email_or_userlogin" placeholder="'.__('Username or E-mail').'" />
		</div>
		<div class="impu-form-submit">
			<input type="submit" value="<?php _e('Get New Password', 'uap');?>" name="Submit" class="button button-primary button-large" />
		</div>
	<?php break;?>
						
	<?php default:?>
		<div class="impu-form-line-fr">
			<span class="impu-form-label-fr impu-form-label-username"><?php _e('Username or E-mail', 'uap');?></span>
			<input type="text" value="" name="email_or_userlogin" />
		</div>
		<div class="impu-form-submit">
			<input type="submit" value="<?php _e('Get New Password', 'uap');?>" name="Submit" class="button button-primary button-large" />
		</div>
	<?php break;?>
	<?php }?>
	
	</form>
	<?php 
	if (!empty($data['success_message'])){
		echo "<div class='uap-reset-pass-success-msg'>" . $data['success_message'] . '</div>';
	} else if (!empty($data['error_message'])){
		echo "<div class='uap-wrapp-the-errors'>" . $data['error_message'] . '</div>';
	}
	?>		
</div>		
	
