<div class="crud-wrapper">
<div class="sg-settings-wrapper">
	<div id="special-options">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-2" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div class="postbox popup-builder-special-postbox">
						<div class="handlediv js-special-title" title="Click to toggle"><br></div>
						<h3 class="hndle ui-sortable-handle js-special-title">
							<span>Newsletter Settings</span>
						</h3>
						<div class="special-options-content">
							<div class="sgpb-alert sgpb-newsletter-notice sgpb-alert-info fade in sg-hide-element">
								<span>You will receive a notification email when everything is done!</span>
							</div>
							<span class="liquid-width">Choose The Forms</span><br>
							<?php 
								$newsletterForms = SgPopupGetData::getAllSubscriptionForms();
								echo SGFunctions::createSelectBox($newsletterForms, '', array('class'=>'js-sg-newslatter-forms'));
							?>
							<a href="<?php echo admin_url();?>admin-post.php?action=subs_error_csv"><input type="button" value="Get error list" class="button sg-subs-error-list sg-hide-element" data-subs-list=""></a><br>
							<span class="liquid-width">Emails Is One Flow in per <?php echo SG_FILTER_REPEAT_INTERVAL;?> minute</span><br>
							<input type="number" class="sg-emails-in-flow" value="50"><br>
							<span class="liquid-width">Emails Subject</span><br>
							<input type="text" value="Your subject here" class="sg-newsletter-subject"><br>
							<span class="liquid-width">Type your text here</span><br>
							<?php

								$editorId = 'sg_newsletter_text';
								$content = "<p>Hi [First name] [Last name],</p>
									<p>Super excited to have you on board, we know you’ll just love us.</p>
									<p>Sincerely,</p>
									<p>[Blog name]</p>";
								$settings = array(
									'wpautop' => false,
									'tinymce' => array(
										'width' => '100%',
									),
									'textarea_rows' => '6',
									'media_buttons' => false
								);
								wp_editor($content, $editorId, $settings);

							?>
							<div class="sg-newslatter-submit-wrapper">
								<input type="submit" class="button-primary sg-newsletter-sumbit" value="Send">
								<img src="<?php echo plugins_url('img/wpAjax.gif', dirname(__FILE__).'../../../'); ?>" alt="gif" class="spiner-allPages js-sg-spinner sg-hide-element js-sg-send-subsribe">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="sg-newsletter-description">
<div id="special-options ">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-2" class="">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div class="postbox popup-builder-special-postbox">
						<div class="handlediv js-special-title" title="Click to toggle"><br></div>
						<h3 class="hndle ui-sortable-handle js-special-title">
							<span>Newsletter Info</span>
						</h3>
						<div class="special-options-content">
							<span class="liquid-width">[First name]</span>Subsriber First name<br>
							<span class="liquid-width">[Last name]</span>Subscriber last name<br>
							<span class="liquid-width">[Blog name]</span>Sender name<br>
							<span class="liquid-width">[User name]</span>Sender name
 						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
