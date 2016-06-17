<?php get_header(); ?>

			<div id="content">
				
				<div id="inner-content" class="wrap cf">
					<div class="home-content-area">
						<ul class="blog-list">
							<header class="article-header">
								<h1 class="archive-title"><span><?php _e( 'Search Results for:', 'urban-bold' ); ?></span> <?php echo esc_attr(get_search_query()); ?></h1>
							</header>
							
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			  						<?php get_template_part( 'home-post-format/home', get_post_format() ); ?>
			  				<?php endwhile;  ?>
			  				<?php else : ?>

									<article id="post-not-found" class="hentry cf">
										<header class="article-header">
											<h1><?php _e( 'Oops, Post Not Found!', 'omg' ); ?></h1>
										</header>
										<section class="entry-content">
											<p><?php _e( 'The article you were looking for was not found. You may want to check your link or perhaps that post/page does not exist anymore.', 'omg' ); ?></p>
										</section>
									</article>
			  				<?php endif; ?>
		     				<div class="clear"></div>
		     				<?php  urbanbold_page_navi(); ?>
							<?php wp_reset_postdata(); ?>
						</ul>
						<div id="sidebar5" class="sidebar m-all t-1of3 d-2of7 last-col cf" role="complementary">

							<?php if ( is_active_sidebar( 'sidebar5' ) ) : ?>

								<?php dynamic_sidebar( 'sidebar5' ); ?>

							<?php else : ?>

								<?php
									/*
									 * This content shows up if there are no widgets defined in the backend.
									*/
								?>

								<div class="no-widgets">
									<p><?php _e( 'This is a widget ready area. Add some and they will appear here.', 'urban-bold' );  ?></p>
								</div>

							<?php endif; ?>

						</div> <!-- sidebar -->
						<div class="clear"></div>
					</div><!-- content-area -->
				</div> <!-- inner-content -->

			</div>

<?php get_footer(); ?>
