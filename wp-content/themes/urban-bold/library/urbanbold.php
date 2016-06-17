<?php
/* Welcome to urbanbold :)
This is the core urbanbold file where most of the
main functions & features reside. If you have
any custom functions, it's best to put them
in the functions.php file.

*********************/

// remove WP version from scripts
function urbanbold_remove_wp_ver_css_js( $src ) {
	if ( strpos( $src, 'ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}

// remove injected CSS for recent comments widget
function urbanbold_remove_wp_widget_recent_comments_style() {
	if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
		remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
	}
}

// remove injected CSS from recent comments widget
function urbanbold_remove_recent_comments_style() {
	global $wp_widget_factory;
	if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
		remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
	}
}

// remove injected CSS from gallery
function urbanbold_gallery_style($css) {
	return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );
}


/*********************
SCRIPTS & ENQUEUEING
*********************/

// loading modernizr and jquery, and reply script
function urbanbold_scripts_and_styles() {

  global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way

  if (!is_admin()) {

		// modernizr (without media query polyfill)
		wp_register_script( 'urbanbold-modernizr', get_template_directory_uri() . '/library/js/libs/modernizr.custom.min.js', array(), '2.5.3', false );

		// register main stylesheet
		wp_register_style( 'urbanbold-main-stylesheet', get_template_directory_uri() . '/style.css', array(), '', 'all' );
		wp_register_style( 'urbanbold-stylesheet', get_template_directory_uri() . '/library/css/style.min.css', array(), '', 'all' );

		// ie-only style sheet
		wp_register_style( 'urbanbold-ie-only', get_template_directory_uri() . '/library/css/ie.css', array(), '' );

		wp_register_style( 'urbanbold-font', get_template_directory_uri() . '/css/font-awesome.css', array(), '' );

	    // comment reply script for threaded comments
	    if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
			  wp_enqueue_script( 'comment-reply' );
	    }

		//adding scripts file in the footer
		wp_register_script( 'urbanbold-js', get_template_directory_uri() . '/library/js/scripts.js', array(), '', true );
		wp_register_script( 'urbanbold-js-scripts-home', get_template_directory_uri() . '/library/js/scripts-home.js', array(), '', true );

		wp_register_script( 'imagesloaded', get_template_directory_uri() . '/library/js/imagesloaded.pkgd.min.js', array(), '', true);
		wp_register_script( 'cycle2', get_template_directory_uri() . '/library/js/jquery.cycle2.min.js', array(), '', true );
		wp_register_script( 'cycle2_tile', get_template_directory_uri() . '/library/js/jquery.cycle2.tile.min.js' , array(), '', true);
		wp_register_script( 'cycle2_shuffle', get_template_directory_uri() . '/library/js/jquery.cycle2.shuffle.min.js', array(), '', true );
		wp_register_script( 'cycle2_scrollvert', get_template_directory_uri() . '/library/js/jquery.cycle2.scrollVert.min.js', array(), '', true );
		
		wp_enqueue_style( 'urbanbold-main-stylesheet' );
		wp_enqueue_style( 'urbanbold-font' ); 
		wp_enqueue_script( 'urbanbold-modernizr' );
		wp_enqueue_style( 'urbanbold-stylesheet' );
		wp_enqueue_style( 'urbanbold-ie' );
		wp_enqueue_style( 'urbanbold-ie-only' );
		wp_enqueue_script( 'jquery-effects-core ');
		wp_enqueue_script( 'jquery-effects-slide');

		if ( is_home() && get_theme_mod('urbanbold_slider_area') == '' ){
		wp_enqueue_script( 'imagesloaded' );
		wp_enqueue_script( 'cycle2' );
		wp_enqueue_script( 'cycle2_tile' );
		wp_enqueue_script( 'cycle2_shuffle' );
		wp_enqueue_script( 'cycle2_scrollvert' );
		wp_enqueue_script( 'urbanbold-js-scripts-home' );
		}

		$wp_styles->add_data( 'urbanbold-ie-only', 'conditional', 'lt IE 9' ); // add conditional wrapper around ie stylesheet
		wp_enqueue_script( 'urbanbold-js' );

	}
}
/*********************
THEME SUPPORT
*********************/

// Adding WP 3+ Functions & Theme Support
function urbanbold_theme_support() {

	// wp thumbnails (sizes handled in functions.php)
	add_theme_support( 'post-thumbnails' );
	add_editor_style();
	// default thumb size
	set_post_thumbnail_size(125, 125, true);

	// wp custom background (thx to @bransonwerner for update)
	add_theme_support( 'custom-background',
	    array(
	    'default-image' => '',    // background image default
	    'default-color' => 'dddddd',    // background color default (dont add the #)
	    'wp-head-callback' => '_custom_background_cb',
	    'admin-head-callback' => '',
	    'admin-preview-callback' => ''
	    )
	);

	// rss thingy
	add_theme_support('automatic-feed-links');

	// to add header image support go here: http://themble.com/support/adding-header-background-image-support/

	// adding post format support
	add_theme_support( 'post-formats',
		array(
			'aside',             // title less blurb
			'gallery',           // gallery of images
			'link',              // quick link to other site
			'image',             // an image
			'quote',             // a quick quote
			'status',            // a Facebook like status update
			'video',             // video
			'audio',             // audio
			'chat'               // chat transcript
		)
	);


	// registering wp3+ menus
	register_nav_menus(
		array(
			'main-nav' => __( 'The Main Menu', 'urban-bold' ),   // main nav in header
			/*'footer-links' => __( 'Footer Links', 'urban-bold' ) // secondary nav in footer*/
		)
	);
	add_theme_support( 'title-tag' );

} /* end urbanbold theme support */

if ( ! function_exists( '_wp_render_title_tag' ) ) :
    function urbanbold_render_title() {
		?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}
    add_action( 'wp_head', 'urbanbold_render_title' );
    
    add_filter( 'wp_title', 'urbanbold_rw_title', 10, 3 );
	function urbanbold_rw_title( $title, $sep, $seplocation ) {
	  global $page, $paged;

	  // Don't affect in feeds.
	  if ( is_feed() ) return $title;

	  // Add the blog's name
	  if ( 'right' == $seplocation ):
	    $title .= get_bloginfo( 'name' );
	  else:
	    $title = get_bloginfo( 'name' ) . $title;
	  endif;

	  // Add the blog description for the home/front page.
	  $site_description = get_bloginfo( 'description', 'display' );

	  if ( $site_description && ( is_home() || is_front_page() ) ):
	    $title .= " {$sep} {$site_description}";
	  endif;

	  // Add a page number if necessary:
	  if ( $paged >= 2 || $page >= 2 ):
	    $title .= " {$sep} " . sprintf( __( 'Page %s', 'dbt' ), max( $paged, $page ) );
	  endif;

	  return $title;

	} // end better title

endif;




/*********************
RELATED POSTS FUNCTION
*********************/

// Related Posts Function (call using urbanbold_related_posts(); )
function urbanbold_related_posts() {
	echo '<ul id="urbanbold-related-posts">';
	global $post;
	$tags = wp_get_post_tags( $post->ID );
	if($tags) {
		foreach( $tags as $tag ) {
			$tag_arr .= $tag->slug . ',';
		}
		$args = array(
			'tag' => $tag_arr,
			'numberposts' => 5, /* you can change this to show more */
			'post__not_in' => array($post->ID)
		);
		$related_posts = get_posts( $args );
		if($related_posts) {
			foreach ( $related_posts as $post ) : setup_postdata( $post ); ?>
				<li class="related_post"><a class="entry-unrelated" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
			<?php endforeach; }
		else { ?>
			<?php echo '<li class="no_related_post">' . __( 'No Related Posts Yet!', 'urban-bold' ) . '</li>'; ?>
		<?php }
	}
	wp_reset_postdata();
	echo '</ul>';
} /* end urbanbold related posts function */

/*********************
PAGE NAVI
*********************/

// Numeric Page Navi (built into the theme by default)
function urbanbold_page_navi() {
  global $wp_query, $paged;
  $bignum = 999999999;
  if ( $wp_query->max_num_pages <= 1 )
    return;
  echo '<div class="pagination">';
  echo paginate_links( array(
    'base'         => str_replace( $bignum, '%#%', esc_url( get_pagenum_link($bignum) ) ),
    'format'       => '',
    'current'      => max( 1, get_query_var('paged') ),
    'total'        => $wp_query->max_num_pages,
    'prev_text'    => '<i class="fa fa-chevron-left"></i>',
    'next_text'    => '<i class="fa fa-chevron-right"></i>',
    'type'         => 'list',
    'end_size'     => 3,
    'mid_size'     => 3
  ) );
  echo '</div>';
} /* end page navi */

/*********************
RANDOM CLEANUP ITEMS
*********************/

// This removes the annoying […] to a Read More link
function urbanbold_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '...  <a class="read-more-link" href="'. get_permalink($post->ID) . '" title="'. __( 'Read ', 'urban-bold' ) . get_the_title($post->ID).'">'. __( 'Read more', 'urban-bold' ) .'</a>';
}

add_action( 'wp_enqueue_media', 'urbanbold_mgzc_media_gallery_zero_columns' );
function urbanbold_mgzc_media_gallery_zero_columns(){
    add_action( 'admin_print_footer_scripts', 'urbanbold_mgzc_media_gallery_zero_columns_script', 999);
}
function urbanbold_mgzc_media_gallery_zero_columns_script(){
?>
<script type="text/javascript">
jQuery(function(){
    if(wp.media.view.Settings.Gallery){
        wp.media.view.Settings.Gallery = wp.media.view.Settings.extend({
            className: "gallery-settings",
            template: wp.media.template("gallery-settings"),
            render: function() {
                wp.media.View.prototype.render.apply( this, arguments );
                // Append an option for 0 (zero) columns if not already present...
                var $s = this.$('select.columns');
               
                   $s.find('option[value="5"]').remove();
                   $s.find('option[value="6"]').remove();
                   $s.find('option[value="7"]').remove();
                   $s.find('option[value="8"]').remove();
                   $s.find('option[value="9"]').remove();
                // Select the correct values.
                _( this.model.attributes ).chain().keys().each( this.update, this );
                return this;
            }
        });
    }
});
</script>
<?php
}
/*video*/

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_video',
		'title' => 'Video',
		'fields' => array (
			array (
				'key' => 'field_542906321cdab',
				'label' => __('Embed Video','urban-bold'),
				'name' => 'wpdevshed_post_format_embed_video',
				'type' => 'textarea',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'formatting' => 'html',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_format',
					'operator' => '==',
					'value' => 'video',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

/*link*/
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_link',
		'title' => 'Link',
		'fields' => array (
			array (
				'key' => 'field_54290c22892fe',
				'label' => __('Link','urban-bold'),
				'name' => 'wpdevshed_post_format_link_url',
				'type' => 'text',
				'instructions' => 'place url here',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_format',
					'operator' => '==',
					'value' => 'link',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

/*quote*/
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_quote',
		'title' => 'Quote',
		'fields' => array (
			array (
				'key' => 'field_5428fc13708c4',
				'label' => __('Quote Content','urban-bold'),
				'name' => 'wpdevshed_post_format_quote_content',
				'type' => 'textarea',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'formatting' => 'none',
			),
			array (
				'key' => 'field_5428fc4e3e3fc',
				'label' => __('Quote Source','urban-bold'),
				'name' => 'wpdevshed_post_format_quote_source',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_format',
					'operator' => '==',
					'value' => 'quote',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_audio',
		'title' => 'Audio',
		'fields' => array (
			array (
				'key' => 'field_542a4c44cc3c2',
				'label' => __('Upload Audio File Here','urban-bold'),
				'name' => 'wpdevshed_post_format_audio_content',
				'type' => 'file',
				'save_format' => 'url',
				'library' => 'all',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_format',
					'operator' => '==',
					'value' => 'audio',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_status',
		'title' => 'Status',
		'fields' => array (
			array (
				'key' => 'field_542a5b07626a0',
				'label' => __('Insert Short Status Here','urban-bold'),
				'name' => 'wpdevshed_post_format_status_content',
				'type' => 'textarea',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'formatting' => 'none',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_format',
					'operator' => '==',
					'value' => 'status',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_chat',
		'title' => 'Chat',
		'fields' => array (
			array (
				'key' => 'field_542a5d28507df',
				'label' => __('Insert Chat Conversation here','urban-bold'),
				'name' => 'wpdevshed_post_format_chat_content',
				'type' => 'wysiwyg',
				'default_value' => '',
				'toolbar' => 'basic',
				'media_upload' => 'yes',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_format',
					'operator' => '==',
					'value' => 'chat',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}


?>