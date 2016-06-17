<?php get_header(); ?>

			<div id="content">
				
				<div id="inner-content" class="wrap cf">
					
					<div id="main" class="cf" role="main">
						<header class="article-header">
								<h1><?php _e( 'Epic 404 - Article Not Found', 'urban-bold' ); ?></h1>
						</header>
						<article id="post-not-found" class="hentry cf full">


							<section class="entry-content">

								<p><?php _e( 'The article you were looking for was not found. You may want to check your link or perhaps that page does not exist anymore.', 'urban-bold' ); ?></p>

							</section>

							<section class="search">

									<p><?php get_search_form(); ?></p>

							</section>

						</article>

					</div>

				</div>

			</div>

<?php get_footer(); ?>