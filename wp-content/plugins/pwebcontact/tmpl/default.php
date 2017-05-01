<?php
/**
 * @version 2.1.8
 * @package Perfect Easy & Powerful Contact Form
 * @copyright © 2016 Perfect Web sp. z o.o., All rights reserved. https://www.perfect-web.co
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Piotr Moćko
 */

// No direct access
function_exists('add_action') or die;

$user = wp_get_current_user();

$row        = 0;
$column 	= 0;
$page 		= 0;
$pages 		= array();

$toggler = 
	 '<div id="pwebcontact'.$form_id.'_toggler" class="pwebcontact'.$form_id.'_toggler pwebcontact_toggler pweb-closed '.$params->get('togglerClass').'">'
    .'<span class="pweb-text">'.(($params->get('toggler_vertical', 0) AND !$params->get('toggler_vertical_type', 1)) ? ' ' : $params->get('toggler_name_open')).'</span>'
	.'<span class="pweb-icon"></span>'
	.'</div>';
	
$message =
	 '<div class="pweb-msg pweb-msg-'.$params->get('msg_position', 'after').'"><div id="pwebcontact'.$form_id.'_msg" class="pweb-progress">'
	.'<script type="text/javascript">document.getElementById("pwebcontact'.$form_id.'_msg").innerHTML="'.__('Initializing form...', 'pwebcontact').'"</script>'
	.'</div></div>';

?>
<!-- PWebContact -->

<?php if ($layout == 'modal' AND $params->get('handler') == 'button') : ?>
<div class="<?php echo $params->get('moduleClass'); ?>" dir="<?php echo $params->get('rtl', 0) ? 'rtl' : 'ltr'; ?>">
	<?php echo $toggler; ?>
</div>
<?php endif; ?>

<div id="pwebcontact<?php echo $form_id; ?>" class="pwebcontact <?php echo $params->get('positionClass').' '.$params->get('moduleClass'); ?>" dir="<?php echo $params->get('rtl', 0) ? 'rtl' : 'ltr'; ?>">
	
	<?php 
    if ( ($layout == 'accordion' AND $params->get('handler') == 'button') 
        OR ( ( ($layout == 'slidebox' AND !$params->get('toggler_slide')) OR $layout == 'modal') AND $params->get('handler') == 'tab' ) 
       )
        echo $toggler; 
    ?>
	
	<?php if ($layout == 'modal') : ?><div id="pwebcontact<?php echo $form_id; ?>_modal" class="pwebcontact-modal modal fade<?php if ((int)$params->get('bootstrap_version', 2) === 2) echo ' hide'; ?>" style="display:none"><?php endif; ?>
    
    <div id="pwebcontact<?php echo $form_id; ?>_box" class="pwebcontact-box <?php echo $params->get('moduleClass').' '.$params->get('boxClass'); ?>" dir="<?php echo $params->get('rtl', 0) ? 'rtl' : 'ltr'; ?>">
    
    <div class="pwebcontact-container-outset">
    <div id="pwebcontact<?php echo $form_id; ?>_container" class="pwebcontact-container<?php if ($layout == 'modal' AND (int)$params->get('bootstrap_version', 2) === 3) echo ' modal-dialog'; ?>">
    <div class="pwebcontact-container-inset">
	
		<?php if ($layout == 'slidebox' AND $params->get('handler') == 'tab' AND $params->get('toggler_slide')) echo $toggler; ?>
		
		<?php if ($layout == 'accordion' OR ($layout == 'modal' AND !$params->get('modal_disable_close', 0))) : ?>
		<button type="button" class="pwebcontact<?php echo $form_id; ?>_toggler pweb-button-close" aria-hidden="true"<?php if ($value = $params->get('toggler_name_close')) echo ' title="'.$value.'"' ?> data-role="none">&times;</button>
		<?php endif; ?>
		
		<?php if ($layout == 'accordion') : ?><div class="pweb-arrow"></div><?php endif; ?>
		
		<form name="pwebcontact<?php echo $form_id; ?>_form" id="pwebcontact<?php echo $form_id; ?>_form" class="pwebcontact-form" action="<?php echo esc_url( home_url() ); ?>" method="post" accept-charset="utf-8">
			
			<?php if ($params->get('msg_position', 'after') == 'before') echo $message; ?>
			
			<div class="pweb-fields">
			<?php 
            
            $filedTypes = array('text', 'name', 'email', 'phone', 'subject', 'password', 'date', 'textarea', 'select', 'multiple', 'radio', 'checkboxes', 'checkbox', 'checkbox_modal');
            
            $custom_text_fields = 0;
            $header_fields      = 0;
            
			/* ----- Form --------------------------------------------------------------------------------------------- */
			foreach ($fields as $field) :
			
				/* ----- Separators ----- */
				if ($field['type'] == 'page') : 
					$page++;
                    $row = 0;
					$column = 0;
                    $pages[$page] = array();
                
                elseif ($field['type'] == 'row') : 
					$row++;
					$column = 0;
                    $pages[$page][$row] = array();
                
                elseif ($field['type'] == 'column') : 
					// create new empty column slot
                    $column++;
                    $pages[$page][$row][$column] = null;
				
				
				else :
					
                    ob_start();
                    
                    
                    /* ----- Buttons ------------------------------------------------------------------------------------------ */
                    if ($field['type'] == 'button_send') :
                     ?>
					<div class="pweb-field-container pweb-field-buttons">
						<div class="pweb-field">
							<button id="pwebcontact<?php echo $form_id; ?>_send" type="<?php echo ($params->get('autocomplete_inputs', 1) == 1 ? 'submit' : 'button'); ?>" class="btn pweb-button-send" data-role="none"><?php _e($field['label'] ? $field['label'] : 'Send', 'pwebcontact') ?></button>
							<?php if ($params->get('reset_form', 1) == 3) : ?>
							<button id="pwebcontact<?php echo $form_id; ?>_reset" type="reset" class="btn pweb-button-reset" style="display:none" data-role="none"><i class="glyphicon glyphicon-remove-sign"></i> <?php _e($params->get('button_reset', 'Reset'), 'pwebcontact') ?></button>
							<?php endif; ?>
							<?php if ($params->get('msg_position', 'after') == 'button' OR $params->get('msg_position', 'after') == 'popup') echo $message; ?>
                        </div>
					</div>
                    <?php
					
                    
					/* ----- Custom Text --------------------------------------------------------------------------- */
					elseif ($field['type'] == 'custom_text') : 
						$field['id'] = 'pwebcontact'.$form_id.'_text-'.$custom_text_fields++;
					?>
					<div class="pweb-field-container pweb-field-custom-text" id="<?php echo $field['id']; ?>">
						<?php $text = __($field['value'], 'pwebcontact');
                        if ($field['parse_shortcodes']) $text = do_shortcode($text);
                        echo $field['line_breaks'] ? nl2br($text) : $text; ?>
					</div>
					<?php 
                    
                    /* ----- Header --------------------------------------------------------------------------- */
					elseif ($field['type'] == 'header') : 
						$field['id'] = 'pwebcontact'.$form_id.'_header-'.$header_fields++;
					?>
					<div class="pweb-field-container pweb-field-header" id="<?php echo $field['id']; ?>">
						<?php _e($field['label'], 'pwebcontact'); ?>
					</div>
					<?php 
					
                    
					/* ----- Mail to list ------------------------------------------------------------------------------- */
					elseif ($field['type'] == 'mailto_list') :
						
						$optValues = isset($field['values']) ? explode("\n", $field['values']) : array();
                        if (count($optValues)) :

                            $field['id'] 	= 'pwebcontact'.$form_id.'_mailto';
                            $i 			= 1;
					?>
					<div class="pweb-field-container pweb-field-select pweb-field-mailto">
						<div class="pweb-label">
							<label for="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>-lbl">
								<?php _e($field['label'], 'pwebcontact'); ?>
								<span class="pweb-asterisk">*</span>
							</label>
						</div>
						<div class="pweb-field">
                            <div class="pweb-field-shadow">
                                <select name="mailto" id="<?php echo $field['id']; ?>" class="required" data-role="none">
                                    <option value=""><?php _e('-- Select --', 'pwebcontact'); ?></option>
                                <?php foreach ($optValues as $value) : 
                                    // Skip empty rows
                                    if (empty($value)) continue;
                                    // Get recipient
                                    $recipient = explode('|', $value);
                                    // Skip incorrect rows
                                    if (!array_key_exists(1, $recipient)) continue;
                                ?>
                                    <option value="<?php echo $i++; ?>"><?php esc_html_e($recipient[1], 'pwebcontact'); ?></option>
                                <?php endforeach; ?>
                                </select>
                            </div>
						</div>
					</div>
					<?php
						endif;
					
					
					/* ----- Captcha ---------------------------------------------------------------------------- */
					elseif ($field['type'] == 'captcha') :
						
                        $params->def('captcha', 'grecaptcha');
                        
                        require_once (dirname(__FILE__).'/../captcha.php');
                        
                        $captcha_options = array('form_id' => $form_id);
                        if (isset($field['theme']) AND $field['theme'])
                            $captcha_options['theme'] = $field['theme'];
                        $captcha = new PWebContact_Captcha($captcha_options);
                        
						$field['id'] = 'pwebcontact'.$form_id.'_captcha';
					?>
					<div class="pweb-field-container pweb-field-captcha <?php if (!$field['label']) echo 'pweb-field-buttons'; ?>">
						<?php if ($field['label']) : ?>
                        <div class="pweb-label">
							<label for="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>-lbl">
								<?php _e($field['label'], 'pwebcontact'); ?>
								<span class="pweb-asterisk">*</span>
							</label>
						</div>
                        <?php endif; ?>
						<div class="pweb-field pweb-captcha">
							<?php echo $captcha->display($field['id'], 'required'); ?>
						</div>
					</div>
					<?php

					/* ----- Newsletter ---------------------------------------------------------------------------- */
					elseif ($field['type'] == 'newsletter' AND $field['newsletter_visibility']) : 

                                            $field['id'] = 'pwebcontact'.$form_id.'_newsletter';
                                            $newsletter_lists = (array)$field['newsletter_lists'];
                            $newsletter_inline 	= count($newsletter_lists) == 1;

						if ($newsletter_inline) :

							$list = json_decode($newsletter_lists[0], true);
							reset($list);
							$newsletter_value = key($list);
							$newsletter_title = $list[$newsletter_value];
						?>

						<div class="pweb-field-container pweb-field-single-checkbox pweb-field-newsletter pweb-field-buttons">
							<div class="pweb-field">
								<input type="checkbox" name="newsletter[]" id="<?php echo $field['id']; ?>" value="<?php echo $newsletter_value; ?>" class="pweb-checkbox pweb-single-checkbox<?php if (isset($field['required']) AND $field['required']) echo ' required'; ?>" <?php if ($field['newsletter_visibility'] == 1) echo 'checked="checked"'; ?> data-role="none">
								<label for="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>-lbl"<?php if (isset($field['tooltip']) AND $field['tooltip']) echo ' class="pweb-tooltip" title="'.esc_attr__($field['tooltip'], 'pwebcontact').'"'; ?>>
									<?php _e($field['label'] ? $field['label'] : 'Subscribe to newsletter', 'pwebcontact'); ?>
                                                                        <?php if (isset($field['required']) AND $field['required']) : ?><span class="pweb-asterisk">*</span><?php endif; ?>
								</label>
							</div>
						</div>

						<?php else: ?>
						<div class="pweb-field-container pweb-field-checkboxes pweb-field-newsletter">
							<div class="pweb-label">
								<label id="<?php echo $field['id']; ?>-lbl">
									<?php _e($field['label'] ? $field['label'] : 'Subscribe to newsletter', 'pwebcontact'); ?>
                                                                        <?php if (isset($field['required']) AND $field['required']) : ?><span class="pweb-asterisk">*</span><?php endif; ?>
								</label>
							</div>
							<div class="pweb-field">
								<fieldset id="<?php echo $field['id']; ?>" class="pweb-fields-group<?php if (isset($field['tooltip']) AND $field['tooltip']) echo ' pweb-tooltip" title="'.esc_attr__($field['tooltip'], 'pwebcontact'); ?>">
								<?php $i = 0; 
                                                                foreach($newsletter_lists as $newsletter_list) :

                                    $list = json_decode($newsletter_list, true);
                                    reset($list);
                                    $newsletter_value = key($list);
                                    $newsletter_title = $list[$newsletter_value];
								?>
								<input type="checkbox" name="newsletter[]" id="<?php echo $field['id'].'_'.$i; ?>" value="<?php echo esc_attr($newsletter_value); ?>" class="pweb-checkbox pweb-fieldset<?php if ($i == 0 AND isset($field['required']) AND $field['required']) echo ' required'; ?>" <?php if ($field['newsletter_visibility'] == 1) echo 'checked="checked"'; ?> data-role="none">
								<label for="<?php echo $field['id'].'_'.$i++; ?>">
									<?php esc_html_e($newsletter_title, 'pwebcontact'); ?>
								</label>
								<?php endforeach; ?>
								</fieldset>
							</div>
						</div>
							<?php
                                                        endif;

                    /* ----- Email copy --------------------------------------------------------------------------- */
                    elseif ($field['type'] == 'email_copy' AND $params->get('email_copy', 2) == 1) :
                            $field['id'] = 'pwebcontact'.$form_id.'_copy';
					?>
					<div class="pweb-field-container pweb-field-checkbox pweb-field-copy">
						<div class="pweb-field">
							<input type="checkbox" name="copy" id="<?php echo $field['id']; ?>" value="1" class="pweb-checkbox" data-role="none">
							<label for="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>-lbl">
								<?php _e($field['label'] ? $field['label'] : 'Send a copy to yourself', 'pwebcontact'); ?>
							</label>
						</div>
					</div>
					<?php 
                    
					
					/* ----- Upload ----------------------------------------------------------------------------------- */
					elseif ($field['type'] == 'upload') :
						
                        $params->def('show_upload', 1);
                    
                        $field['id'] = 'pwebcontact'.$form_id.'_uploader';

                        $field['attributes'] = null;
                        $field['class'] = null;
                        $field['title'] = array();
                        if ($params->get('upload_show_limits')) {
                            $exts = explode('|', $params->get('upload_allowed_ext'));
                            $types = array();
                            foreach ($exts as $ext) {
                                $pos = strpos($ext, '?');
                                if ($pos !== false) {
                                    $types[] = str_replace(substr($ext, $pos-1, 2), '', $ext);
                                    $types[] = str_replace('?', '', $ext);
                                }
                                else {
                                    $types[] = $ext;
                                }
                            }
                            $field['title'][] = esc_attr(sprintf(__('Select a file or drag and drop on form. Max file size %s, max number of files %s, allowed file types: %s. ', 'pwebcontact'), 
                                floatval($params->get('upload_size_limit', 1)).'MB',
                                intval($params->get('upload_files_limit', 5)),
                                implode(', ', $types)
                            ));
                        }
                        if (isset($field['tooltip']) AND $field['tooltip']) {
                            $field['title'][] = esc_attr__($field['tooltip'], 'pwebcontact');
                        }
                        if (count($field['title'])) {
                            $field['class'] = ' pweb-tooltip';
                            $field['attributes'] .= ' title="'.implode(' ', $field['title']).'"';
                        }
					?>
					<div class="pweb-field-container pweb-field-uploader">
						<div class="pweb-label">
							<label for="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>-lbl">
								<?php _e($field['label'] ? $field['label'] : 'Attachment', 'pwebcontact'); ?>
								<?php if (isset($field['required']) AND $field['required']) : ?><span class="pweb-asterisk">*</span><?php endif; ?>
							</label>
						</div>
						<div class="pweb-field pweb-uploader" id="<?php echo $field['id']; ?>_container">
							<div class="fileupload-buttonbar">
								<span class="fileinput-button btn<?php echo $field['class']; ?>"<?php echo $field['attributes']; ?>>
				                    <i class="glyphicon glyphicon-plus-sign"></i>
                                    <span><?php _e((isset($field['button']) AND $field['button']) ? $field['button'] : 'Add files', 'pwebcontact'); ?></span>
				                    <input type="file" name="files[]" multiple="multiple" id="<?php echo $field['id']; ?>"<?php if (isset($field['required']) AND $field['required']) echo ' class="pweb-validate-uploader"'; ?> data-role="none">
				                </span>
							</div>
							<div class="files"></div>
							<div class="templates" style="display:none" aria-hidden="true">
								<div class="template-upload fade">
									<span class="ready"><i class="glyphicon glyphicon-upload"></i></span>
									<span class="warning"><i class="glyphicon glyphicon-warning-sign"></i></span>
				                	<span class="name"></span>
				                	<span class="size"></span>
				                	<span class="error invalid"></span>
				                	<a href="#" class="cancel"><i class="glyphicon glyphicon-remove"></i><?php _e('Cancel', 'pwebcontact'); ?></a>
				                	<div class="progress progress-striped active"><div class="bar progress-bar" style="width:0%"></div></div>
				                </div>
								<div class="template-download fade">
									<span class="success"><i class="glyphicon glyphicon-ok"></i></span>
									<span class="warning"><i class="glyphicon glyphicon-warning-sign"></i></span>
				                	<span class="name"></span>
				                    <span class="size"></span>
				                    <span class="error invalid"></span>
				                    <a href="#" class="delete"><i class="glyphicon glyphicon-trash"></i><?php _e('Delete', 'pwebcontact'); ?></a>
				                </div>
							</div>
						</div>
					</div>
					<?php 
                    
                    /* ----- Fields ----------------------------------------------------------------------------------- */
					elseif (in_array($field['type'], $filedTypes)) : 
						
                        $field['id'] = 'pwebcontact'.$form_id.'_field-'.$field['alias'];
						$field['name'] = 'fields['.$field['alias'].']';
					?>
					<div class="pweb-field-container pweb-field-<?php echo $field['type']; ?> pweb-field-<?php echo $field['alias']; ?>">
						<?php 
						
						if ($field['type'] != 'checkbox' AND $field['type'] != 'checkbox_modal') : 
						/* ----- Label -------------------------------------------------------------------------------- */ ?>
						<div class="pweb-label">
							<label id="<?php echo $field['id']; ?>-lbl"<?php if ($field['type'] != 'checkboxes' AND $field['type'] != 'radio') echo ' for="'.$field['id'].'"'; ?>>
								<?php _e($field['label'], 'pwebcontact'); ?>
								<?php if (isset($field['required']) AND $field['required']) : ?><span class="pweb-asterisk">*</span><?php endif; ?>
							</label>
						</div>
						<?php endif; ?>
						<div class="pweb-field">
							<?php 
							
							/* ----- Text fields: text, name, email, phone, subject, password, date ------------------------- */
							if (in_array($field['type'], array('text', 'name', 'email', 'phone', 'subject', 'password', 'date'))) : 
								
								if ($user->ID AND ($field['type'] == 'name' OR $field['type'] == 'email') AND $params->get('user_data', 1) > 0) {
									$field['value'] = $field['type'] == 'email' ? $user->user_email : $user->display_name;
                                    //TODO addHiddenField(); ob_get_clean(); continue; remove some CSS
								}
								
								$field['attributes'] = null;
								$field['classes'] = array('pweb-input');
								if (isset($field['required']) AND $field['required']) 
									$field['classes'][] = 'required';
								
								if (isset($field['validation']) AND $field['validation']) 
									$field['classes'][] = 'pweb'.$form_id.'-validate-'.$field['alias'];
								
								if (isset($field['tooltip']) AND $field['tooltip']) {
									$field['classes'][] = 'pweb-tooltip';
									$field['attributes'] .= ' title="'.esc_attr__($field['tooltip'], 'pwebcontact').'"';
								}
                                                                if ($params->get('autocomplete_inputs', 1) == 1)
                                                                        $field['attributes'] .= ' autocomplete="on"';

								if (count($field['classes']))
									$field['attributes'] .= ' class="'.implode(' ', $field['classes']).'"';
								
								switch ($field['type']) {
									case 'email':
										$field['classes'][] = 'email';
										$type = 'email';
										break;
									case 'password':
										$type = 'password';
										break;
									case 'phone':
										$type = 'tel';
										break;
									default:
										$type = 'text';
								}
							?>
							<div class="pweb-field-shadow">
                                <input type="<?php echo $type; ?>" name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>"<?php echo $field['attributes']; ?> value="<?php esc_attr_e($field['value'], 'pwebcontact'); ?>" data-role="none">
                            </div>
							<?php 
                            if ($field['type'] == 'date') : ?>
							<span class="pweb-calendar-btn" id="<?php echo $field['id']; ?>_btn"><i class="glyphicon glyphicon-calendar"></i></span>
							<?php endif;
                                unset($type);
							
							
							/* ----- Textarea ------------------------------------------------------------------------- */
							elseif ($field['type'] == 'textarea') :
								$field['attributes'] = null;
								$field['classes'] = array();
								
								$field['attributes'] .= ' rows="'.($field['rows'] ? (int)$field['rows'] : 5).'"';
								if (isset($field['maxlength']) AND $field['maxlength']) {
									$field['attributes'] .= ' maxlength="'.$field['maxlength'].'"';
								}
								if (isset($field['required']) AND $field['required']) 
									$field['classes'][] = 'required';
								
								if (isset($field['tooltip']) AND $field['tooltip']) {
									$field['classes'][] = 'pweb-tooltip';
									$field['attributes'] .= ' title="'.esc_attr__($field['tooltip'], 'pwebcontact').'"';
								}
								if (count($field['classes']))
									$field['attributes'] .= ' class="'.implode(' ', $field['classes']).'"';
							?>
							<div class="pweb-field-shadow">
                                <textarea name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>" cols="50"<?php echo $field['attributes']; ?> data-role="none"><?php esc_html_e($field['value'], 'pwebcontact'); ?></textarea>
                            </div>
							<?php if ($field['maxlength']) : ?>
							<div class="pweb-chars-counter"><?php echo sprintf(__('%s characters left', 'pwebcontact'), '<span id="'.$field['id'].'-limit">'.$field['maxlength'].'</span>'); ?></div>
							<?php endif; ?>	
							<?php 
							
							
							/* ----- Select and Multiple select ------------------------------------------------------- */
							elseif ($field['type'] == 'select' OR $field['type'] == 'multiple') : 
								$optValues = isset($field['values']) ? explode("\n", $field['values']) : array();
								$field['attributes'] = null;
								$field['classes'] = array();
								
								if (isset($field['required']) AND $field['required']) 
									$field['classes'][] = 'required';
								
								if ($field['type'] == 'multiple') 
								{
									$field['classes'][] = 'pweb-multiple';
									$field['name'] 		 .= '[]';
									
									$optCount 		= count($optValues);
									$field['rows'] 	= (isset($field['rows']) AND $field['rows']) ? (int)$field['rows'] : 4;
									$field['rows'] 	= $field['rows'] > $optCount ? $optCount : $field['rows'];
									unset($optCount);
                                    
									$field['attributes'] .= ' multiple="multiple" size="'.$field['rows'].'"';
								}
								else {
									$field['classes'][] = 'pweb-select';
								}
								
								if (isset($field['tooltip']) AND $field['tooltip']) {
									$field['classes'][] = 'pweb-tooltip';
									$field['attributes'] .= ' title="'.esc_attr__($field['tooltip'], 'pwebcontact').'"';
								}
								
								if (count($field['classes']))
									$field['attributes'] .= ' class="'.implode(' ', $field['classes']).'"';
							?>
							<div class="pweb-field-shadow">
                                <select name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>"<?php echo $field['attributes']; ?> data-role="none">
                                <?php if ($field['type'] == 'select' AND $field['default']) : ?>
                                    <option value=""><?php esc_html_e($field['default'], 'pwebcontact'); ?></option>
                                <?php endif; ?>
                                <?php foreach ($optValues as $value) : ?>
                                    <?php if (empty($value)) continue; ?>
                                    <option value="<?php echo esc_attr( preg_replace('/[\r\n]+/', '', $value) ); ?>"><?php esc_html_e($value, 'pwebcontact'); ?></option>
                                <?php endforeach; ?>
                                </select>
                            </div>
							<?php 
                                unset($optValues);
							
							
							/* ----- Checkboxes and Radio group ------------------------------------------------------- */
							elseif ($field['type'] == 'checkboxes' OR $field['type'] == 'radio') : 
								$i 			= 0;
								
								$type 		= $field['type'] == 'checkboxes' ? 'checkbox' : 'radio';
                                $optValues  = isset($field['values']) ? explode("\n", $field['values']) : array();
								
								$optCount 	= count($optValues);
								$optColumns = isset($field['cols']) ? (int)$field['cols'] : 0;
								$optRows	= false;
								if ($optColumns > 1 AND $optCount >= $optColumns) 
								{
									$optCount 	= count($optValues);
									$optRows 	= ceil($optCount / $optColumns);
									$width 		= floor(100 / $optColumns);
									$cols 		= 1;
								}
								if ($field['type'] == 'checkboxes') 
									$field['name'] .= '[]';
							?>
							<fieldset id="<?php echo $field['id']; ?>" class="pweb-fields-group<?php if (isset($field['tooltip']) AND $field['tooltip']) echo ' pweb-tooltip" title="'.esc_attr__($field['tooltip'], 'pwebcontact'); ?>">
							<?php 
							/* ----- Options in multiple columns ----- */
							if ($optRows) : ?>
							<div class="pweb-column pweb-width-<?php echo $width; ?>">
							<?php foreach ($optValues as $value) : ?>
                                <?php if (empty($value)) continue; ?>
								<input type="<?php echo $type; ?>" name="<?php echo $field['name']; ?>" id="<?php echo $field['id'].'_'.$i; ?>" value="<?php echo esc_attr( preg_replace('/[\r\n]+/', '', $value) ); ?>" class="pweb-<?php echo $type; ?> pweb-fieldset<?php if ($i == 0 AND isset($field['required']) AND $field['required']) echo ' required'; ?>" data-role="none">
								<label for="<?php echo $field['id'].'_'.$i++; ?>">
									<?php esc_html_e($value, 'pwebcontact'); ?>
								</label>
							<?php // Column separator
							if (($i % $optRows) == 0 AND $cols < $optColumns) : $cols++; ?>
							</div><div class="pweb-column pweb-width-<?php echo $width; ?>">
							<?php endif;
							endforeach; ?>
							</div>
							<?php 
                                unset($width, $cols);
                            
							/* ----- Options in one column ----- */
							else :
							foreach ($optValues as $value) : ?>
                                <?php if (empty($value)) continue; ?>
								<input type="<?php echo $type; ?>" name="<?php echo $field['name']; ?>" id="<?php echo $field['id'].'_'.$i; ?>" value="<?php echo esc_attr( preg_replace('/[\r\n]+/', '', $value) ); ?>" class="pweb-<?php echo $type; ?> pweb-fieldset<?php if ($i == 0 AND isset($field['required']) AND $field['required']) echo ' required'; ?>" data-role="none">
								<label for="<?php echo $field['id'].'_'.$i++; ?>">
									<?php esc_html_e($value, 'pwebcontact'); ?>
								</label>
							<?php endforeach;
							endif; ?>
							</fieldset>
							<?php 
                                unset($type, $optValues, $optCount, $optColumns, $optRows, $i);
							
							
							/* ----- Single checkbox ------------------------------------------------------------------ */
							elseif ($field['type'] == 'checkbox') : ?>
								<input type="checkbox" name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>" class="pweb-checkbox pweb-single-checkbox<?php if (isset($field['required']) AND $field['required']) echo ' required'; ?>" value="1" data-role="none">
								<label for="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>-lbl"<?php if (isset($field['tooltip']) AND $field['tooltip']) echo ' class="pweb-tooltip" title="'.esc_attr__($field['tooltip'], 'pwebcontact').'"'; ?>>
                                    <?php _e($field['label'], 'pwebcontact'); ?>
                                    <?php if (isset($field['required']) AND $field['required']) : ?>
                                        <span class="pweb-asterisk">*</span>
                                    <?php endif; ?>
								</label>
							<?php
                            
                            
                            /* ----- Single checkbox with Terms & Conditions ----------------------------------------- */
							elseif ($field['type'] == 'checkbox_modal') : ?>
								<input type="checkbox" name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>" class="pweb-checkbox pweb-single-checkbox<?php if (isset($field['required']) AND $field['required']) echo ' required'; ?>" value="1" data-role="none">
								<label for="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>-lbl"<?php if (isset($field['tooltip']) AND $field['tooltip']) echo ' class="pweb-tooltip" title="'.esc_attr__($field['tooltip'], 'pwebcontact').'"'; ?>>
								<?php if (isset($field['url']) AND $field['url']) : ?>
									<a href="<?php echo $field['url']; ?>" target="_blank"<?php if ($field['target'] == 1) echo ' class="pweb-modal-url"'; ?>>
                                        <?php esc_html_e($field['label'], 'pwebcontact'); ?>
                                        <span class="glyphicon glyphicon-new-window"></span>
                                    </a>
								<?php else : 
									esc_html_e($field['label'], 'pwebcontact'); 
								endif; ?>
								<?php if (isset($field['required']) AND $field['required']) : ?>
									<span class="pweb-asterisk">*</span>
								<?php endif; ?>
								</label>
							<?php 
                            
                            endif; 
                            ?>
						</div>
					</div>
					<?php 
                    else :
                        ob_get_clean();
                        continue;
                    endif;
				
                    // create new column slot
                    $column++;
                    if (isset($pages[$page][$row][$column])) {
                        $pages[$page][$row][$column] .= ob_get_clean(); 
                    }
                    else {
                        $pages[$page][$row][$column] = ob_get_clean(); 
                    }
				
				endif;
			endforeach; 
            
	
			/* ----- Display form pages, rows and columns ------------------------------------------------------------------- */
				$pages_count = count($pages);
				foreach ($pages as $page => $rows) 
				{
					if ($pages_count > 1) echo '<div class="pweb-page" id="pwebcontact'.$form_id.'_page-'.$page.'">';
					
                    foreach ($rows as $row => $columns) 
                    {
                        if (!count($columns)) continue;
                        
                        //TODO join rows if have the same number of columns
                        echo '<div class="pweb-row">';
                        
                        $width = floor(100 / count($columns));
                        foreach ($columns as $column) 
                        {
                            $column = $column ? $column : '&nbsp;';
                            
                            if ($width < 100) 
                                echo '<div class="pweb-column pweb-width-'.$width.'">'.$column.'</div>';
                            else
                                echo '<div>'.$column.'</div>';
                        }
                        
                        echo '</div>';
                    }
                    
					if ($pages_count > 1) echo '</div>';
				}
				
			/* ----- Display pages navigation ------------------------------------------------------------------------- */
				if ($pages_count > 1) : ?>
					<div class="pweb-pagination">
						<button id="pwebcontact<?php echo $form_id; ?>_prev" class="btn pweb-prev" type="button" data-role="none"><span class="glyphicon glyphicon-chevron-left"></span> <?php _e('Previous', 'pwebcontact'); ?></button>
						<div class="pweb-counter">
							<span id="pwebcontact<?php echo $form_id; ?>_page_counter">1</span>
							<?php _e('of', 'pwebcontact'); ?>
							<span><?php echo $pages_count; ?></span>
						</div>
						<button id="pwebcontact<?php echo $form_id; ?>_next" class="btn pweb-next" type="button" data-role="none"><?php _e('Next', 'pwebcontact'); ?> <span class="glyphicon glyphicon-chevron-right"></span></button>
					</div>
				<?php endif;
			?>
			</div>
			
			<?php if ($params->get('msg_position', 'after') == 'after') echo $message; ?>
			
			<?php echo PWebContact::getHiddenFields($form_id); ?>
			<input type="hidden" name="<?php echo wp_create_nonce('pwebcontact'.$form_id); ?>" value="1" id="pwebcontact<?php echo $form_id; ?>_token">
		</form>
		
		<?php if ($params->get('show_upload', 0)) : ?>
		<div class="pweb-dropzone" aria-hidden="true"><div><?php _e('Drop files here to upload', 'pwebcontact'); ?></div></div>
		<?php endif; ?>
       
    </div>
	</div>
	</div>
    
	</div>
	<?php if ($layout == 'modal') : ?></div><?php endif; ?>
</div>

<script type="text/javascript">
<?php echo $script; ?>
</script>
<!-- PWebContact end -->
