<div class="sg-wp-editor-container">
<?php
	$content = @$sgSocialContent;
	$editor_id = 'sg_social';
	$settings = array(
		'wpautop' => false,
		'tinymce' => array(
			'width' => '100%',
		),
		'textarea_rows' => '6',
		'media_buttons' => true
	);
	wp_editor($content, $editor_id, $settings);
?>
</div>