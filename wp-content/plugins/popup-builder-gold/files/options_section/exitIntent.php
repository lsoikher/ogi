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
						<span><b>Mode</b></span><br>
						<?php echo createRadiobuttons($radiobuttons, "exit-intent-type", true, esc_html($sgExitIntentTpype), "liquid-width"); ?>
						<span class="liquid-width">Show Popup:</span>
						<?php echo sgCreateSelect($sgExitIntentSelectOptions, "exit-intent-expire-time", $sgExitIntntExpire)?>
						<span class='dashicons dashicons-info repositionImg sameImageStyle'></span>
							<span class='infoReposition samefontStyle'>
								The popup will show up after each X period of time.
						</span><br>
						<span class="liquid-width">Alert text:</span>
						<input  class="input-width-static" type="text" name="exit-intent-alert" value="<?php echo esc_attr(@$sgExitIntentAlert); ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>