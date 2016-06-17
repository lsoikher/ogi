<div class="item format">

	<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
		<?php $first_image_gallery = urbanbold_catch_gallery_image_full();
		if(isset($first_image_gallery)):
			echo $first_image_gallery; 
		endif; ?>
		<span class="fa fa-file-image-o"></span>
	</a>
	<a href="<?php the_permalink(); ?>"><h2><?php the_title(); ?></h2></a>
	<div class="date"><?php _e('By ','urban-bold'); ?><?php the_author_posts_link(); echo " / "; ?><?php printf( __( '<span class="time">%2$s</span>', 'urban-bold' ), get_the_time('m-d-Y'), get_the_time(get_option('date_format'))); ?></div>
	<div class="excerpt"><?php the_excerpt(); ?></div>

</div>