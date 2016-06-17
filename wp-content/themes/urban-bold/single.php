<?php get_header(); ?>

			<div id="content">
				
				<div id="inner-content" class="wrap cf single-content">
					<?php if ( get_theme_mod('urbanbold_full_width') ) :
						$full_class = 'full';
						else:
						
						$full_class = '';
						endif;
					?>
					<div id="main" class="m-all t-2of3 d-5of7 cf <?php echo $full_class; ?>" role="main">
						
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
				                <?php if ( get_theme_mod('urbanbold_image_post') ) :
				                     	$image_class = '';
				                    else:
				                    	$image_class = 'image-hide';
				                   		
				                    endif;
				                ?>

				                <div class="featured-image-wrapper <?php echo $image_class; ?>">
				                	<?php the_post_thumbnail('full'); ?>
				            	</div>

				                <header class="article-header">
									<h1 class="entry-title single-title" itemprop="headline"><?php the_title(); ?></h1>
									<p class="byline vcard">
					                    <?php printf( __( 'Posted <span>%2$s</span> by <span class="author">%3$s</span>', 'urban-bold' ), get_the_time('Y-m-j'), get_the_time(get_option('date_format')), get_the_author_link( get_the_author_meta( 'ID' ) )); ?>
					                    <?php
					                      /* translators: used between list items, there is a space after the comma */
					                      $category_list = get_the_category_list( __( ', ', 'urban-bold' ) );
					                      printf( __('under %s', 'urban-bold'),
					                        $category_list
					                      );
					                    ?>
					                </p>
								</header> <?php // end article header ?>
				                
								<?php
									
									get_template_part( 'post-formats/format', get_post_format() );
								?>

								<div class="next-prev-post">
				                  <div class="prev">
				                    <?php previous_post_link('<p><span class="fa fa-angle-left"></span> PREVIOUS POST</p> %link'); ?>
				                  </div>
				                  <div class="center-divider"></div>
				                  <div class="next">
				                  <?php next_post_link('<p>NEXT POST <span class="fa fa-angle-right"></span></p> %link'); ?>
				                  </div>
				                  <div class="clear"></div>
				                </div> <!-- next-prev-post -->

								<?php if ( get_theme_mod('urbanbold_author_bio') ) :
				                     	$author_class = 'author-hide';

				                    else:
				                   		$author_class = '';
				                    endif;
				                ?>

				                <footer class="article-footer <?php echo $author_class; ?>">
				                  <div class="avatar">
				                  	<?php echo get_avatar( get_the_author_meta( 'ID' ) , 80 ); ?>
				                  </div>
				                  <div class="info">
					                  <p class="author"><span><?php _e('Written by','urban-bold'); ?></span> <?php the_author(); ?></p>
					                  <p class="author-desc"> <?php if (function_exists('urbanbold_author_excerpt')){echo urbanbold_author_excerpt();} ?> </p>
				                  </div>
				                  <div class="clear"></div>
				                </footer> <?php // end article footer ?>

				                <?php comments_template(); ?>

				             </article> <?php // end article ?>

						<?php endwhile; ?>

						<?php else : ?>

							<article id="post-not-found" class="hentry cf">
									<header class="article-header">
										<h1><?php _e( 'Oops, Post Not Found!', 'urban-bold' ); ?></h1>
										<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'urban-bold' ); ?></p>
									</header>
							</article>

						<?php endif; ?>

					</div>

					<?php get_sidebar(); ?>

				</div>

			</div>

<?php get_footer(); ?>