<?php
/*
 Template Name: Full-width
 
*/
?>

<?php get_header(); ?>

		<div id="content">
			
			<div id="inner-content" class="wrap cf single-content">

					<div id="main" class="m-all t-2of3 d-5of7 cf full" role="main">

						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

						<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
							<header class="article-header">
								<h1 class="page-title"><?php the_title(); ?></h1>
							</header>
						
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
							</section>

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
		

			</div>

		</div>


<?php get_footer(); ?>