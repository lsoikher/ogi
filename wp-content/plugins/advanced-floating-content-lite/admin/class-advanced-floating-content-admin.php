<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.codetides.com/
 * @since      1.1.0
 *
 * @package    Advanced_Floating_Content
 * @subpackage Advanced_Floating_Content/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Advanced_Floating_Content
 * @subpackage Advanced_Floating_Content/admin
 * @author     Code Tides <contact@codetides.com>
 */
class Advanced_Floating_Content_Admin {

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

    public $options;
    
    
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->options = get_option( 'ct_afc_options' );
	}

	/**
	 * Register the stylesheets for the admin area.
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
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/advanced-floating-content-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/advanced-floating-content-admin.js', array( 'wp-color-picker' ), $this->version, false );

	}
	
	/*
	*	Register CPT 
	*/
	
	public function register_cpt_floating_content()
	{
		$labels = array(
            'name'                => _x( 'Advanced Floating Content', 'Post Type General Name', 'advanced-floating-content' ),
            'singular_name'       => _x( 'Advanced Floating Content', 'Post Type Singular Name', 'advanced-floating-content' ),
            'menu_name'           => __( 'Advanced Floating Content', 'advanced-floating-content' ),
            'name_admin_bar'      => __( 'Advanced Floating Content', 'advanced-floating-content' ),
            'parent_item_colon'   => __( 'Parent Advanced Floating Content:', 'advanced-floating-content' ),
            'all_items'           => __( 'All Advanced Floating Content', 'advanced-floating-content' ),
            'add_new_item'        => __( 'Add New Advanced Floating Content', 'advanced-floating-content' ),
            'add_new'             => __( 'Add New', 'advanced-floating-content' ),
            'new_item'            => __( 'New Advanced Floating Content', 'advanced-floating-content' ),
            'edit_item'           => __( 'Edit Advanced Floating Content', 'advanced-floating-content' ),
            'update_item'         => __( 'Update Advanced Floating Content', 'advanced-floating-content' ),
            'view_item'           => __( 'View Advanced Floating Content', 'advanced-floating-content' ),
            'search_items'        => __( 'Search Advanced Floating Content', 'advanced-floating-content' ),
            'not_found'           => __( 'Not found', 'advanced-floating-content' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'advanced-floating-content' ),
        );
        $args = array(
            'label'               => __( 'Advanced Floating Content', 'advanced-floating-content' ),
            'description'         => __( 'Another Flexible Advanced Floating Content', 'advanced-floating-content' ),      
			'labels'              => $labels,     
            'supports'            => array('title','editor'),
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 10,
            'menu_icon'           => 'dashicons-admin-site',
            'show_in_admin_bar'   => false,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capability_type'     => 'post',
        );
        register_post_type( 'ct_afc', apply_filters( 'ct_afc_register_arguments', $args) );
        
	}
	
	/*
 	* Adds a options details.
	*/
	public function add_meta_box() {
		add_meta_box(
			'advanced_floating_content_meta_box',
			__( 'Floating Content Details', 'advanced-floating-content' ),
			array($this,'meta_box_print'),
			'ct_afc'
		);
	}
	/*
	* Prints the box content.
	*/
	public function meta_box_print( $post ) {
	
		require_once plugin_dir_path( __FILE__ ). 'views/advanced-floating-content-admin-display.php';
	}
	
	
	/*
 	* Adds a options details for premium users.
	*/
	public function add_meta_box_premium() {
		add_meta_box(
			'advanced_floating_content_premium_meta_box',
			__( 'Premium Options', 'advanced-floating-content' ),
			array($this,'meta_box_premium_print'),
			'ct_afc'
		);
	}
	/*
	* Prints the box content for premium users.
	*/
	public function meta_box_premium_print( $post ) {
	
		require_once plugin_dir_path( __FILE__ ). 'views/advanced-floating-content-premium-display.php';
	}
	
	
	/*
	*	Save the post content
	*/
	
	public function save_meta_box( $post_id ) {
 
    /* If we're not working with a 'post' post type or the user doesn't have permission to save,
     * then we exit the function.
     */
	 	
		if ( ! $this->is_valid_post_type() || ! $this->user_can_save( $post_id, 'advanced_floating_content_nonce', 'advanced_floating_content_save' ) ) {
			return;
		}	
		
		foreach($_POST as $key => $value)
		{
			if (0 === strpos($key, 'ct_afc_')) {
				update_post_meta( $post_id, $key, $value );
			}
		}
		
		
 
	}
	
	private function is_valid_post_type() {
		
		return ! empty( $_POST['post_type'] ) && 'ct_afc' == $_POST['post_type'];
	}
	
	private function user_can_save( $post_id, $nonce_action, $nonce_id ) {
 
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ $nonce_action ] ) && wp_verify_nonce( $_POST[ $nonce_action ], $nonce_id ) );
	 
		// Return true if the user is able to save; otherwise, false.
		return ! ( $is_autosave || $is_revision ) && $is_valid_nonce;
	 
	}
	
	/*
	* Hide quick edit in Fun Facts Pro the box content.
	*/
	public function replace_submit_meta_box() 
      {

          remove_meta_box('submitdiv', 'ct_afc', 'core'); // $item represents post_type
          add_meta_box('submitdiv', 'Advanced Floating Content' , array($this,'submit_meta_box'), 'ct_afc', 'side', 'low');
		  add_meta_box('ct_information', 'Code Tides' , array($this,'ct_meta_box'), 'ct_afc', 'side', 'low');
      }
	  
	  public function ct_meta_box()
	  {
			echo '<div class="ct_info" style="margin-left:-20px;"><iframe frameborder="0" width="300" height="900" src="http://www.codetides.com/free_plugin_right_side.php"></iframe></div>'; 
	   }
	  
	  
	 public function submit_meta_box() {
        global $action, $post;
       
        $post_type = $post->post_type; // get current post_type
        $post_type_object = get_post_type_object($post_type);
        $can_publish = current_user_can($post_type_object->cap->publish_posts);
       
        ?>
        <div class="submitbox" id="submitpost">
         <div id="major-publishing-actions">
         <?php
         do_action( 'post_submitbox_start' );
         ?>
         <div id="delete-action">
         <?php
         if ( current_user_can( "delete_post", $post->ID ) ) {
           if ( !EMPTY_TRASH_DAYS )
                $delete_text = __('Delete Permanently');
           else
                $delete_text = __('Move to Trash');
         ?>
         <a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo $delete_text; ?></a><?php
         } //if ?>
        </div>
         <div id="publishing-action">
         <span class="spinner"></span>
         <?php
         if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
              if ( $can_publish ) : ?>
                <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Add Tab') ?>" />
                <?php submit_button( sprintf( __( 'Add %' ), 'advanced-floating-content' ), 'primary button-large', 'publish', false, array( 'accesskey' => 'p' ) ); ?>
         <?php   
              endif; 
         } else { ?>
                <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update ') . $item; ?>" />
                <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php esc_attr_e('Update ') . 'advanced-floating-content'; ?>" />
         <?php
         } //if ?>
         </div>
         <div class="clear"></div>
         </div>
         </div>
        <?php
      }  	
    
    
    
    public function initialize_floating_content_options(){

        // create plugin options if not exist
        if( false == $this->options ) {
            add_option( 'ct_afc_options' );
            add_option('verified_purchase', '0');
            add_option('ct_afc_installed_date', current_time( 'timestamp' ));
        }
    }
    
    
    
	public function floating_content_admin_notice(){
        $installed_date = get_option( 'ct_afc_installed_date' );
        $checked_date  = strtotime('+1 month', current_time( 'timestamp' )); 
                
        if(current_time( 'timestamp' ) > $checked_date ){ 
            
        ?>
        <div class="error settings-error notice is-dismissible ">
        <div class="afc_banner">		

<div class="button_div">
    <a class="button" target="_blank" href="http://codecanyon.net/item/advanced-floating-content/9945856?ref=CodeTides">Buy now</a>
    
</div>		

<div class="text">You've been using <strong>Advanced Floating Content</strong> for a while now, and we hope you're happy with it. Why not upgrade to the <strong>PRO</strong> version?
    <br>
<span>Get it free support for any bugs or issues you faced regarding Advanced Floating Content</span>
</div>

	
</div>
            
    </div>
    <?php
        }
    }
}
