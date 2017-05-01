<?php $showside=istOption('showside');if(istOption('pagenavnum')=='default'){$pnavcalss='navigation';}else{$pnavcalss='page-links';}if(istOption('adcode_singlecont')){$singlecontad=istOption('adcode_singlecont');$singlecontshow=istOption('ad_show_singlecont');$scadfloat=istOption('adfloat');}else{$singlecontad=NULL;}if($singlead=istOption('adcode_single')){$singlead=istOption('adcode_single');$singleshow=istOption('ad_show_single');}else{$singlead=NULL;}
get_header();
if($showside){?>
<div id="content">
<?php istudio_randompic();?>
<article>
<?php }else{?>
<div id="content_post">
<?php }
if(have_posts()){while(have_posts()){the_post();?>
  <section id="post-<?php the_ID();?>" <?php post_class();?>>
    <div class="title">
      <h3><?php the_time('F jS, Y');?> <!-- by <?php the_author();?> --></h3>
      <h2><?php the_title();?></h2>
      <small><?php if(function_exists('the_views')){the_views();?>, <?php }?><?php the_category(', ');?>, by <?php the_author();?>.<?php edit_post_link('Edit',' [',']&#187;');?></small>
    </div>
    <div class="entry">
      <?php if($singlecontad){if($singlecontshow){if(!is_user_logged_in()){echo "<section style=\"float:".$scadfloat.";overflow:hidden;margin:0 12px 10px;width:300px;height:250px;\">\n".$singlecontad."</section>\n";}
      }else{echo "<section style=\"float:".$scadfloat.";overflow:hidden;margin:0 12px 10px;width:300px;height:250px;\">\n".$singlecontad."</section>\n";}}
      the_content('More');
      wp_link_pages('before=<nav id="'.$pnavcalss.'">&after=</nav>&next_or_number=number&pagelink=<span>%</span>');?>
      <div class="clear"></div> 
    </div>
    <p align="right"><a href="#" class="gotop" onClick="istoJS.goTop();return false;" title="Back Top">Back Top</a></p>
    <p class="postmeta"><?php the_tags('Tags: ',', ','');?></p>
    <nav class="post-nav">
      <span class="previous"><?php previous_post_link('%link');?></span>
      <span class="next"><?php next_post_link('%link');?></span>
      <div class="clear"></div>
    </nav>
    <?php if($singlead){if($singleshow){if(!is_user_logged_in()){echo "<section style=\"padding:10px 0;\">\n".$singlead."</section>\n";}}else{echo "<section style=\"padding:10px 0;\">\n".$singlead."</section>\n";}}?>
  </section>
  <?php comments_template('',true);}}
if($showside){?>
</article>
<?php get_sidebar();?>
<div class="clear"></div>  
</div>
<?php }else{?>
</div>
<?php }get_footer();?>