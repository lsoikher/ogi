<?php

defined('ABSPATH') or die();

//require_once( 'wjecf-pro-evalmath.php' );

/**
 * Miscellaneous Pro functions
 */
class WJECF_Pro_Controller extends WJECF_Controller {

    public function __construct() {    
        parent::__construct();
    }

    /**
     * Singleton Instance
     *
     * @static
     * @return Singleton Instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    protected static $_instance = null;    

    public function start() {
        parent::start();
        add_action('init', array( &$this, 'pro_controller_init' ));
    }

    public function pro_controller_init() {
        if ( ! class_exists('WC_Coupon') ) {
            return;
        }
        add_action( 'admin_init', array( $this, 'admin_init' ) );

        //Coupon columns
        add_filter( 'manage_shop_coupon_posts_columns', array( $this, 'admin_shop_coupon_columns' ), 20, 1 );
        add_action( 'manage_shop_coupon_posts_custom_column', array( $this, 'admin_render_shop_coupon_columns' ), 2 );
        
        //Frontend hooks
        add_action('woocommerce_coupon_loaded', array( $this, 'woocommerce_coupon_loaded' ), 10, 1);
        add_action('wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 10);

    }

/* ADMIN HOOKS */
    public function admin_init() {    
        //Admin hooks

        //Removed in 2.5.0: add_action( 'wjecf_coupon_metabox_products', array( $this, 'wjecf_coupon_metabox_products' ), 11 );
        //Moved here:
        add_action('woocommerce_coupon_options_usage_restriction', array( $this, 'on_woocommerce_coupon_options_usage_restriction' ), 20, 1);
    }

//Admin

    // //Tab 'extended features'
    // public function wjecf_coupon_metabox_products() {
    
    //since 2.5.0 moved to the 'Usage restriction' tab
    public function on_woocommerce_coupon_options_usage_restriction() {
        global $thepostid, $post;
        $thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
        echo '<div class="options_group wjecf_hide_on_product_discount">';
        echo '<h3>' . __( 'Discount on cart with excluded products', 'woocommerce-jos-autocoupon') . '</h3>';

        //=============================
        //2.2.3 Allow even if excluded items in cart
        woocommerce_wp_checkbox( array(
            'id'          => '_wjecf_allow_cart_excluded',
            'label'       => __( 'Allow discount on cart with excluded items', 'woocommerce-jos-autocoupon' ),
            'description' => __( 'Check this box to allow a \'Cart Discount\' coupon to be applied even when excluded items are in the cart (see tab \'usage restriction\').', 'woocommerce-jos-autocoupon' ),
        ) );    
        echo '</div>';
    }

    public function admin_coupon_meta_fields( $coupon ) {
        //$fields = parent::admin_coupon_meta_fields();
        return array(
            //2.2.3
            '_wjecf_allow_cart_excluded' => 'yesno'
        );
    }

    private $inject_coupon_columns = array();
    /**
     * Inject custom columns on the Coupon Admin Page
     *
     * @param string $column_key The key to identify the column
     * @param string $caption The title to show in the header
     * @param callback $callback The function to call when rendering the column value ( Will be called with parameters $column_key, $post )
     * @param string $after_column Optional, The key of the column after which the column should be injected, if omitted the column will be placed at the end
     */
    public function inject_coupon_column( $column_key, $caption, $callback, $after_column = null ) {
        $this->inject_coupon_columns[ $column_key ] = array('caption' => $caption, 'callback' => $callback, 'after' => $after_column);
    }

    /**
     * Custom columns on coupon admin page
     *
     * @param array $columns
     */
    public function admin_shop_coupon_columns( $columns ) {
        $new_columns = array();
        foreach( $columns as $key => $column ) {
            $new_columns[$key] = $column;
            foreach( $this->inject_coupon_columns as $inject_key => $inject_column ) {
                if ( $inject_column['after'] == $key ) {
                    $new_columns[$inject_key] = $inject_column['caption'];
                }
            }
        }
        foreach( $this->inject_coupon_columns as $inject_key => $inject_column ) {
            if ( $inject_column['after'] == null || ! isset( $columns[ $inject_column['after'] ] ) ) {
                $new_columns[$inject_key] = $inject_column['caption'];
            }
        }
        return $new_columns;
    }

    /**
     * Output custom columns for coupons
     *
     * @param string $column
     */
    public function admin_render_shop_coupon_columns( $column ) {
        global $post;
        if ( isset( $this->inject_coupon_columns[$column]['callback'] ) ) {
            call_user_func( $this->inject_coupon_columns[$column]['callback'], $column, $post );
        }
    } 

//Frontend

    public function woocommerce_coupon_loaded ( $coupon ) {
        if ( $this->allow_overwrite_coupon_values() ) {
            $wrap_coupon = WJECF_Wrap( $coupon );
            //2.2.3 Allow coupon even if excluded products are not in the cart 
            //This way we can use the subtotal/quantity of matching products for a cart discount
            $allow_cart_excluded = $wrap_coupon->get_meta( '_wjecf_allow_cart_excluded' ) == 'yes';
            if ( $allow_cart_excluded && $wrap_coupon->is_type( WJECF_WC()->wc_get_cart_coupon_types() ) ) {
                //HACK: Overwrite the exclusions so WooCommerce will allow the coupon
                //These values are used in the WJECF_Controller->coupon_is_valid_for_product
                $wrap_coupon->set_excluded_product_ids( array() );
                $wrap_coupon->set_excluded_product_categories( array() );
                $wrap_coupon->set_exclude_sale_items( false );
            }
        }
    }


    /**
     * Include stylesheet
     */
    public function wp_enqueue_scripts() {
        wp_enqueue_style( 'wjecf-style', WJECF()->plugin_url( 'assets/wjecf.css' ), array(), WJECF()->plugin_version() ); 
    }


}
