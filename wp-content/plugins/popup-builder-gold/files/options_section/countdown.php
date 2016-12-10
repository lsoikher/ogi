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

						<div class="sg-countdown-wrapper" id="sg-clear-coundown">
							<div class="sg-counts-content"></div>
						</div>
						<?php if(empty($sgGetCountdownType)) $sgGetCountdownType = 1; ?>

						<span class="liquid-width">Counter background color:</span>
						<div class="color-picker"><input class="sgOverlayColor" type="text" name="countdownNumbersBgColor" value="<?php echo esc_attr(@$sgCountdownNumbersBgColor); ?>" /></div><br>

						<span class="liquid-width">Counter text color:</span>
						<div class="color-picker"><input class="sgOverlayColor" type="text" name="countdownNumbersTextColor" value="<?php echo esc_attr(@$sgCountdownNumbersTextColor); ?>" /></div><br>

						<span class="liquid-width">Due date:</span>
						<input id="sg-datapicker" type="text" name="sg-due-date" value="<?php echo esc_attr(@$sgDueDate)?>"><br>

						<span class="liquid-width">Countdown format:</span>
						<?php echo sgCreateSelect($sgCountdownType,'sg-countdown-type',@$sgGetCountdownType)?>

						<span class="liquid-width">Time zone:</span>
						<?php echo sgCreateSelect($sgTimeZones,'sg-time-zone',@$sgSelectedTimeZone)?>

						<span class="liquid-width">Select language:</span>
						<?php echo sgCreateSelect($sgCountdownlang,'counts-language',@$sgCountdownLang)?>

						<span class="liquid-width">Show counter on the Top:</span>
						<input type="checkbox"  class="pushToBottom" name="pushToBottom" <?php echo $sgPushToBottom;?>><br>

						<span class="liquid-width">Time out close popup:</span>
						<input type="checkbox"  class="pushToBottom" name="countdown-autoclose" <?php echo $sgCountdownAutoclose;?>>
						<?php
							$countdownCustomParams['countdownAutoclose'] = $sgCountdownAutoclose;
							$countdownParams = SGCountdownPopup::renderScript(@$seconds, $sgGetCountdownType, '', '$sgCountdownLang',$countdownCustomParams);
							echo SGCountdownPopup::renderStyle($sgCountdownNumbersBgColor, $sgCountdownNumbersTextColor);
							echo "<script type=\"text/javascript\">
								jQuery(document).ready(function() {
									var objCountdown = new SGCountdown();
									objCountdown.adminInit();
									
								});

							</script>";
							wp_register_script('sg_coundtdown_js', SG_APP_POPUP_URL . '/javascript/sg_countdown.js');
							wp_localize_script('sg_coundtdown_js', 'SgCountdownParams', $countdownParams);
							wp_enqueue_script('sg_coundtdown_js');
						?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>