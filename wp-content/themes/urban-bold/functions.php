<?php

// LOAD Urban Bold CORE (if you remove this, the theme will break)
require_once( 'library/urbanbold.php' );

function urbanbold_ahoy() {

  // let's get language support going, if you need it
  load_theme_textdomain( 'urban-bold', get_template_directory() . '/library/translation' );

  // remove pesky injected css for recent comments widget
  add_filter( 'wp_head', 'urbanbold_remove_wp_widget_recent_comments_style', 1 );
  // clean up comment styles in the head
  add_action( 'wp_head', 'urbanbold_remove_recent_comments_style', 1 );
  // clean up gallery output in wp
  add_filter( 'gallery_style', 'urbanbold_gallery_style' );

  // enqueue base scripts and styles
  add_action( 'wp_enqueue_scripts', 'urbanbold_scripts_and_styles', 999 );
  // ie conditional wrapper

  // launching this stuff after theme setup
  urbanbold_theme_support();
 

  // adding sidebars to Wordpress (these are created in functions.php)
  add_action( 'widgets_init', 'urbanbold_register_sidebars' );

  // cleaning up excerpt
  add_filter( 'excerpt_more', 'urbanbold_excerpt_more' );

} /* end urbanbold ahoy */

// let's get this party started
add_action( 'after_setup_theme', 'urbanbold_ahoy' );


/************* OEMBED SIZE OPTIONS *************/

if ( ! isset( $content_width ) ) {
  $content_width = 640;
}

/************* THUMBNAIL SIZE OPTIONS *************/

// Thumbnail sizes
add_image_size( 'urbanbold-thumb-600', 600, 150, true );
add_image_size( 'urbanbold-thumb-300', 300, 100, true );
add_image_size( 'urbanbold-slider-image', 1280, 500, true );
add_image_size( 'urbanbold-thumb-image-300by300', 300, 300, true );


add_filter( 'image_size_names_choose', 'urbanbold_custom_image_sizes' );
function urbanbold_custom_image_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'urbanbold-thumb-600' => '600px by 150px',
        'urbanbold-thumb-300' => '300px by 100px',
        'urbanbold-slider-image' => '1280px by 500px'
    ) );
}

/************* ACTIVE SIDEBARS ********************/

// Sidebars & Widgetizes Areas
function urbanbold_register_sidebars() {
  register_sidebar(array(
    'id' => 'sidebar1',
    'name' => __( 'Posts Sidebar', 'urban-bold' ),
    'description' => __( 'The Posts sidebar.', 'urban-bold' ),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h4 class="widgettitle">',
    'after_title' => '</h4>',
  ));

  register_sidebar(array(
    'id' => 'sidebar2',
    'name' => __( 'Page Sidebar', 'urban-bold' ),
    'description' => __( 'The Page sidebar.', 'urban-bold' ),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h4 class="widgettitle">',
    'after_title' => '</h4>',
  ));

  register_sidebar(array(
    'id' => 'sidebar3',
    'name' => __( 'Home Page Sidebar', 'urban-bold' ),
    'description' => __( 'The Home page sidebar.', 'urban-bold' ),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h4 class="widgettitle">',
    'after_title' => '</h4>',
  ));

   register_sidebar(array(
    'id' => 'sidebar4',
    'name' => __( 'Footer Widgets', 'urban-bold' ),
    'description' => __( 'The Footer Widget area.', 'urban-bold' ),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h4 class="widgettitle">',
    'after_title' => '</h4>',
  ));

   register_sidebar(array(
    'id' => 'sidebar5',
    'name' => __( 'Archive Page Sidebar', 'urban-bold' ),
    'description' => __( 'The Archive page sidebar.', 'urban-bold' ),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h4 class="widgettitle">',
    'after_title' => '</h4>',
  ));


} // don't remove this bracket!


/************* COMMENT LAYOUT *********************/

// Comment Layout
function urbanbold_comments( $comment, $args, $depth ) {
   $GLOBALS['comment'] = $comment; ?>
  <div id="comment-<?php comment_ID(); ?>" <?php comment_class('cf'); ?>>
    <article  class="cf">
      <header class="comment-author vcard">
        <?php
        /*
          this is the new responsive optimized comment image. It used the new HTML5 data-attribute to display comment gravatars on larger screens only. What this means is that on larger posts, mobile sites don't have a ton of requests for comment images. This makes load time incredibly fast! If you'd like to change it back, just replace it with the regular wordpress gravatar call:
          echo get_avatar($comment,$size='32',$default='<path_to_url>' );
        */
        ?>
        <?php // custom gravatar call ?>
        <?php
          // create variable
          $bgauthemail = get_comment_author_email();
        ?>
        <img data-gravatar="http://www.gravatar.com/avatar/<?php echo md5( $bgauthemail ); ?>?s=80" class="load-gravatar avatar avatar-48 photo" height="80" width="80" src="<?php echo get_template_directory_uri(); ?>/library/images/nothing.gif" />
        <?php // end custom gravatar call ?>
        
      </header>
      <?php if ($comment->comment_approved == '0') : ?>
        <div class="alert alert-info">
          <p><?php _e( 'Your comment is awaiting moderation.', 'urban-bold' ) ?></p>
        </div>
      <?php endif; ?>
      <section class="comment_content cf">
        <?php printf(__( '<cite class="fn">%1$s</cite> %2$s', 'urban-bold' ), get_comment_author_link(), edit_comment_link(__( '(Edit)', 'urban-bold' ),'  ','') ) ?>
        <time datetime="<?php echo comment_time('Y-m-j'); ?>"><?php comment_time(__( 'F jS, Y', 'urban-bold' )); ?></time>
        <div class="comment-content">
          <?php comment_text() ?>
        </div>
        <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
      </section>
    </article>
  <?php // </li> is added by WordPress automatically ?>
<?php
} // don't remove this bracket!

add_filter( 'comment_form_defaults', 'urbanbold_remove_comment_form_allowed_tags' );
function urbanbold_remove_comment_form_allowed_tags( $defaults ) {

  $defaults['comment_notes_after'] = '';
  return $defaults;

}


/*
This is a modification of a function found in the
twentythirteen theme where we can declare some
external fonts. If you're using Google Fonts, you
can replace these fonts, change it in your scss files
and be up and running in seconds.
*/
function urbanbold_fonts() {
  wp_register_style('urbanboldFonts', get_template_directory_uri() . '/fonts/raleway-font.css');
  wp_enqueue_style( 'urbanboldFonts');
}

add_action('wp_print_styles', 'urbanbold_fonts');

/*******************************************************************
* These are settings for the Theme Customizer in the admin panel. 
*******************************************************************/
if ( ! function_exists( 'urbanbold_theme_customizer' ) ) :
  function urbanbold_theme_customizer( $wp_customize ) {
  
    /* color scheme option */
    $wp_customize->add_setting( 'urbanbold_primary_color_settings', array (
      'default' => '#203748',
      'sanitize_callback' => 'sanitize_hex_color',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'urbanbold_primary_color_settings', array(
      'label'    => __( 'Primary Color Scheme', 'urban-bold' ),
      'section'  => 'colors',
      'settings' => 'urbanbold_primary_color_settings',
    ) ) );

    $wp_customize->add_setting( 'urbanbold_secondary_color_settings', array (
      'default' => '#E5756E',
      'sanitize_callback' => 'sanitize_hex_color',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'urbanbold_secondary_color_settings', array(
      'label'    => __( 'Secondary Color Scheme', 'urban-bold' ),
      'section'  => 'colors',
      'settings' => 'urbanbold_secondary_color_settings',
    ) ) );

    $wp_customize->add_setting( 'urbanbold_links_color_settings', array (
      'default' => '#37BC9B',
      'sanitize_callback' => 'sanitize_hex_color',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'urbanbold_links_color_settings', array(
      'label'    => __( 'Links and Button Color Scheme', 'urban-bold' ),
      'section'  => 'colors',
      'settings' => 'urbanbold_links_color_settings',
    ) ) );

    
    /* logo option */
    $wp_customize->add_section( 'urbanbold_logo_section' , array(
      'title'       => __( 'Site Logo', 'urban-bold' ),
      'priority'    => 1,
      'description' => __( 'Upload a logo to replace the default site name in the header', 'urban-bold' ),
    ) );
    
    $wp_customize->add_setting( 'urbanbold_logo', array(
      'sanitize_callback' => 'esc_url_raw',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'urbanbold_logo', array(
      'label'    => __( 'Choose your logo (ideal width is 100-350px and ideal height is 35-40)', 'urban-bold' ),
      'section'  => 'urbanbold_logo_section',
      'settings' => 'urbanbold_logo',
    ) ) );
  
    /* favicon option */
    $wp_customize->add_section( 'urbanbold_favicon_section' , array(
      'title'       => __( 'Site favicon', 'urban-bold' ),
      'priority'    => 2,
      'description' => __( 'Upload a favicon', 'urban-bold' ),
    ) );
    
    $wp_customize->add_setting( 'urbanbold_favicon', array(
      'sanitize_callback' => 'esc_url_raw',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'urbanbold_favicon', array(
      'label'    => __( 'Choose your favicon (ideal width and height is 16x16 or 32x32)', 'urban-bold' ),
      'section'  => 'urbanbold_favicon_section',
      'settings' => 'urbanbold_favicon',
    ) ) );
    
    
    /* slider options */
    
    $wp_customize->add_section( 'urbanbold_slider_section' , array(
      'title'       => __( 'Slider Options', 'urban-bold' ),
      'priority'    => 33,
      'description' => __( 'Adjust the behavior of the image slider.', 'urban-bold' ),
    ) );
    
    $wp_customize->add_setting( 'urbanbold_slider_area', array (
      'sanitize_callback' => 'urbanbold_sanitize_checkbox',
    ) );
    
    $wp_customize->add_control('slider_area', array(
      'settings' => 'urbanbold_slider_area',
      'label' => __('Disable home page slider?', 'urbanbold'),
      'section' => 'urbanbold_slider_section',
      'type' => 'checkbox',
    ));

    $wp_customize->add_setting( 'urbanbold_slider_effect', array(
      'default' => 'scrollHorz',
      'capability' => 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control( 'effect_select_box', array(
      'settings' => 'urbanbold_slider_effect',
      'label' => __( 'Select Effect:', 'urban-bold' ),
      'section' => 'urbanbold_slider_section',
      'type' => 'select',
      'choices' => array(
        'scrollHorz' => 'Horizontal (Default)',
        'scrollVert' => 'Vertical',
        'tileSlide' => 'Tile Slide',
        'tileBlind' => 'Blinds',
        'shuffle' => 'Shuffle',
      ),
    ));
    
    $wp_customize->add_setting( 'urbanbold_slider_timeout', array (
      'sanitize_callback' => 'urbanbold_sanitize_integer',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'urbanbold_slider_timeout', array(
      'label'    => __( 'Autoplay Speed in Seconds', 'urban-bold' ),
      'section'  => 'urbanbold_slider_section',
      'settings' => 'urbanbold_slider_timeout',
    ) ) );

     /* author bio in posts option */
    $wp_customize->add_section( 'urbanbold_post_options' , array(
      'title'       => __( 'Post Options', 'urban-bold' ),
      'priority'    => 35,
      'description' => __( '', 'urban-bold' ),
    ) );

    $wp_customize->add_setting( 'urbanbold_image_post', array (
      'sanitize_callback' => 'urbanbold_sanitize_checkbox',
    ) );
    
    $wp_customize->add_control('image_post', array(
      'settings' => 'urbanbold_image_post',
      'label' => __('Show Featured Image?', 'urban-bold'),
      'section' => 'urbanbold_post_options',
      'type' => 'checkbox',
    ));

    $wp_customize->add_setting( 'urbanbold_full_width', array (
      'sanitize_callback' => 'urbanbold_sanitize_checkbox',
    ) );
    
    $wp_customize->add_control('full_width', array(
      'settings' => 'urbanbold_full_width',
      'label' => __('Enable post full width template?', 'urban-bold'),
      'section' => 'urbanbold_post_options',
      'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting( 'urbanbold_author_bio', array (
      'sanitize_callback' => 'urbanbold_sanitize_checkbox',
    ) );
    
    $wp_customize->add_control('author_bio', array(
      'settings' => 'urbanbold_author_bio',
      'label' => __('Disable the Author Bio?', 'urban-bold'),
      'section' => 'urbanbold_post_options',
      'type' => 'checkbox',
    ));


    /* Page Options */
    $wp_customize->add_section( 'urbanbold_image_page_section' , array(
      'title'       => __( 'Page Options', 'urban-bold' ),
      'priority'    => 37,
      'description' => __( '', 'urban-bold' ),
    ) );
    
    $wp_customize->add_setting( 'urbanbold_image_page', array (
      'sanitize_callback' => 'urbanbold_sanitize_checkbox',
    ) );
    
    $wp_customize->add_control('image_page', array(
      'settings' => 'urbanbold_image_page',
      'label' => __('Show Featured Image?', 'urban-bold'),
      'section' => 'urbanbold_image_page_section',
      'type' => 'checkbox',
    ));
  
  }
endif;
add_action('customize_register', 'urbanbold_theme_customizer');

/**
 * Sanitize checkbox
 */
if ( ! function_exists( 'urbanbold_sanitize_checkbox' ) ) :
  function urbanbold_sanitize_checkbox( $input ) {
    if ( $input == 1 ) {
      return 1;
    } else {
      return '';
    }
  }
endif;


/**
 * Sanitize integer input
 */
if ( ! function_exists( 'urbanbold_sanitize_integer' ) ) :
  function urbanbold_sanitize_integer( $input ) {
    return absint($input);
  }
endif;
/*
* Apply Color Scheme
*/
if ( ! function_exists( 'urbanbold_apply_color' ) ) :
  function urbanbold_apply_color() {
    if ( get_theme_mod('urbanbold_primary_color_settings') ) {
  ?>
    <style>
       
    .wrap.header-inner,nav[role="navigation"] .nav li ul li a,p.format-link a,body .mejs-container .mejs-controls,body .mejs-container,
    .status-content,.chat-content,.blog-list .item blockquote,footer.footer[role="contentinfo"], footer.footer[role="contentinfo"] .footer-inner{ 
        background: <?php echo get_theme_mod('urbanbold_primary_color_settings'); ?>;
      }
    .entry-content blockquote{border-left: 4px solid <?php echo get_theme_mod('urbanbold_primary_color_settings'); ?>;}
    </style>
  <?php
    } 

    if ( get_theme_mod('urbanbold_secondary_color_settings') ) {

  ?>
    <style>
      body .slide-copy-wrap a,body .slide-copy-wrap a:hover,.blog-list .item h2,.widget h4, .widget h4 a,.article-header h1,
      body #comments-title, body .comment-reply-title{
        color: <?php echo get_theme_mod('urbanbold_secondary_color_settings'); ?>;
      }

      .blog-list .item.format span.fa,body .mejs-controls .mejs-time-rail .mejs-time-loaded, body .mejs-controls .mejs-time-rail .mejs-time-total,.archive-title{
        background: <?php echo get_theme_mod('urbanbold_secondary_color_settings'); ?>;
      }

      footer.footer[role="contentinfo"] p.copyright{
        border-top: 2px solid <?php echo get_theme_mod('urbanbold_secondary_color_settings'); ?>;
      }

    </style>

  <?php
    }

    if ( get_theme_mod('urbanbold_links_color_settings') ) {

  ?>
    <style>
     a, a:visited,p.format-link a,nav[role="navigation"] .nav li ul li.current-menu-item a,footer.footer[role="contentinfo"] .sidebar .widget ul li:before, footer.footer[role="contentinfo"] .sidebar .widget ul li a,
     footer.footer[role="contentinfo"] p.copyright span a,body .byline a,body .tag-links a,body .pagination li a{
        color: <?php echo get_theme_mod('urbanbold_links_color_settings'); ?>;
     }

     .searchform input[type="text"],body .pagination li a,body .pagination li span.current{
      border: 2px solid  <?php echo get_theme_mod('urbanbold_links_color_settings'); ?>;
     }
     button, html input[type="button"], input[type="reset"], input[type="submit"],.blue-btn, #submit,nav[role="navigation"] .nav li.current-menu-item,.blog-list .item .excerpt a.read-more-link,
     body .pagination li span.current,body .pagination li a:hover,body .pagination li a:focus{
      background-color: <?php echo get_theme_mod('urbanbold_links_color_settings'); ?>;
     }
     @media screen and (max-width: 1039px) {
      #main-navigation ul li.current-menu-item a,#main-navigation ul li a:hover{
        color: <?php echo get_theme_mod('urbanbold_links_color_settings'); ?>;
      }
     #main-navigation{
      border-top: 2px solid <?php echo get_theme_mod('urbanbold_links_color_settings'); ?>;
     }
    }
    </style>

  <?php
    }
    
  }
endif;
add_action( 'wp_head', 'urbanbold_apply_color' );
/*-----------------------------------------------------------------------------------*/
/* custom functions below */
/*-----------------------------------------------------------------------------------*/
define('urbanbold_THEMEURL', get_template_directory_uri());
define('urbanbold_IMAGES', urbanbold_THEMEURL.'/images'); 
define('urbanbold_JS', urbanbold_THEMEURL.'/js');
define('urbanbold_CSS', urbanbold_THEMEURL.'/css');
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

add_filter( 'post_thumbnail_html', 'urbanbold_remove_thumbnail_dimensions', 10, 3 );

function urbanbold_remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}

function urbanbold_paginate() {
global $wp_query, $wp_rewrite;
$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;

$pagination = array(
    'base' => @add_query_arg('page','%#%'),
    'format' => '',
    'total' => $wp_query->max_num_pages,
    'current' => $current,
    'show_all' => true,
    'type' => 'list',
    'next_text' => '&raquo;',
    'prev_text' => '&laquo;'
    );

if( $wp_rewrite->using_permalinks() )
    $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 'page', get_pagenum_link( 1 ) ) ) . '?page=%#%/', 'paged' );

if( !empty($wp_query->query_vars['s']) )
    $pagination['add_args'] = array( 's' => get_query_var( 's' ) );

echo paginate_links( $pagination );
}
add_filter( 'the_content', 'urbanbold_remove_br_gallery', 11, 2);

function urbanbold_remove_br_gallery($output) {
    return preg_replace('/<br style=(.*)>/mi','',$output);
}

function urbanbold_author_excerpt() {
      $text_limit = 50; //Words to show in author bio excerpt
      $read_more  = "Read more"; //Read more text
      $end_of_txt = "...";
      $name_of_author = get_the_author();
      $url_of_author  = get_author_posts_url(get_the_author_meta('ID'));
      $short_desc_author = wp_trim_words(strip_tags(
                          get_the_author_meta('description')), $text_limit, 
                          $end_of_txt.'<br/>
                        <a href="'.$url_of_author.'" style="font-weight:bold;">'.$read_more .'</a>');

      return $short_desc_author;
}

function urbanbold_catch_that_image() {
global $post;
$pattern = '|<img.*?class="([^"]+)".*?/>|';
$transformed_content = apply_filters('the_content',$post->post_content);
preg_match($pattern,$transformed_content,$matches);
if (!empty($matches[1])) {
  $classes = explode(' ',$matches[1]);
  $id = preg_grep('|^wp-image-.*|',$classes);
  if (!empty($id)) {
    $id = str_replace('wp-image-','',$id);
    if (!empty($id)) {
      $id = reset($id);
      $transformed_content = wp_get_attachment_image($id,'full');  
      return $transformed_content;
    }
  }
}

}

function urbanbold_catch_that_image_thumb() {
global $post;
$pattern = '|<img.*?class="([^"]+)".*?/>|';
$transformed_content = apply_filters('the_content',$post->post_content);
preg_match($pattern,$transformed_content,$matches);
if (!empty($matches[1])) {
  $classes = explode(' ',$matches[1]);
  $id = preg_grep('|^wp-image-.*|',$classes);
  if (!empty($id)) {
    $id = str_replace('wp-image-','',$id);
    if (!empty($id)) {
      $id = reset($id);
      $transformed_content = wp_get_attachment_image($id,'thumbnail');  
       return $transformed_content;
    }
  }
}

}

function urbanbold_catch_gallery_image_full()  { 
  global $post;
  $gallery = get_post_gallery( $post, false );
  if ( !empty($gallery['ids']) ) {
    $ids = explode( ",", $gallery['ids'] );
    $total_images = 0;
    foreach( $ids as $id ) {
      
      $title = get_post_field('post_title', $id);
      $meta = get_post_field('post_excerpt', $id);
      $link = wp_get_attachment_url( $id );
      $image  = wp_get_attachment_image( $id, 'full');
      $total_images++;
      
      if ($total_images == 1) {
        $first_img = $image;
        return $first_img;
      }
    }
  } 
}

function urbanbold_catch_gallery_image_thumb()  { 
  global $post;
  $gallery = get_post_gallery( $post, false );
  if ( !empty($gallery['ids']) ) {
    $ids = explode( ",", $gallery['ids'] );
    $total_images = 0;
    foreach( $ids as $id ) {
      
      $title = get_post_field('post_title', $id);
      $meta = get_post_field('post_excerpt', $id);
      $link = wp_get_attachment_url( $id );
      $image  = wp_get_attachment_image( $id, 'thumbnail');
      $total_images++;
      
      if ($total_images == 1) {
        $first_img = $image;
        return $first_img;
      }
    }
  } 
}

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once get_template_directory() . '/library/class/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'urbanbold_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function urbanbold_register_required_plugins() {
 
    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
 
 
        // This is an example of how to include a plugin from the WordPress Plugin Repository.
        array(
            'name'      => 'Advanced Custom Fields',
            'slug'      => 'advanced-custom-fields',
            'required'  => false,
        ),
 
    );
 
    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'default_path' => '',                      // Default absolute path to pre-packaged plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        'strings'      => array(
            'page_title'                      => __( 'Install Required Plugins', 'urban-bold' ),
            'menu_title'                      => __( 'Install Plugins', 'urban-bold' ),
            'installing'                      => __( 'Installing Plugin: %s', 'urban-bold' ), // %s = plugin name.
            'oops'                            => __( 'Something went wrong with the plugin API.', 'urban-bold' ),
            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
            'return'                          => __( 'Return to Required Plugins Installer', 'urban-bold' ),
            'plugin_activated'                => __( 'Plugin activated successfully.', 'urban-bold' ),
            'complete'                        => __( 'All plugins installed and activated successfully. %s', 'urban-bold' ), // %s = dashboard link.
            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );
 
    tgmpa( $plugins, $config );
 
}
/* DON'T DELETE THIS CLOSING TAG */ ?>