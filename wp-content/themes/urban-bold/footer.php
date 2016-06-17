			<footer class="footer" role="contentinfo">

				<div id="inner-footer" class="wrap cf footer-inner">
					<?php if ( is_active_sidebar( 'sidebar4' ) ) : ?>
					<div class="footer-widgets">
						<div id="sidebar4" class="sidebar m-all t-1of3 d-2of7 last-col cf" role="complementary">

								<?php dynamic_sidebar( 'sidebar4' ); ?>

						</div> <!-- sidebar -->
						<div class="clear"></div>
					</div>
					<?php endif; ?>
					<p class="source-org copyright">
						 &#169; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?> 
						<span><?php if(is_home()): ?>
							- <a href="http://wordpress.org/" target="_blank">Powered by WordPress</a> and <a href="http://deucethemes.com" target="_blank">Deuce Themes</a> 
						<?php endif; ?>
						</span>
					</p>

				</div>

			</footer>

		</div>
		<a href="#" class="scrollToTop"><span class="fa fa-caret-square-o-up"></span><?php _e( 'Back to Top', 'urban-bold' );  ?></a>
		<?php wp_footer(); ?>
	</body>

</html> <!-- end of site. what a ride! -->