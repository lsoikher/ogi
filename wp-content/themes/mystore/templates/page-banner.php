<?php
if ( is_archive() || is_search() ) {
    if ( mystore_is_woocommerce_activated() ) {
        if ( is_shop() ) {
            $shop = get_option( 'woocommerce_shop_page_id' );
            $featured_image = wp_get_attachment_url( get_post_thumbnail_id( $shop ) );
        }
    } else {
        $page = get_option( 'page_for_posts' );
        $featured_image = wp_get_attachment_url( get_post_thumbnail_id( $page ) );
    }
} else {
    $page = get_queried_object();
    $featured_image = wp_get_attachment_url( get_post_thumbnail_id( $page->ID ) );
} ?>

<div class="page-banner" <?php echo ( $featured_image ) ? 'style="background-image: url(' . esc_url( $featured_image ) . ');"' : ''; ?>>
    
    <?php if ( get_theme_mod( 'mystore-banner-size', false ) == 'mystore-banner-size-small' ) : ?>
        <img src="<?php echo get_template_directory_uri() ?>/images/banner_blank_img_small.png" />
    <?php elseif ( get_theme_mod( 'mystore-banner-size', false ) == 'mystore-banner-size-medium' ) : ?>
        <img src="<?php echo get_template_directory_uri() ?>/images/banner_blank_img_medium.png" />
    <?php elseif ( get_theme_mod( 'mystore-banner-size', false ) == 'mystore-banner-size-large' ) : ?>
        <img src="<?php echo get_template_directory_uri() ?>/images/banner_blank_img_large.png" />
    <?php else : ?>
        <img src="<?php echo get_template_directory_uri() ?>/images/banner_blank_img_extra_small.png" />
    <?php endif; ?>
    
</div><!-- .page-banner -->
