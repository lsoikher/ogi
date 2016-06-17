<div class="item format">
	<?php if(is_plugin_active('advanced-custom-fields/acf.php') && get_field('wpdevshed_post_format_embed_video')) : ?>
		<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
			<?php echo get_field('wpdevshed_post_format_embed_video'); ?>
			<span class="fa fa-play"></span>
		</a>
	<?php endif; ?>
	<a href="<?php the_permalink(); ?>"><h2><?php the_title(); ?></h2></a>
	<div class="date"><?php _e('By ','urban-bold'); ?><?php the_author_posts_link(); echo " / "; ?><?php printf( __( '<span class="time">%2$s</span>', 'urban-bold' ), get_the_time('m-d-Y'), get_the_time(get_option('date_format'))); ?></div>
	<div class="excerpt"><?php the_excerpt(); ?></div>
</div>