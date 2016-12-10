<div id="special-options">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="postbox-container-2" class="postbox-container">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox popup-builder-special-postbox">
					<div class="handlediv js-special-title" title="Click to toggle"><br></div>
					<h3 class="hndle ui-sortable-handle js-special-title">
						<span>
						<?php
							global $POPUP_TITLES;
							$popupTypeTitle = $POPUP_TITLES[$popupType];
							echo $popupTypeTitle." <span>options</span>";
						?>
						</span>
					</h3>
					<div class="special-options-content">
						<div class="sg-text-align">
							<h1 >Live Preview</h1>
							<input type="text" class="js-contact-text-inputs js-contact-name" value="" placeholder="<?php echo @$sgContactNameLabel;?>">
							<input type="text" class="js-contact-text-inputs js-contact-subject" value="" placeholder="<?php echo @$sgContactSubjectLabel;?>">
							<input type="text" class="js-contact-text-inputs js-contact-email" value="" placeholder="<?php echo @$sgContactEmailLabel;?>">
							<textarea placeholder="<?php echo @$sgContactMessageLabel?>" class="js-contact-message js-contact-text-area"></textarea><br>
							<input type="button" value="Submit" class="js-contact-submit-btn"><br>
							<hr>
						</div>
						<span class="liquid-width"><b>General options</b></span><br>
						<span class="liquid-width">Show form after content:</span><input type="checkbox" name="show-form-to-top" <?php echo $sgShowFormToTop; ?>>
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">
If this option is checked Contact Form will be shown above of your content.</span><br>
						<span class="liquid-width"><b>Name</b></span><input class="js-checkbox-acordion" data-contact-rel="js-contact-name" type="checkbox" name="contact-name-status" <?php echo $sgContactNameStatus; ?>>
						<div class="sub-options-content">
							<span class="liquid-width">Label:</span><input class="input-width-static sg-contact-fileds" data-contact-rel="js-contact-name" type="text" name="contact-name" value="<?php echo esc_attr(@$sgContactNameLabel); ?>"/>
							<span class="liquid-width">Required:</span><input type="checkbox" name="contact-name-required" <?php echo $sgContactNameRequired; ?>>
							<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">This field will be required.</span>
						</div><br>

						<span class="liquid-width"><b>Subject</b></span><input class="js-checkbox-acordion" data-contact-rel="js-contact-subject" type="checkbox" name="contact-subject-status" <?php echo $sgContactSubjectStatus; ?>>
						<div class="sub-options-content">
							<span class="liquid-width">Label:</span><input class="input-width-static sg-contact-fileds" data-contact-rel="js-contact-subject" type="text" name="contact-subject" value="<?php echo esc_attr(@$sgContactSubjectLabel); ?>"/><br>
							<span class="liquid-width">Required:</span><input type="checkbox" name="contact-subject-required" <?php echo $sgContactSubjectRequired; ?>>
							<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">This field will be required.</span>
						</div><br>

						<span class="liquid-width">Email:</span>
						<input type="text" class="input-width-static sg-contact-fileds" data-contact-rel="js-contact-email" name="contact-email" value="<?php echo esc_attr(@$sgContactEmailLabel); ?>"><br>

						<span class="liquid-width">Message:</span>
						<input class="input-width-static sg-contact-fileds" data-contact-rel="js-contact-message" type="text" name="contact-message" value="<?php echo esc_attr(@$sgContactMessageLabel); ?>"/>

						<span class="liquid-width">Receive mail:</span>
						<input class="input-width-static sg-contact-fileds" type="email" name="contact-receive-email" value="<?php echo esc_attr(@$sgContactResiveEmail); ?>"/>

						<span class="liquid-width">Send error message:</span>
						<input class="input-width-static sg-contact-fileds" type="text" name="contact-fail-message" value="<?php echo esc_attr(@$sgContactFailMessage); ?>"/>

						<span class="liquid-width">Required field message:</span>
						<input class="input-width-static" type="text" name="contact-validation-message" value="<?php echo esc_attr(@$sgContactValidationMessage);?>"><br>

						<span class="liquid-width">Invalid email field message:</span>
						<input class="input-width-static" type="text" name="contact-validate-email" value="<?php echo esc_attr(@$sgContactValidateEmail);?>"><br>

						<span class="liquid-width">Success message</span>
						<input class="input-width-static" type="text" name="contact-success-message" value="<?php echo esc_attr(@$sgContactSuccessMessage);?>"><br>

						<span class="liquid-width"><b>Input styles</b></span><br>
						<!--
							Text inputs options
						-->
						<span class="liquid-width">Width:</span>
						<input type="text" class="input-width-static" name="contact-inputs-width" data-contact-rel="js-contact-text-inputs" value="<?php echo esc_attr(@$sgContactInputsWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Height:</span>
						<input type="text" class="input-width-static" name="contact-inputs-height" data-contact-rel="js-contact-text-inputs" value="<?php echo esc_attr(@$sgContactInputsHeight); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Border width:</span>
						<input type="text" class="input-width-static" name="contact-inputs-border-width" data-contact-rel="js-contact-text-inputs" value="<?php echo esc_attr(@$sgContactInputsBorderWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example:"5px".</span><br>

						<span class="liquid-width">Background color:</span>
						<div class="color-picker"><input class="sg-contact-btn-color" id="sgOverlayColor" data-contact-rel="js-contact-text-inputs" data-contact-area-rel="js-contact-text-area" type="text" name="contact-text-input-bgcolor" value="<?php echo esc_attr(@$sgContactTextInputBgcolor); ?>"></div><br>

						<span class="liquid-width">Border color:</span>
						<div class="color-picker"><input class="sg-contact-btn-border-color" id="sgOverlayColor" data-contact-rel="js-contact-text-inputs" data-contact-area-rel="js-contact-text-area" type="text" name="contact-text-bordercolor" value="<?php echo esc_attr(@$sgContactTextBordercolor); ?>"></div><br>

						<span class="liquid-width">Text color:</span>
						<div class="color-picker"><input class="sg-contact-btn-text-color" id="sgOverlayColor" data-contact-rel="js-contact-text-inputs" data-contact-area-rel="js-contact-text-area" type="text" name="contact-inputs-color" value="<?php echo esc_attr(@$sgContactInputsColor); ?>"></div><br>

						<span class="liquid-width">Placeholder color:</span>
						<div class="color-picker"><input class="sg-contact-placeholder-color" id="sgOverlayColor" data-contact-rel="js-contact-text-inputs" data-contact-area-rel="js-contact-text-area" type="text" name="contact-placeholder-color" value="<?php echo esc_attr(@$sgContactPlaceholderColor); ?>"></div><br>


						<!--
							Textarea button options
						-->
						<span class="liquid-width"><b>Textarea style</b></span><br>

						<span class="liquid-width">Width:</span>
						<input type="text" class="input-width-static" name="contact-area-width" data-contact-rel="js-contact-text-area" value="<?php echo esc_attr(@$sgContactAreaWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Height:</span>
						<input type="text" class="input-width-static" name="contact-area-height" data-contact-rel="js-contact-text-area" value="<?php echo esc_attr(@$sgContactAreaHeight); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Reszie</span>
						<?php echo sgCreateSelect($sgTextAreaResizeOptions,'sg-contact-resize',@$sgContactResize)?><br>

						<span class="liquid-width"><b>Sumbmit button style</b></span><br>
						<!--
							Submit button options
						-->

						<span class="liquid-width">Width:</span>
						<input type="text" class="input-width-static" data-contact-rel="js-contact-submit-btn" name="contact-btn-width" value="<?php echo esc_attr(@$sgContactBtnWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Height:</span>
						<input type="text" class="input-width-static" data-contact-rel="js-contact-submit-btn" name="contact-btn-height" value="<?php echo esc_attr(@$sgContactBtnHeight); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Title:</span>
						<input type="text" class="input-width-static" data-contact-rel="js-contact-submit-btn" name="contact-btn-title" value="<?php echo esc_attr(@$sgContactBtnTitle); ?>"><br>

						<span class="liquid-width">Title (in progress):</span>
						<input type="text" class="input-width-static" data-contact-rel="js-contact-submit-btn" name="contact-btn-progress-title" value="<?php echo esc_attr(@$sgContactBtnProgressTitle); ?>"><br>

						<span class="liquid-width">Background color:</span>
						<div class="color-picker"><input class="sg-contact-btn-color" id="sgOverlayColor" data-contact-rel="js-contact-submit-btn" type="text" name="contact-button-bgcolor" value="<?php echo esc_attr(@$sgContactButtonBgcolor); ?>"></div><br>

						<span class="liquid-width">Text color:</span>
						<div class="color-picker"><input class="sg-contact-btn-text-color" id="sgOverlayColor" data-contact-rel="js-contact-submit-btn" type="text" name="contact-button-color" value="<?php echo esc_attr(@$sgContactButtonColor); ?>"></div><br>
						<?php
							@$contactParams = array(
								'inputsWidth' => $sgContactInputsWidth,
								'buttnsWidth' => $sgContactBtnWidth,
								'inputsHeight' => $sgContactInputsHeight,
								'buttonHeight' => $sgContactBtnHeight,
								'procesingTitle' => $sgContactBtnProgressTitle,
								'placeholderColor' => $sgContactPlaceholderColor,
								'btnTextColor' => $sgContactButtonColor,
								'btnBackgroundColor' => $sgContactButtonBgcolor,
								'inputsBackgroundColor' => $sgContactTextInputBgcolor,
								'inputsColor' => $sgContactInputsColor,
								'inputsBorderColor' => $sgContactTextBordercolor,
								'contactInputsBorderWidth' => $sgContactInputsBorderWidth,
								'contactAreaWidth' => $sgContactAreaWidth,
								'contactAreaHeight' => $sgContactAreaHeight,
								'contactResize' => $sgContactResize
							);
						?>
						<?php  wp_localize_script('sg_contactForm', 'contactFrontend', $contactParams);
							echo "<script type=\"text/javascript\">
								jQuery(document).ready(function() {
									sgContactObj = new SgContactForm();
									sgContactObj.livePreview();
									sgContactObj.buildStyle();
								});

							</script>";
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>