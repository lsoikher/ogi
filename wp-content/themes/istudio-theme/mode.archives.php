<?php /* Template Name: Archives */
get_header();?>
<div id="content_post">
  <?php if(have_posts()){while(have_posts()){the_post();?>
  <section id="post-<?php the_ID();?>" <?php post_class();?>>
    <div class="title">
      <h2><?php the_title();?></h2>
    </div>
    <div id="archives" class="entry">
      <div id="arslink">
        <ul><?php wp_get_archives('type=monthly');?></ul>
      </div>
      <div class="line">
        <?php _e('Recent Articles','iStudio');?>
      </div>
      <ul class="ul"><?php wp_get_archives('type=postbypost&limit=25');?></ul>
    </div>
    <?php }}?>
    <p class="postmeta"><?php edit_post_link('Edit this entry.','[',']');?></p>
  </section>
</div>
<?php get_footer();?>