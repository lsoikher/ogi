<?php if ( get_theme_mod('urbanbold_full_width') ) :
	
	$full_class = 'hide-sidebar';
	else:
	$full_class = '';

	endif;
?>

<div id="sidebar1" class="sidebar m-all t-1of3 d-2of7 last-col cf <?php echo $full_class; ?>" role="complementary">

	<?php if ( is_active_sidebar( 'sidebar1' ) ) : ?>

		<?php dynamic_sidebar( 'sidebar1' ); ?>

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

</div>