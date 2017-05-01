<?php $pagenavnum=istOption('pagenavnum');
get_header();?>
<div id="content">
<?php istudio_randompic();?>
<article>
<?php if(have_posts()){?>
  <div class="archive"><span class="title"><?php _e('Search Results', 'iStudio');?></span></div>
  <?php while(have_posts()){the_post();?>
  <section id="post-<?php the_ID();?>" <?php post_class();?>>
  	<div class="title">
     	<h3><?php the_time('F jS, Y');?></h3>
     	<h2><a href="<?php the_permalink();?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute();?>"><?php the_title();?></a></h2>
			<small><?php comments_popup_link('No Comments','1 Comment','% Comments');?>, <?php the_category(', ');?>, by <?php the_author();?><?php if(function_exists('the_views')){?>, <?php the_views();}?>.<?php edit_post_link('Edit',' [',']&#187;');?></small>
		</div>
    <div class="entry">
      <?php the_excerpt();?> 
    </div>
    <p class="postmeta"><?php the_tags('Tags: ',', ','');?></p>
  </section>
	<?php }?>
	<nav class="navigation">
  <?php if($pagenavnum=='default'){if(function_exists('wp_pagenavi')){wp_pagenavi();}else{?>
		<div class="alignleft"><?php next_posts_link('&laquo; Older Entries');?></div>
    <div class="alignright"><?php previous_posts_link('Newer Entries &raquo;');?></div>
    <div class="clear"></div> 
	<?php }}else{istudio_pagenavi();}?>
  </nav>
	<?php }else{?>
  <section>
   	<div class="title">
			<h2><?php _e('Not Found','iStudio');?></h2>
		</div>
		<div class="entry">
			<p><?php _e('Sorry, but you are looking for something that isn\'t here.','iStudio');?></p>
		</div>
  </section>
	<?php }?>
</article>
<?php get_sidebar();?>
<div class="clear"></div>  
</div>
<?php get_footer();?>