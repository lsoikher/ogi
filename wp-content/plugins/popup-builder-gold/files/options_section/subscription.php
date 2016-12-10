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
							<input type="text" class="js-subs-text-inputs js-subs-email-name" value="" placeholder="<?php echo @$sgSubscriptionEmail;?>">
							<input type="text" class="js-subs-text-inputs js-subs-first-name" value="" placeholder="<?php echo @$sgSubsFirstName;?>">
							<input type="text" class="js-subs-text-inputs js-subs-last-name" value="" placeholder="<?php echo @$sgSubsLastName; ?>">
							<input type="button" value="Submit" class="js-subs-submit-btn"><br>
							<hr>
						</div>
						<span class="liquid-width"><b>General options</b></span><br>

						<span class="liquid-width">Hide popup after submitting</span>
						<input type="checkbox" name="subs-dont-show-after-submitting" <?php echo $sgSubsDontShowAfterSubmitting;?>>
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">If user already subscribed don't show popup for that user.</span><br>

						<span class="liquid-width">Email:</span>
						<input type="text" class="input-width-static sg-subs-fileds" data-subs-rel="js-subs-email-name" name="subscription-email" value="<?php echo esc_attr(@$sgSubscriptionEmail); ?>"><br>

						<span class="liquid-width"><b>First name</b></span>
						<input class="js-checkbox-acordion" data-subs-rel="js-subs-first-name" type="checkbox" name="subs-first-name-status" <?php echo $sgSubsFirstNameStatus;?>>
						<div class="socials-content js-email-options-content">
							<span class="liquid-width">Your first Name:</span>
							<input class="input-width-static sg-subs-fileds" data-subs-rel="js-subs-first-name" type="text" name="subs-first-name" value="<?php echo esc_attr(@$sgSubsFirstName); ?>"/><br>
						</div><br>

						<span class="liquid-width"><b>last name</b></span>
						<input class="js-checkbox-acordion" type="checkbox" data-subs-rel="js-subs-last-name" name="subs-last-name-status" <?php echo $sgSubsLastNameStatus;?>>
						<div class="socials-content js-email-options-content">
							<span class="liquid-width">Your last name:</span>
							<input class="input-width-static sg-subs-fileds" data-subs-rel="js-subs-last-name" type="text" name="subs-last-name" value="<?php echo esc_attr(@$sgSubsLastName); ?>"/>
						</div><br>

						<span class="liquid-width">Required field message:</span>
						<input class="input-width-static" type="text" name="subs-validation-message" value="<?php echo esc_attr(@$sgSubsValidateMessage);?>"><br>

						<span class="liquid-width">Success message</span>
						<input class="input-width-static" type="text" name="subs-success-message" value="<?php echo esc_attr(@$sgSuccessMessage);?>"><br>

						<span class="liquid-width"><b>Input styles</b></span><br>
						<!--
							Text inputs options
						-->
						<span class="liquid-width">Width:</span>
						<input type="text" class="input-width-static" name="subs-text-width" value="<?php echo esc_attr(@$sgSubsTextWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Height:</span>
						<input type="text" class="input-width-static" name="subs-text-height" value="<?php echo esc_attr(@$sgSubsTextHeight); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Border width:</span>
						<input type="text" class="input-width-static" name="subs-text-border-width" data-subs-rel="js-subs-text-inputs" value="<?php echo esc_attr(@$sgSubsTextBorderWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example:"5px".</span><br>

						<span class="liquid-width">Background color:</span>
						<div class="color-picker"><input class="sg-subs-btn-color" id="sgOverlayColor" data-subs-rel="js-subs-text-inputs" type="text" name="subs-text-input-bgcolor" value="<?php echo esc_attr(@$sgSubsTextInputBgcolor); ?>"></div><br>

						<span class="liquid-width">Border color:</span>
						<div class="color-picker"><input class="sg-subs-btn-border-color" id="sgOverlayColor" data-subs-rel="js-subs-text-inputs" type="text" name="subs-text-bordercolor" value="<?php echo esc_attr(@$sgSubsTextBordercolor); ?>"></div><br>

						<span class="liquid-width">Text color:</span>
						<div class="color-picker"><input class="sg-subs-btn-text-color" id="sgOverlayColor" data-subs-rel="js-subs-text-inputs" type="text" name="subs-inputs-color" value="<?php echo esc_attr(@$sgSubsInputsColor); ?>"></div><br>

						<span class="liquid-width">Placeholder color:</span>
						<div class="color-picker"><input class="sg-subs-placeholder-color" id="sgOverlayColor" data-subs-rel="js-subs-text-inputs" type="text" name="subs-placeholder-color" value="<?php echo esc_attr(@$sgSubsPlaceholderColor); ?>"></div><br>

						<span class="liquid-width"><b>Sumbmit button styles</b></span><br>
						<!--
							Submit button options
						-->

						<span class="liquid-width">Width:</span>
						<input type="text" class="input-width-static" data-subs-rel="js-subs-submit-btn" name="subs-btn-width" value="<?php echo esc_attr(@$sgSubsBtnWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Height:</span>
						<input type="text" class="input-width-static" name="subs-btn-height" value="<?php echo esc_attr(@$sgSubsBtnHeight); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Title:</span>
						<input type="text" class="input-width-static" data-subs-rel="js-subs-submit-btn" name="subs-btn-title" value="<?php echo esc_attr(@$sgSubsBtnTitle); ?>"><br>

						<span class="liquid-width">Title (in progress):</span>
						<input type="text" class="input-width-static" data-subs-rel="js-subs-submit-btn" name="subs-btn-progress-title" value="<?php echo esc_attr(@$sgSubsBtnProgressTitle); ?>"><br>

						<span class="liquid-width">Background color:</span>
						<div class="color-picker"><input class="sg-subs-btn-color" id="sgOverlayColor" data-subs-rel="js-subs-submit-btn" type="text" name="subs-button-bgcolor" value="<?php echo esc_attr(@$sgSubsButtonBgcolor); ?>"></div><br>

						<span class="liquid-width">Text color:</span>
						<div class="color-picker"><input class="sg-subs-btn-text-color" id="sgOverlayColor" data-subs-rel="js-subs-submit-btn" type="text" name="subs-button-color" value="<?php echo esc_attr(@$sgSubsButtonColor); ?>"></div><br>
						<?php
							$subscriptionParams = array(
								"ajaxurl" => admin_url( 'admin-ajax.php'),
								"textInputsWidth" => $sgSubsTextWidth,
								"sgSubsBtnWidth" => $sgSubsBtnWidth,
								"sgSubsBtnHeight" => $sgSubsBtnHeight,
								"sgSubsTextHeight" => $sgSubsTextHeight,
								"textInputsBgColor" => $sgSubsTextInputBgcolor,
								"submitButtonBgColor" => $sgSubsButtonBgcolor,
								"sgSubsTextBordercolor" => $sgSubsTextBordercolor,
								"sgSubsInputsColor" => $sgSubsInputsColor,
								"subsButtonColor" => $sgSubsButtonColor,
								"sgSubsBtnTitle" => $sgSubsBtnTitle,
								"sgSubsBtnProgressTitle" => $sgSubsBtnProgressTitle,
								"sgSubstextBorderWidth" => $sgSubsTextBorderWidth
							);

							wp_enqueue_script('sg-subscription', SG_APP_POPUP_URL . '/javascript/sg_subscription.js', array( 'jquery' ));
							wp_localize_script('sg-subscription', 'SgSubscriptionParams', $subscriptionParams);
							wp_enqueue_script('jquery');
							echo "<script type=\"text/javascript\">
								jQuery(document).ready(function() {
									sgSubscriptionObj = new SgSubscription();
									sgSubscriptionObj.setupPlaceholderColor('js-subs-text-inputs', '$sgSubsPlaceholderColor');
									sgSubscriptionObj.init();
									sgSubscriptionObj.livePreview();
								});
							</script>";
							echo "<style type=\"text/css\">
							.js-subs-submit-btn,
							.js-subs-text-inputs {
								padding: 5px !important;
							}
							</style>";
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>