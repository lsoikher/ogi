<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package myStore
 */
global $woocommerce;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php if ( ! get_theme_mod( 'mystore-site-remove-border' ) ) : ?>
	<div class="site-border-top"></div><div class="site-border-bottom"></div><div class="site-border-left"></div><div class="site-border-right"></div>
<?php endif; ?>

<div id="page" class="hfeed site <?php echo ( get_theme_mod( 'mystore-page-banner-enable' ) ) ? sanitize_html_class( 'has-page-banner' ) : ''; ?> <?php echo sanitize_html_class( get_theme_mod( 'mystore-slider-type' ) ); ?> <?php echo sanitize_html_class( get_theme_mod( 'mystore-header-layout' ) ); ?>">
	
	<?php if ( get_theme_mod( 'mystore-header-layout' ) == 'mystore-header-layout-standard' ) : ?>
	
		<?php get_template_part( '/templates/header/header-layout-standard' ); ?>
		
	<?php else : ?>
	
		<?php get_template_part( '/templates/header/header-layout-centered' ); ?>
		
	<?php endif; ?>
	
	<?php if ( is_front_page() ) : ?>
		
		<?php get_template_part( '/templates/slider/homepage-slider' ); ?>
		
	<?php else : ?>
		
		<?php if ( get_theme_mod( 'mystore-page-banner-enable' ) ) : ?>
		
			<?php get_template_part( '/templates/page-banner' ); ?>
		
		<?php endif; ?>
		
	<?php endif; ?>

	<div class="site-container<?php echo ( ! is_active_sidebar( 'sidebar-1' ) ) ? ' content-no-sidebar' : ' content-has-sidebar'; ?>">
