<?php $data['template_with_cols'] = array('uap-register-6', 'uap-register-11', 'uap-register-12', 'uap-register-13');?>
<div class="uap-register-form  <?php echo $data['template'];?>">
	<style><?php echo $data['css'];?></style>
	<form action="<?php echo @$data['action'];?>" method="post" name="<?php echo $data['form_name'];?>" id="<?php echo $data['form_id'];?>" class="uap-form-create-edit" enctype="multipart/form-data" >

		<?php do_action('uap_register_form_before_form_fields');?>
		
		<?php if (!empty($data['form_fields'])):
				$i = 0;
				$stop = ceil(@$data['count_register_fields']/2);

		?>
			<?php foreach ($data['form_fields'] as $form_field): ?>
				<?php 
					if (empty($form_field)){
						continue;
					}
					$i++;
					if (in_array($data['template'], $data['template_with_cols']) ):
						if ($i==1):?>
							<div class="uap-register-col">
						<?php endif;
						if ($i-1==$stop):	?>							
							</div><div class="uap-register-col">
						<?php endif;?>
					<?php endif;?>
					<?php echo $form_field;?>
			<?php endforeach;?>
		<?php endif;?>
		<?php if (in_array($data['template'], $data['template_with_cols']) ):?>
			</div>
		<?php endif;?>
		<?php if ($data['template']=='uap-register-7'):?>
			<div class="uap-temp7-row">
		<?php endif;?>
		<?php if (!empty($data['hiddens'])):?>
			<?php foreach ($data['hiddens'] as $value): ?>
				<?php echo $value;?>
			<?php endforeach;?>
		<?php endif;?>		
		
		<?php do_action('uap_register_form_before_submit_button');?>
		
		<div class="uap-submit-form">
			<?php echo $data['submit_button'];?>
		</div>
		<?php if ($data['template']=='uap-register-7'):?>
		</div>
		<?php endif;?>
	</form>
</div>
	
<?php if (!empty($data['js'])): ?>
<script><?php echo $data['js'];?></script>
<?php endif;?>