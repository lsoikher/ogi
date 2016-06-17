<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<meta charset="utf-8">

		<?php // force Internet Explorer to use the latest rendering engine available ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<?php // mobile meta (hooray!) ?>
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

		<?php if ( get_theme_mod( 'urbanbold_favicon' ) ) : ?>
		<link rel="icon" href="<?php echo esc_url( get_theme_mod( 'urbanbold_favicon' ) ); ?>">
		<?php endif; ?>

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<?php // wordpress head functions ?>
		<?php wp_head(); ?>
		<?php // end of wordpress head ?>

	</head>

	<body <?php body_class(); ?>>

		<div id="container">

			<header class="header" role="banner" style="background:url(<?php header_image(); ?>)no-repeat;background-size:cover;">
				<div id="inner-header" class="wrap cf header-inner">

					<?php if ( get_theme_mod( 'urbanbold_logo' ) ) : ?>
						<p id="logo" class="h1">
							<a href="<?php echo home_url(); ?>" rel="nofollow">
								<img src="<?php echo esc_url( get_theme_mod( 'urbanbold_logo' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" />
							</a>
						</p>

					<?php else : ?>
						<p id="logo" class="h1"><a href="<?php echo home_url(); ?>" rel="nofollow"><?php bloginfo('name'); ?></a></p>
					<?php endif; ?>

					<div id="responsive-nav">
            			<img src="<?php echo urbanbold_IMAGES; ?>/nav.png" alt="nav">
       				</div>
   					 
   					 <div class="clear no-display"></div>
   					 <nav role="navigation" id="main-navigation">
   					 	<?php if ( has_nav_menu('main-nav') ) { ?>
							<?php wp_nav_menu(array(
	    					'container' => false,                           // remove nav container
	    					'container_class' => 'menu cf',                 // class of container (should you choose to use it)
	    					'menu' => __( 'The Main Menu', 'urban-bold' ),  // nav name
	    					'menu_class' => 'nav top-nav cf',               // adding custom nav class
	    					'theme_location' => 'main-nav',                 // where it's located in the theme
	    					'before' => '',                                 // before the menu
		        			'after' => '',                                  // after the menu
		        			'link_before' => '',                            // before each link
		        			'link_after' => '',                             // after each link
		        			'depth' => 0,                                   // limit the depth of the nav
	    					'fallback_cb' => ''                             // fallback function (if there is one)
							)); ?>
						<?php } else { ?>
							<ul class="nav top-nav cf">
								<?php wp_list_pages('sort_column=menu_order&title_li='); ?>
							</ul>
						<?php } ?>
					</nav>

				</div>

			</header>