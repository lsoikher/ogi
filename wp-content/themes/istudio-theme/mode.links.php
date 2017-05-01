<?php /* Template Name: Links */
get_header();?>
<div id="content_post">
  <article>
    <section id="linkpage">
      <ul><?php wp_list_bookmarks();?></ul>
    </section>
    <p class="postmeta"><?php edit_post_link('Edit this entry.','[',']');?></p>
  </article>
</div>
<?php get_footer();?>