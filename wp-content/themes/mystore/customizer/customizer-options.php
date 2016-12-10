<?php
/**
 * Defines customizer options
 *
 * @package Customizer Library myStore
 */

function customizer_library_mystore_options() {

	// Theme defaults
	$header_bg_color = '#f9f9f9';
	
	$primary_color = '#29a6e5';
	$secondary_color = '#2886e5';
	
	$body_font_color = '#404040';
	$heading_font_color = '#5E5E5E';
	
	$site_border_color = '#212121';

	// Stores all the controls that will be added
	$options = array();

	// Stores all the sections to be added
	$sections = array();

	// Stores all the panels to be added
	$panels = array();

	// Adds the sections to the $options array
	$options['sections'] = $sections;

	// Favicon
	$section = 'mystore-favicon-section';
	
	$sections[] = array(
		'id' => $section,
		'title' => __( 'Favicon', 'mystore' ),
		'priority' => '30',
		'description' => __( '', 'mystore' )
	);

	$options['mystore-favicon'] = array(
		'id' => 'mystore-favicon',
		'label'   => __( 'Uplod a favicon', 'mystore' ),
		'section' => $section,
		'type'    => 'image',
		'description' => __( 'Favicon size is 16 X 16 pixels.', 'mystore' ),
		'default' => ''
	);

	
	// Site Layout Options
	$section = 'mystore-site-layout-section';

	$sections[] = array(
		'id' => $section,
		'title' => __( 'Layout Options', 'mystore' ),
		'priority' => '30',
		'description' => __( '', 'mystore' )
	);
	
    // Upsell Button One
    $options['mystore-setting-upsell-one'] = array(
        'id' => 'mystore-setting-upsell-one',
        'label'   => __( 'Page Styling - Flat / Blocks', 'mystore' ),
        'section' => $section,
        'type'    => 'upsell',
    );
	
	$options['mystore-site-remove-border'] = array(
		'id' => 'mystore-site-remove-border',
		'label'   => __( 'Remove the Site Border', 'mystore' ),
		'section' => $section,
		'type'    => 'checkbox',
		'default' => 0,
	);
	
	// Upsell Button Two
    $options['mystore-setting-upsell-two'] = array(
        'id' => 'mystore-setting-upsell-two',
        'label'   => __( 'Extra Layout Settings', 'mystore' ),
        'section' => $section,
        'type'    => 'upsell',
    );
	
	
	// Header Layout Options
	$section = 'mystore-header-section';

	$sections[] = array(
		'id' => $section,
		'title' => __( 'Header Options', 'mystore' ),
		'priority' => '30',
		'description' => __( '', 'mystore' )
	);

	$choices = array(
		'mystore-header-layout-centered' => __( 'Centered Layout Style', 'mystore' ),
		'mystore-header-layout-standard' => __( 'Standard Layout Style', 'mystore' )
	);
	$options['mystore-header-layout'] = array(
		'id' => 'mystore-header-layout',
		'label'   => __( 'Header Layout', 'mystore' ),
		'section' => $section,
		'type'    => 'select',
		'choices' => $choices,
		'default' => 'mystore-header-layout-centered'
	);
	
	$options['mystore-header-bg-color'] = array(
		'id' => 'mystore-header-bg-color',
		'label'   => __( 'Header Background Color', 'mystore' ),
		'section' => $section,
		'type'    => 'color',
		'default' => $header_bg_color,
	);
	
	$options['mystore-header-remove-topbar'] = array(
		'id' => 'mystore-header-remove-topbar',
		'label'   => __( 'Show the Top Bar', 'mystore' ),
		'section' => $section,
		'type'    => 'checkbox',
		'default' => 0,
	);
	
	$options['mystore-header-menu-text'] = array(
		'id' => 'mystore-header-menu-text',
		'label'   => __( 'Menu Button Text', 'mystore' ),
		'section' => $section,
		'type'    => 'text',
		'default' => 'menu',
		'description' => __( 'This is the text for the menu button', 'mystore' )
	);
	
	$options['mystore-header-search'] = array(
        'id' => 'mystore-header-search',
        'label'   => __( 'Show Search', 'mystore' ),
        'section' => $section,
        'type'    => 'checkbox',
        'description' => __( 'Enable a search box on your site', 'mystore' ),
        'default' => 0,
    );
    
    
    // Page Banner Settings
    $section = 'mystore-page-banner-section';

    $sections[] = array(
        'id' => $section,
        'title' => __( 'Page Banner Options', 'mystore' ),
        'priority' => '35'
    );
    
    $options['mystore-page-banner-enable'] = array(
        'id' => 'mystore-page-banner-enable',
        'label'   => __( 'Enable Page Banner', 'mystore' ),
        'section' => $section,
        'type'    => 'checkbox',
        'description' => __( 'Select this to add the Page Banner to all the pages', 'mystore' ),
        'default' => 0,
    );
    
    $options['mystore-page-banner-bg-color'] = array(
        'id' => 'mystore-page-banner-bg-color',
        'label'   => __( 'Background Color', 'mystore' ),
        'section' => $section,
        'type'    => 'color',
        'default' => $header_bg_color,
    );
    
    $options['mystore-default-page-banner'] = array(
        'id' => 'mystore-default-page-banner',
        'label'   => __( 'Default Page Banner', 'mystore' ),
        'section' => $section,
        'type'    => 'image',
        'description' => __( 'Upload a default background image for the Page Banners', 'mystore' )
    );
    $choices = array(
        'mystore-banner-size-extra-small' => __( 'Extra Small Page Banner', 'mystore' ),
        'mystore-banner-size-small' => __( 'Small Page Banner', 'mystore' ),
        'mystore-banner-size-medium' => __( 'Medium Page Banner', 'mystore' ),
        'mystore-banner-size-large' => __( 'Large Page Banner', 'mystore' )
    );
    $options['mystore-banner-size'] = array(
        'id' => 'mystore-banner-size',
        'label'   => __( 'Page Banner Size', 'mystore' ),
        'section' => $section,
        'type'    => 'select',
        'choices' => $choices,
        'default' => 'mystore-banner-size-extra-small'
    );
    
    
    // Slider Settings
    $section = 'mystore-slider-section';

    $sections[] = array(
        'id' => $section,
        'title' => __( 'Slider Options', 'mystore' ),
        'priority' => '35'
    );
    
    $choices = array(
        'mystore-slider-default' => __( 'Default Slider', 'mystore' ),
        'mystore-meta-slider' => __( 'Meta Slider', 'mystore' ),
        'mystore-no-slider' => __( 'None', 'mystore' )
    );
    $options['mystore-slider-type'] = array(
        'id' => 'mystore-slider-type',
        'label'   => __( 'Choose a Slider', 'mystore' ),
        'section' => $section,
        'type'    => 'select',
        'choices' => $choices,
        'default' => 'mystore-slider-default'
    );
    $options['mystore-slider-cats'] = array(
        'id' => 'mystore-slider-cats',
        'label'   => __( 'Slider Categories', 'mystore' ),
        'section' => $section,
        'type'    => 'text',
        'description' => __( 'Enter the ID\'s of the post categories you want to display in the slider. Eg: "13,17,19" (no spaces and only comma\'s)<br /><a href="https://kairaweb.com/documentation/setting-up-the-default-slider/" target="_blank"><b>Follow instructions here</b></a>', 'mystore' )
    );
    $options['mystore-meta-slider-shortcode'] = array(
        'id' => 'mystore-meta-slider-shortcode',
        'label'   => __( 'Slider Shortcode', 'mystore' ),
        'section' => $section,
        'type'    => 'text',
        'description' => __( 'Enter the shortcode give by meta slider.', 'mystore' )
    );
    
    // Upsell Button Three
    $options['mystore-setting-upsell-three'] = array(
        'id' => 'mystore-setting-upsell-three',
        'label'   => __( 'Extra Slider Settings', 'mystore' ),
        'section' => $section,
        'type'    => 'upsell',
    );


	// Colors
	$section = 'colors';

	$sections[] = array(
		'id' => $section,
		'title' => __( 'Colors', 'mystore' ),
		'priority' => '80'
	);

	$options['mystore-primary-color'] = array(
		'id' => 'mystore-primary-color',
		'label'   => __( 'Primary Color', 'mystore' ),
		'section' => $section,
		'type'    => 'color',
		'default' => $primary_color,
	);

	$options['mystore-secondary-color'] = array(
		'id' => 'mystore-secondary-color',
		'label'   => __( 'Secondary Color', 'mystore' ),
		'section' => $section,
		'type'    => 'color',
		'default' => $secondary_color,
	);

	$options['mystore-site-border'] = array(
		'id' => 'mystore-site-border',
		'label'   => __( 'Site Border Color', 'mystore' ),
		'section' => $section,
		'type'    => 'color',
		'default' => $site_border_color,
	);

	// Font Options
	$section = 'mystore-typography-section';
	$font_choices = customizer_library_get_font_choices();

	$sections[] = array(
		'id' => $section,
		'title' => __( 'Font Options', 'mystore' ),
		'priority' => '80'
	);

	$options['mystore-body-font'] = array(
		'id' => 'mystore-body-font',
		'label'   => __( 'Body Font', 'mystore' ),
		'section' => $section,
		'type'    => 'select',
		'choices' => $font_choices,
		'default' => 'Open Sans'
	);
	$options['mystore-body-font-color'] = array(
		'id' => 'mystore-body-font-color',
		'label'   => __( 'Body Font Color', 'mystore' ),
		'section' => $section,
		'type'    => 'color',
		'default' => $body_font_color,
	);

	$options['mystore-heading-font'] = array(
		'id' => 'mystore-heading-font',
		'label'   => __( 'Heading Font', 'mystore' ),
		'section' => $section,
		'type'    => 'select',
		'choices' => $font_choices,
		'default' => 'Droid Serif'
	);
	$options['mystore-heading-font-color'] = array(
		'id' => 'mystore-heading-font-color',
		'label'   => __( 'Heading Font Color', 'mystore' ),
		'section' => $section,
		'type'    => 'color',
		'default' => $heading_font_color,
	);
	
	$options['mystore-site-title-font'] = array(
        'id' => 'mystore-site-title-font',
        'label'   => __( 'Site Title Font', 'mystore' ),
        'section' => $section,
        'type'    => 'select',
        'choices' => $font_choices,
        'default' => 'Droid Serif'
    );
    $options['mystore-site-title-font-color'] = array(
        'id' => 'mystore-site-title-font-color',
        'label'   => __( 'Site Title Font Color', 'mystore' ),
        'section' => $section,
        'type'    => 'color',
        'default' => $primary_color,
    );
    $options['mystore-site-desc-font-color'] = array(
        'id' => 'mystore-site-desc-font-color',
        'label'   => __( 'Site Description Font Color', 'mystore' ),
        'section' => $section,
        'type'    => 'color',
        'default' => '#FFFFFF',
    );
	
	
	// Blog Settings
    $section = 'mystore-blog-section';

    $sections[] = array(
        'id' => $section,
        'title' => __( 'Blog Options', 'mystore' ),
        'priority' => '50'
    );
    
    // Upsell Button Four
    $options['mystore-setting-upsell-four'] = array(
        'id' => 'mystore-setting-upsell-four',
        'label'   => __( 'Blog Layout & full width option', 'mystore' ),
        'section' => $section,
        'type'    => 'upsell',
    );
    
    $options['mystore-blog-title'] = array(
        'id' => 'mystore-blog-title',
        'label'   => __( 'Blog Page Title', 'mystore' ),
        'section' => $section,
        'type'    => 'text',
        'default' => 'Blog'
    );
    $options['mystore-blog-cats'] = array(
        'id' => 'mystore-blog-cats',
        'label'   => __( 'Exclude Blog Categories', 'mystore' ),
        'section' => $section,
        'type'    => 'text',
        'description' => __( 'Enter the ID\'s of the post categories you\'d like to EXCLUDE from the Blog, enter only the ID\'s with a minus sign (-) before them, separated by a comma (,)<br />Eg: "-13, -17, -19"<br />If you enter the ID\'s without the minus then it\'ll show ONLY posts in those categories.', 'mystore' )
    );
	
	
	// Footer Settings
    $section = 'mystore-footer-section';

    $sections[] = array(
        'id' => $section,
        'title' => __( 'Footer Layout Options', 'mystore' ),
        'priority' => '85'
    );
    
    // Upsell Button Five
    $options['mystore-setting-upsell-five'] = array(
        'id' => 'mystore-setting-upsell-five',
        'label'   => __( 'Different Footer Layouts & remove bottom bar', 'mystore' ),
        'section' => $section,
        'type'    => 'upsell',
    );
	
	// Site Text Settings
    $section = 'mystore-website-section';

    $sections[] = array(
        'id' => $section,
        'title' => __( 'Website Text', 'mystore' ),
        'priority' => '50'
    );
    
    // Upsell Button Six
    $options['mystore-setting-upsell-six'] = array(
        'id' => 'mystore-setting-upsell-six',
        'label'   => __( 'Change CopyRight Text', 'mystore' ),
        'section' => $section,
        'type'    => 'upsell',
    );
    
    $options['mystore-website-error-head'] = array(
        'id' => 'mystore-website-error-head',
        'label'   => __( '404 Error Page Heading', 'mystore' ),
        'section' => $section,
        'type'    => 'text',
        'default' => __( 'Oops! <span>404</span>', 'mystore'),
        'description' => __( 'Enter the heading for the 404 Error page', 'mystore' )
    );
    $options['mystore-website-error-msg'] = array(
        'id' => 'mystore-website-error-msg',
        'label'   => __( 'Error 404 Message', 'mystore' ),
        'section' => $section,
        'type'    => 'textarea',
        'default' => __( 'It looks like that page does not exist. <br />Return home or try a search', 'mystore'),
        'description' => __( 'Enter the default text on the 404 error page (Page not found)', 'mystore' )
    );
    $options['mystore-website-nosearch-msg'] = array(
        'id' => 'mystore-website-nosearch-msg',
        'label'   => __( 'No Search Results', 'mystore' ),
        'section' => $section,
        'type'    => 'textarea',
        'default' => __( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'mystore'),
        'description' => __( 'Enter the default text for when no search results are found', 'mystore' )
    );
	
	
	// Social Settings
    $section = 'mystore-social-section';

    $sections[] = array(
        'id' => $section,
        'title' => __( 'Social Links', 'mystore' ),
        'priority' => '80'
    );
    
    // Upsell Button Seven
    $options['mystore-setting-upsell-seven'] = array(
        'id' => 'mystore-setting-upsell-seven',
        'label'   => __( 'Add Social Links', 'mystore' ),
        'section' => $section,
        'type'    => 'upsell',
    );
	

	// Adds the sections to the $options array
	$options['sections'] = $sections;

	// Adds the panels to the $options array
	$options['panels'] = $panels;

	$customizer_library = Customizer_Library::Instance();
	$customizer_library->add_options( $options );

	// To delete custom mods use: customizer_library_remove_theme_mods();

}
add_action( 'init', 'customizer_library_mystore_options' );
