<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-fafcing aspects of the plugin.
 *
 * @link       http://www.codetides.com/
 * @since      1.0.0
 *
 * @pafckage    Advanced_Floating_Content
 * @subpafckage Advanced_Floating_Content/admin/views
 */
?>
<div class="afc-panel">                        	
    <div class="afc-panel-div">
        <label for="bafckground_color"><?php _e('Position','advanced-floating-content')?></label>
        <select style="width:22%;" name="ct_afc_position_place" id="ct_afc_position_place">
                <?php
                    $options = array('fixed'=>'Fixed','absolute'=>'Absolute');
                    foreach($options as $key => $value) { 
                    ?>
                    <option value="<?php echo $key;?>" <?php if ($key==get_text_value(get_the_ID(),'ct_afc_position_place','fixed')) {?> selected="selected" <?php } ?>><?php echo $value;?></option>
                    <?php } ?>
            </select>
        <select style="width:22%;" name="ct_afc_position_y" id="ct_afc_position_y">
                <?php
                    $options = array('top'=>'Top','bottom'=>'Bottom');
                    foreach($options as $key => $value) { 
                    ?>
                    <option value="<?php echo $key;?>" <?php if ($key==get_text_value(get_the_ID(),'ct_afc_position_y','yes')) {?> selected="selected" <?php } ?>><?php echo $value;?></option>
                    <?php } ?>
            </select>
           <select style="width:22%;" name="ct_afc_position_x" id="ct_afc_position_x">
                <?php
                    $options = array('left'=>'Left','right'=>'Right');
                    foreach($options as $key => $value) { 
                ?>
                <option value="<?php echo $key;?>" <?php if ($key==get_text_value(get_the_ID(),'ct_afc_position_x','yes')) {?> selected="selected" <?php } ?>><?php echo $value;?></option>
                <?php } ?>
            </select>     
        
    </div>
    <div class="afc-panel-div">
        <label for="button"><?php _e('Show Close Button','advanced-floating-content')?></label>
        <select style="width:71.1%;" name="ct_afc_close_button" id="ct_afc_close_button">
                <?php
                $options = array(
                    'yes'=>'Yes',
                    'no'=>'No'
                );
                foreach($options as $key => $value) { 
                ?>
                <option value="<?php echo $key;?>" <?php if ($key==get_text_value(get_the_ID(),'ct_afc_close_button','yes')) {?> selected="selected" <?php } ?>><?php echo $value;?></option>
                <?php } ?>
            </select>
    </div>
    <div class="afc-panel-div">
        <label for="width"><?php _e('Width','advanced-floating-content')?></label>
        <div class="control-input">
            <input type="text" name="ct_afc_width" id="ct_afc_width" value="<?php echo get_text_value(get_the_ID(),'ct_afc_width',100)?>" class="" style="width:61.4%;">
            <select style="width:30%;" name="ct_afc_width_unit" id="ct_afc_width_unit">
                <?php
                $options = array(
                    'px'=>'Pixels',
                    '%'=>'Percentage'
                );
                foreach($options as $key => $value) { 
                ?>
                <option value="<?php echo $key;?>" <?php if ($key==get_text_value(get_the_ID(),'ct_afc_width_unit','px')) {?> selected="selected" <?php } ?>><?php echo $value;?></option>
                <?php } ?>
            </select>
        </div>        
    </div> 
    <div class="afc-panel-div">
        <label for="bafckground_color"><?php _e('Background Color','advanced-floating-content')?></label>
        <div class="control-input">
            <input type="text" name="ct_afc_background_color" id="ct_afc_background_color" value="<?php echo get_text_value(get_the_ID(),'ct_afc_background_color','#FFFFFF')?>" class="color-picker-afc">
        </div>
    </div>
	<div class="afc-panel-div">
        <label for="margin"><?php _e('Margin','advanced-floating-content')?></label>
        <div class="control-input">
            <input type="text" name="ct_afc_margin_top" id="ct_afc_margin_top" value="<?php echo get_text_value(get_the_ID(),'ct_afc_margin_top',0)?>" class="" style="width:21%;">
            <input type="text" name="ct_afc_margin_right" id="ct_afc_margin_right" value="<?php echo get_text_value(get_the_ID(),'ct_afc_margin_right',0)?>" class="" style="width:21%;">
            <input type="text" name="ct_afc_margin_bottom" id="ct_afc_margin_bottom" value="<?php echo get_text_value(get_the_ID(),'ct_afc_margin_bottom',0)?>" class="" style="width:21%;">
            <input type="text" name="ct_afc_margin_left" id="ct_afc_margin_left" value="<?php echo get_text_value(get_the_ID(),'ct_afc_margin_left',0)?>" class="" style="width:21.3%;">
        </div>            
    </div>
    <div class="afc-panel-div">
        <label for="border"><?php _e('Border','advanced-floating-content')?></label>
        <div class="control-input">
            <input type="text" name="ct_afc_border_top" id="ct_afc_border_top" value="<?php echo get_text_value(get_the_ID(),'ct_afc_border_top',0)?>" style="width:21%;">
            <input type="text" name="ct_afc_border_right" id="ct_afc_border_right" value="<?php echo get_text_value(get_the_ID(),'ct_afc_border_right',0)?>" style="width:21%;">
            <input type="text" name="ct_afc_border_bottom" id="ct_afc_border_bottom" value="<?php echo get_text_value(get_the_ID(),'ct_afc_border_bottom',0)?>" style="width:21%;">
            <input type="text" name="ct_afc_border_left" id="ct_afc_border_left" value="<?php echo get_text_value(get_the_ID(),'ct_afc_border_left',0)?>" style="width:21.3%;">
        </div>            
    </div>
    <div class="afc-panel-div">
        <label for="border_properties"><?php _e('Border Properties','advanced-floating-content')?></label>
        <div class="control-input">
            <select style="width:35%;" name="ct_afc_border_type" id="ct_afc_border_type">
                <?php
                $options = array(
                    'dotted'=>'dotted',
                    'solid'=>'solid',
					'double'=>'double',
					'dashed'=>'dashed',
					'groove'=>'groove',
					'ridge'=>'ridge',
					'inset'=>'inset',
					'outset'=>'outset'
					
                );
                foreach($options as $key => $value) { 
                ?>
                <option value="<?php echo $key;?>" <?php if ($key==get_text_value(get_the_ID(),'ct_afc_border_type','solid')) {?> selected="selected" <?php } ?>><?php echo $value;?></option>
                <?php } ?>
            </select>
            
            <input type="text" name="ct_afc_border_color" id="ct_afc_border_color" value="<?php echo get_text_value(get_the_ID(),'ct_afc_border_color','#FFFFFF')?>" class="color-picker-afc">
            
            <select style="width:35%;" name="ct_afc_border_radius" id="ct_afc_border_radius">
                <?php
                $options = array(
                    '0'=>'Straight Cornor',
                    '1'=>'Round Cornor'
                );
                foreach($options as $key => $value) { 
                ?>
                <option value="<?php echo $key;?>" <?php if ($key==get_text_value(get_the_ID(),'ct_afc_border_radius','0')) {?> selected="selected" <?php } ?>><?php echo $value;?></option>
                <?php } ?>
            </select>
            
        </div>            
    </div>                       
</div>
<div id="advanced-floating-content-meta-box-nonce" class="hidden">
  <?php wp_nonce_field( 'advanced_floating_content_save', 'advanced_floating_content_nonce' ); ?>
</div>