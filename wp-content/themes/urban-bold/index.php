<?php get_header(); ?>
		<div class="front-wrapper">
			
			<div id="content">
				
				<div id="inner-content" class="wrap cf slider">
					<?php if ( get_theme_mod('urbanbold_slider_area') ):
		                    $slider_class = 'slider-hide';
		                    else:
		                    $slider_class = '';
		                    endif;
                	?>
					<div id="slide-wrap" class="<?php echo esc_attr( $slider_class ); ?>">
			        	<?php 
			                $args = array(
			                    'posts_per_page' => 10,
								'post_status' => 'publish',
			                    'post__in' => get_option("sticky_posts")
			                );
			                $fPosts = new WP_Query( $args );
			              ?>
				  
						<?php if ( $fPosts->have_posts() ) : ?>
	            
		           			<div class="cycle-slideshow" <?php 
							  	if ( get_theme_mod('urbanbold_slider_effect') ) :
									echo 'data-cycle-fx="' . wp_kses_post( get_theme_mod('urbanbold_slider_effect') ) . '" data-cycle-tile-count="10"';
								else:
									echo 'data-cycle-fx="scrollHorz"';
								endif;
						   	?> data-cycle-slides="> div.slides" <?php
			                  	if ( get_theme_mod('urbanbold_slider_timeout') ):
									$slider_timeout = wp_kses_post( get_theme_mod('urbanbold_slider_timeout') );
									echo 'data-cycle-timeout="' . $slider_timeout . '000"';
								else:
									echo 'data-cycle-timeout="5000"';
								endif;
						  	?> data-cycle-prev="#sliderprev" data-cycle-next="#slidernext">
	            
	            		<?php while ( $fPosts->have_posts() ) : $fPosts->the_post();  ?>
				 
								<div class="slides">
					              
									<div id="post-<?php the_ID(); ?>" <?php post_class('post-theme'); ?>>

									 <?php 
									 	$image_full = urbanbold_catch_that_image(); 
									 	$gallery_full = urbanbold_catch_gallery_image_full(); 
									 ?>

									 <?php if ( has_post_thumbnail()) : ?>
									    <div class="slide-thumb"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( "full" ); ?></a></div>
									 
									 <?php elseif(has_post_format('image') && !empty($image_full)) :  ?>
										<div class="slide-thumb"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php echo $image_full; ?></a></div>
									 
									 <?php elseif(has_post_format('gallery') && !empty($gallery_full)) : ?>  
									 	<div class="slide-thumb"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php echo $gallery_full; ?></a></div>
									 
									 <?php else: ?>
										<div class="slide-noimg"><p><?php _e('', 'urban-bold') ?></p></div>
									 
									 <?php endif;  ?>
									   						
									</div>

									<div class="slide-copy-wrap">
										<div class="slide-copy">
											<div class="table">
												<div class="table-cell">
									  				<h2 class="slide-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'urban-bold' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
												</div>
											</div>
										</div>
									</div>
					              
					            </div>
	    
			 			<?php endwhile; ?>
			            
					            <div class="slidernav">
					                <a id="sliderprev" href="#" title="<?php _e('Previous', 'urban-bold'); ?>"><?php _e('&#9664;', 'urban-bold'); ?></a>
					                <a id="slidernext" href="#" title="<?php _e('Next', 'urban-bold'); ?>"><?php _e('&#9654;', 'urban-bold'); ?></a>
					            </div>

			            	</div>
			            <?php endif; ?>
			            
			          <?php wp_reset_postdata(); ?>

	    			 </div> <!-- slider-wrap -->
 				</div>

				<div class="wrap cf">
					<div class="home-content-area">
						<div class="blog-list">
						<article>
							
								<?php $args2= array('post__not_in' => get_option( 'sticky_posts' ) ,'paged' => $paged);
								$blogPosts = new WP_Query( $args2 ); ?>
								<?php while ( $blogPosts -> have_posts() ) : $blogPosts -> the_post(); ?>
				  						<?php get_template_part( 'home-post-format/home', get_post_format() ); ?>
				  				<?php endwhile;  ?>
			     				
						</article>
						<div class="clear"></div>
			     				<?php  urbanbold_page_navi(); ?>
								<?php wp_reset_postdata(); ?>
						</div>
						<div id="sidebar3" class="sidebar m-all t-1of3 d-2of7 last-col cf" role="complementary">

							<?php if ( is_active_sidebar( 'sidebar3' ) ) : ?>

								<?php dynamic_sidebar( 'sidebar3' ); ?>

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
			</div> <!-- content -->
		</div><!-- front-wrapper -->

<?php get_footer(); ?>