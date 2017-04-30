<?php

/**
 * Include a template
 * @param string $template
 * @param array $vars
 */
function wc_autoship_include_template( $template, $vars = array() ) {
	// Default vars
	$prefix = 'wc_autoship_';
	// Custom vars
	extract( $vars );
	// Find theme template
	$theme_template = apply_filters( 'wc_autoship_theme_template',
		get_stylesheet_directory() . '/woocommerce-autoship/templates/' . $template . '.php',
		$template,
		$vars
	);
	if ( file_exists( $theme_template ) ) {
		// Include theme template
		include ( $theme_template );
	} else {
		// Include plugin template
		$plugin_template = apply_filters( 'wc_autoship_plugin_template',
			WC_AUTOSHIP_PLUGIN_DIR . '/templates/' . $template . '.php',
			$template,
			$vars
		);
		include( $plugin_template );
	}
}

/**
 * Render a template.
 * @param string $template
 * @param array $vars
 * @return string
 */
function wc_autoship_render_template( $template, $vars = array() ) {
	ob_start();
	wc_autoship_include_template( $template, $vars );
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}