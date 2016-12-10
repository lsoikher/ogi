<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.codetides.com/
 * @since      1.0.0
 *
 * @package    Advanced_Floating_Content
 * @subpackage Advanced_Floating_Content/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Advanced_Floating_Content
 * @subpackage Advanced_Floating_Content/public
 * @author     Code Tides <contact@codetides.com>
 */
class Advanced_Floating_Content_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Advanced_Floating_Content_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Advanced_Floating_Content_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/advanced-floating-content-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Advanced_Floating_Content_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Advanced_Floating_Content_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/advanced-floating-content-public.js', array( 'jquery' ), $this->version, false );

	}
	
	/*
	* Display Floating Content
	*/
	public function load_floating_content()
	{
			$args = array( 
			  'post_type' => 'ct_afc', 
			  'posts_per_page' => -1,
			  'post_status' => 'publish',
			  'order' => 'ASC'
			);
			$floating_query = new WP_Query( $args );
			//echo $floating_query->found_posts;
			$css = "";
			if ( $floating_query->have_posts() ) :
				while ( $floating_query->have_posts() ) : $floating_query->the_post();
					$id_afc = $floating_query->post->ID;
					$css .= $this->css_styling($floating_query->post->ID);
					$content .= '<div id="afc_sidebar_'.$id_afc.'" class="afc_popup">';
					$close_btn = get_post_meta( $id_afc, 'ct_afc_close_button', true );
					if($close_btn=="yes"){							
						$content .='<a href="javascript:void()" class="afc_close_content"><img src="'.plugins_url( 'images/close.png', __FILE__ ).'" class="img" alt="" /></a>';
					}
					
					
					
					$content .= get_the_content();
					$content .='</div>';					
					//exit;
					
				endwhile;
			endif;
		//	echo $css;
			$output = '<style type="text/css">'.$css.'.afc_popup .img{position:absolute; top:-15px; right:-15px;}@media screen and (min-width:481px) and (max-width:768px){}@media only screen and (min-width: 321px) and (max-width: 480px) {'.$responsive_view_480.'.afc_popup{margin:0 !important;}.afc_popup iframe{width:100% !important;}}@media only screen and (max-width: 320px) {'.$responsive_view_320.'.afc_popup{margin:0 !important;}.afc_popup iframe{width:100% !important;}}</style>';
					$output .= $content;
					$output .="<script type='text/javascript'>
	(function ($) {
		$('.afc_close_content').click(function(){			
			var afc_content_id = $(this).closest('div').attr('id');
			$('#'+afc_content_id).hide();
		});
	})(jQuery);
</script>";
			
			echo $output;
			
			wp_reset_query();
	}
	
	
	public function css_styling($id_afc) {
		
		
					$position = get_post_meta( $id_afc, 'ct_afc_position_place', true );
					$position_y = get_post_meta( $id_afc, 'ct_afc_position_y', true );
					$position_x = get_post_meta( $id_afc, 'ct_afc_position_x', true );
					
					$width = get_post_meta( $id_afc, 'ct_afc_width', true );
					$width_unit = get_post_meta( $id_afc, 'ct_afc_width_unit', true );
					$background_color = get_post_meta( $id_afc, 'ct_afc_background_color', true );
					$margin = get_post_meta( $id_afc, 'ct_afc_margin_top', true ).'px '.get_post_meta( $id_afc, 'ct_afc_margin_right', true ).'px '.get_post_meta( $id_afc, 'ct_afc_margin_bottom', true ).'px '.get_post_meta( $id_afc, 'ct_afc_margin_left', true ).'px';
					$border_top = get_post_meta( $id_afc, 'ct_afc_border_top', true ).'px' . get_post_meta( $id_afc, 'ct_afc_border_type', true ). ' '.get_post_meta( $id_afc, 'ct_afc_border_color', true );
					$border_right = get_post_meta( $id_afc, 'ct_afc_border_right', true ).'px' . get_post_meta( $id_afc, 'ct_afc_border_type', true ). ' '.get_post_meta( $id_afc, 'ct_afc_border_color', true );
					$border_bottom = get_post_meta( $id_afc, 'ct_afc_border_bottom', true ).'px' . get_post_meta( $id_afc, 'ct_afc_border_type', true ). ' '.get_post_meta( $id_afc, 'ct_afc_border_color', true );
					$border_left = get_post_meta( $id_afc, 'ct_afc_border_left', true ).'px' . get_post_meta( $id_afc, 'ct_afc_border_type', true ). ' '.get_post_meta( $id_afc, 'ct_afc_border_color', true );
					$border_radius = get_post_meta( $id_afc, 'ct_afc_border_radius', true );
					
					
					$classes = "#afc_sidebar_".$id_afc."{"."background:".$background_color.";";			
					if($position=="fixed") {
					$classes .="position:fixed;";
					}
					if($position=="absolute") {
					$classes .="position:absolute;";
					}			
					if($position_y=="top") {
					$classes .="top:0px;";
					}
					if($position_y=="bottom") {
					$classes .="bottom:0px;";
					}
					if($position_x=="left") {
					$classes .="left:0px;";
					}
					if($position_x=="right") {
					$classes .="right:0px;";
					}
					$classes .="width:".$width.$width_unit.";";
					$classes .="margin:".$margin.';';
					
					
					if($border_radius==1) {
						$classes .="border-radius:".get_post_meta( $id_afc, 'ct_afc_border_top', true ).'px '. get_post_meta( $id_afc, 'ct_afc_border_right', true ).'px '.get_post_meta( $id_afc, 'ct_afc_border_bottom', true ).'px '.get_post_meta( $id_afc, 'ct_afc_border_left', true ).'px'.';';
						$classes .="-moz-border-radius:".get_post_meta( $id_afc, 'ct_afc_border_top', true ).'px '. get_post_meta( $id_afc, 'ct_afc_border_right', true ).'px '.get_post_meta( $id_afc, 'ct_afc_border_bottom', true ).'px '.get_post_meta( $id_afc, 'ct_afc_border_left', true ).'px'.';';
						$classes .="-webkit-border-radius: ".get_post_meta( $id_afc, 'ct_afc_border_top', true ).'px '. get_post_meta( $id_afc, 'ct_afc_border_right', true ).'px '.get_post_meta( $id_afc, 'ct_afc_border_bottom', true ).'px '.get_post_meta( $id_afc, 'ct_afc_border_left', true ).'px'.';';
					}
					$classes .="z-index:999999;";
					$classes .="padding:10px;";
					$classes .="color:#ffffff;";				
					$classes .="}"."\n";
					
					
					$responsive_view_320 .= "#afc_sidebar_".$id_afc."{";
					$responsive_view_320 .= "width:".(300-((get_post_meta( $id_afc, 'ct_afc_border_left', true )+get_post_meta( $id_afc, 'ct_afc_border_right', true ))*2))."px !important";
					$responsive_view_320 .="}"."\n";
					
					$responsive_view_480 .= "#afc_sidebar_".$id_afc."{";
					$responsive_view_480 .= "width:".(460-((get_post_meta( $id_afc, 'ct_afc_border_left', true )+get_post_meta( $id_afc, 'ct_afc_border_right', true ))*2))."px !important";
					$responsive_view_480 .="}";
					
					return $classes;
	}
}
