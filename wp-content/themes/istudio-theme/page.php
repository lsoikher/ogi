<?php $pageside=istOption('pageside');if(istOption('pagenavnum')=='default'){$pnavcalss='navigation';}else{$pnavcalss='page-links';}
get_header();
if($pageside){?>
<div id="content">
<article class="postlist">
<?php }else{?>
<div id="content_post">
<?php }
if(have_posts()){while(have_posts()){the_post();?>
  <section id="post-<?php the_ID();?>" <?php post_class();?>>
    <div class="title">
      <h2><?php the_title();?></h2>
    </div>
    <div class="entry">
      <?php the_content();
	  wp_link_pages('before=<nav id="'.$pnavcalss.'">&after=</nav>&next_or_number=number&pagelink=<span>%</span>');?>
    </div>
    <p class="postmeta"><?php edit_post_link('Edit this entry.','[',']');?></p>
  </section>
  <?php comments_template('',true);}}
if($pageside){?>
</article>
<?php get_sidebar();?>
<div class="clear"></div>  
</div>
<?php }else{?>
</div>
<?php }
get_footer();?>