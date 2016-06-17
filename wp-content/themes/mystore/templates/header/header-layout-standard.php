<?php
/**
 * @package myStore
 */
global $woocommerce; ?>

<header id="masthead" class="site-header <?php echo ( get_theme_mod( 'mystore-header-layout' ) == 'mystore-header-layout-standard' ) ? sanitize_html_class( 'mystore-header-layout-standard' ) : sanitize_html_class( 'mystore-header-layout-centered' ); ?>">
	
	<?php if ( get_theme_mod( 'mystore-header-remove-topbar' ) ) : ?>
	<div class="site-header-topbar">
		<div class="site-container">
			
			<div class="site-topbar-left">
				<?php wp_nav_menu( array( 'theme_location' => 'top-bar-menu', 'fallback_cb' => false, 'depth'  => 1 ) ); ?>
			</div>
			
			<div class="site-topbar-right"></div>
			
			<div class="clearboth"></div>
		</div>
	</div>
	<?php endif; ?>
	
	<div class="site-container">
		
		<?php if( get_theme_mod( 'mystore-header-search', false ) ) : ?>
		    <div class="search-block">
		        <?php get_search_form(); ?>
		    </div>
		<?php endif; ?>
		
		<div class="site-branding">
			
			<?php if( get_header_image() ) : ?>
		        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><img src="<?php esc_url( header_image() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ) ?>" /></a>
		    <?php else : ?>
		        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		        <h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
		    <?php endif; ?>
			
		</div><!-- .site-branding -->
		
		<?php if( get_theme_mod( 'mystore-header-search', false ) ) : ?>
			<div class="menu-search">
		    	<i class="fa fa-search search-btn"></i>
		    </div>
		<?php endif; ?>
		
		<?php if ( mystore_is_woocommerce_activated() ) : ?>
			<div class="header-cart">
				
                <a class="header-cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'mystore'); ?>">
                    <span class="header-cart-amount">
                        <?php echo sprintf( _n( '(%d)', '(%d)', $woocommerce->cart->cart_contents_count, 'mystore' ), $woocommerce->cart->cart_contents_count); ?> - <?php echo $woocommerce->cart->get_cart_total(); ?>
                    </span>
                    <span class="header-cart-checkout<?php echo ( $woocommerce->cart->cart_contents_count > 0 ) ? ' cart-has-items' : ''; ?>">
                        <i class="fa fa-shopping-cart"></i>
                    </span>
                </a>
				
			</div>
		<?php endif; ?>

		<nav id="site-navigation" class="main-navigation" role="navigation">
			<span class="header-menu-button"><i class="fa fa-bars"></i><span><?php echo esc_attr( get_theme_mod( 'mystore-header-menu-text', 'menu' ) ); ?></span></span>
			<div id="main-menu" class="main-menu-container">
				<div class="main-menu-close"><i class="fa fa-angle-right"></i><i class="fa fa-angle-left"></i></div>
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
			</div>
		</nav><!-- #site-navigation -->
		
		<div class="clearboth"></div>
	</div>
		
</header><!-- #masthead -->