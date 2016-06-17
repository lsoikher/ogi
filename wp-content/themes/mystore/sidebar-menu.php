<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package myStore
 */
?>
<div id="main-menu" class="main-menu-container <?php echo sanitize_html_class( get_theme_mod( 'mystore-header-layout' ) ); ?>">

	<div class="main-menu-close"><i class="fa fa-angle-right"></i><i class="fa fa-angle-left"></i></div>

	<nav id="site-navigation" class="main-navigation" role="navigation">
		
		<?php if ( get_theme_mod( 'mystore-header-layout' ) == 'mystore-header-layout-standard' ) : ?>
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Menu', 'mystore' ); ?></button>
		<?php endif; ?>
		
		<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
	</nav><!-- #site-navigation -->

</div>