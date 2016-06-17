<?php get_header(); ?>

			<div id="content">
				
				<div id="inner-content" class="wrap cf single-content">

						<div id="main" class="m-all t-2of3 d-5of7 cf" role="main">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
								<?php if ( get_theme_mod('urbanbold_image_page') ) :
				                     	$image_class = '';

				                    else:
				                    	$image_class = 'image-hide';
				                   		
				                    endif;
				                ?>
				                
				                <div class="featured-image-wrapper <?php echo $image_class; ?>">
				                	<?php the_post_thumbnail('full'); ?>
				            	</div>
								<header class="article-header">
										<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
								</header> <?php // end article header ?>
								<section class="entry-content cf" itemprop="articleBody">
									<?php

										the_content();

										wp_link_pages( array(
											'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'urban-bold' ) . '</span>',
											'after'       => '</div>',
											'link_before' => '<span>',
											'link_after'  => '</span>',
										) );
									?>
								</section> <?php // end article section ?>

								

								<?php comments_template(); ?>

							</article>

							<?php endwhile; else : ?>

									<article id="post-not-found" class="hentry cf">
										<header class="article-header">
											<h1><?php _e( 'Oops, Post Not Found!', 'urban-bold' ); ?></h1>
											<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'urban-bold' ); ?></p>
										</header>
									</article>

							<?php endif; ?>

						</div>

						<?php get_sidebar('page'); ?>

				</div>

			</div>

<?php get_footer(); ?>
