<section class="entry-content cf" itemprop="articleBody">
  <?php if(is_plugin_active('advanced-custom-fields/acf.php')) : ?>
    <blockquote>
    <p class="quote-content"><?php echo get_field('wpdevshed_post_format_quote_content'); ?></p>
    <p class="quote-source">-<?php echo get_field('wpdevshed_post_format_quote_source'); ?></p>
    </blockquote>
  <?php endif; ?>
  <?php

    the_content();

    wp_link_pages( array(
      'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'urban-bold' ) . '</span>',
      'after'       => '</div>',
      'link_before' => '<span>',
      'link_after'  => '</span>',
    ) );
  ?>
</section> <?php // end article section ?>