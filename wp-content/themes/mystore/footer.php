<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package myStore
 */
?>
		<div class="clearboth"></div>
	</div><!-- #content -->

	<?php get_template_part( '/templates/footers/footer-standard' ); ?>
	
</div><!-- #page -->

<?php if ( get_theme_mod( 'mystore-header-layout' ) == 'mystore-header-layout-standard' ) : ?>
	
	<!-- Do Nothing -->
	
<?php else : ?>

	<?php get_sidebar( 'menu' ) ?>

<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
