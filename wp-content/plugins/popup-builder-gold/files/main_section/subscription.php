<div class="sg-wp-editor-container">
<?php
	$content = @$sgSunbscriptionContent;
	$editorId = 'sg_subscription';
	$settings = array(
		'wpautop' => false,
		'tinymce' => array(
			'width' => '100%',
		),
		'textarea_rows' => '6',
		'media_buttons' => true
	);
	wp_editor($content, $editorId, $settings);
?>
</div>