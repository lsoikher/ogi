<?php
	define('SG_SOCIAL_LABEL_MAX_SIZE', 15);
?>
<div id="special-options">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="postbox-container-2" class="postbox-container">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox popup-builder-special-postbox sgSameWidthPostBox">
					<div class="handlediv js-special-title" title="Click to toggle"><br></div>
					<h3 class="hndle ui-sortable-handle js-special-title">
					<span><?php
							global $POPUP_TITLES;
							$popupTypeTitle = $POPUP_TITLES[$popupType];
							echo $popupTypeTitle;?> options
					</span>
					</h3>
					<div class="special-options-content">
						<?php
							if (empty($shareUrlType)) {
								$shareUrlType = 'activeUrl';
							}
							echo sgCreateRadioElements($radioElements,$shareUrlType);
						?>
						<span class="liquid-width"><b>Configuration of the buttons</b></span><br>
						<span class="liquid-width">Theme:</span>
						<?php if(@$sgSocialTheme == ''): ?>
							<?php $sgSocialTheme = 'classic'; ?>
						<?php endif; ?>
						<?php echo sgCreateSelect(@$sgTheme,'sgSocialTheme',esc_html(@$sgSocialTheme)); ?>
						<span class="liquid-width">Font size:</span>
						<?php echo sgCreateSelect($sgThemeSize,'sgSocialButtonsSize',esc_html(@$sgSocialButtonsSize)); ?>
						<span class="liquid-width">Show labels:</span>
						<input type="checkbox" name="sgSocialLabel"  class="socialTrigger"<?php echo $sgSocialLabel;?>><br>
						<span class="liquid-width">Show share count:</span>
						<?php echo sgCreateSelect($sgSocialCount,'sgSocialShareCount',esc_html(@$sgSocialShareCount)); ?>
						<span class="liquid-width">Use round buttons:</span>
						<input type="checkbox"  class="socialTrigger" name="sgRoundButton" <?php echo $sgRoundButtons;?>><br>
						<span class="liquid-width">Push to bottom:</span>
						<input type="checkbox"  class="pushToBottom" name="pushToBottom" <?php echo $sgPushToBottom;?>>
						<div id="share-btns-container"></div>

						<span class="liquid-width"><b>Share Buttons</b></span><br>

						<span  class="liquid-width"><b>E-mail</b></span>
						<input id="js-email-checkbox" class="js-social-btn-status js-checkbox-acordion" data-social-button="email" type="checkbox" name="sgEmailStatus" <?php echo $sgEmailStatus;?>>
						<div class="socials-content js-email-options-content">
						<span  class="liquid-width">Label:</span>
						<input  class="input-width-static js-social-btn-text" data-social-button="email" type="text" name="sgMailLable" placeholder="E-mail" maxlength="<?php echo SG_SOCIAL_LABEL_MAX_SIZE;?>" value="<?php echo esc_attr(@$sgMailLable); ?>"/><br>
						</div><br>

						<span  class="liquid-width"><b>Facebook</b></span>
						<input id="js-fb-checkbox" class="js-social-btn-status js-checkbox-acordion" data-social-button="facebook" type="checkbox" name="sgFbStatus" <?php echo $sgFbStatus;?>>
						<div class="socials-content js-fb-options-content">
						<span  class="liquid-width">Label:</span>
						<input  class="input-width-static js-social-btn-text" data-social-button="facebook" type="text" name="fbShareLabel"  placeholder="Like" maxlength="<?php echo SG_SOCIAL_LABEL_MAX_SIZE;?>" value="<?php echo esc_attr(@$fbShareLabel); ?>"/><br>
						</div><br>

						<span  class="liquid-width"><b>LinkedIn</b></span>
						<input type="checkbox" id="js-linkedin-checkbox" class="js-social-btn-status js-checkbox-acordion" data-social-button="linkedin" name="sgLinkedinStatus" <?php echo $sgLinkedinStatus;?>>
						<div class="socials-content js-linkedin-options-content">
						<span  class="liquid-width">Label:</span>
						<input  class="input-width-static js-social-btn-text" data-social-button="linkedin" type="text" name="lindkinLabel" placeholder="Share" maxlength="<?php echo SG_SOCIAL_LABEL_MAX_SIZE;?>" value="<?php echo esc_attr(@$lindkinLabel); ?>"/><br>
						</div></br>

						<span  class="liquid-width"><b>Google+</b></span>
						<input type="checkbox" id="js-google-checkbox" class="js-social-btn-status js-checkbox-acordion" data-social-button="googleplus" name="sgGoogleStatus" <?php echo $sgGoogleStatus;?>>
						<div class="socials-content js-google-options-content">
						<span  class="liquid-width">Label:</span>
						<input  class="input-width-static js-social-btn-text" data-social-button="googleplus" type="text" name="googLelabel" placeholder="+1" maxlength="<?php echo SG_SOCIAL_LABEL_MAX_SIZE;?>" value="<?php echo esc_attr(@$googLelabel); ?>"/><br>
						</div><br>

						<span  class="liquid-width"><b>Twitter</b></span>
						<input id="js-twitter-checkbox" type="checkbox" class="js-social-btn-status js-checkbox-acordion" data-social-button="twitter" name="sgTwitterStatus" <?php echo $sgTwitterStatus;?>>
						<div class="socials-content js-twitter-options-content">
						<span  class="liquid-width">Label:</span>
						<input  class="input-width-static js-social-btn-text" data-social-button="twitter" type="text" name="twitterLabel" placeholder="Tweet" maxlength="<?php echo SG_SOCIAL_LABEL_MAX_SIZE;?>" value="<?php echo esc_attr(@$twitterLabel); ?>"/><br>
						</div><br>

						<span  class="liquid-width"><b>Pinterest</b></span>
						<input type="checkbox" id="js-pinterest-checkbox" class="js-social-btn-status" data-social-button="pinterest" name="sgPinterestStatus" <?php echo $sgPinterestStatus;?>>
						<div class="socials-content js-pinterest-options-content">
						<span  class="liquid-width">Label:</span>
						<input  class="input-width-static js-social-btn-text" data-social-button="pinterest" type="text" name="pinterestLabel" placeholder="Pin it" maxlength="<?php echo SG_SOCIAL_LABEL_MAX_SIZE;?>" value="<?php echo esc_attr(@$pinterestLabel); ?>"/><br>
						</div><br>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>