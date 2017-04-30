<?php

defined('ABSPATH') or die();

//require_once( 'wjecf-pro-evalmath.php' );

/**
 * Miscellaneous Pro functions
 */
class WJECF_Pro_Controller extends WJECF_Controller {

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
	
	public function __construct() {    
		parent::__construct();
		add_action('init', array( &$this, 'pro_controller_init' ));
	}

	public function pro_controller_init() {
		if ( ! class_exists('WC_Coupon') ) {
			return;
		}
        add_action( 'admin_init', array( &$this, 'admin_init' ) );

		//Coupon columns
		add_filter( 'manage_shop_coupon_posts_columns', array( $this, 'admin_shop_coupon_columns' ), 20, 1 );
		add_action( 'manage_shop_coupon_posts_custom_column', array( $this, 'admin_render_shop_coupon_columns' ), 2 );
		
		//Frontend hooks
		add_action('woocommerce_coupon_loaded', array( $this, 'woocommerce_coupon_loaded' ), 10, 1);
		add_filter('woocommerce_coupon_get_discount_amount', array( $this, 'woocommerce_coupon_get_discount_amount' ), 10, 5);
		add_action('wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 10);

	}

/* ADMIN HOOKS */
	public function admin_init() {	
		//Admin hooks
		add_action( 'woocommerce_coupon_options_usage_limit', array( $this, 'woocommerce_coupon_options_usage_limit' ), 10, 2 );
		add_action( 'wjecf_coupon_metabox_products', array( $this, 'wjecf_coupon_metabox_products' ), 11 );
		add_action( 'woocommerce_process_shop_coupon_meta', array( $this, 'process_shop_coupon_meta' ), 10, 2 );		
	}

//Admin

	public function wjecf_coupon_metabox_products() {
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

	//2.3.3-b3
	public function woocommerce_coupon_options_usage_limit() {
		global $thepostid, $post;
		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;

		echo '<div class="options_group wjecf_hide_on_fixed_cart_discount">';
		echo '<h3>' . __( 'Limit discount to', 'woocommerce-jos-autocoupon') . '</h3>';
		echo '<p>' . __( 'Here you can exclude certain products from being discounted (Only applies to Cart % Discount, Product Discount, Product % Discount)', 'woocommerce-jos-autocoupon') . '</p>';

		//2.3.1
		woocommerce_wp_select( array( 
			'id' => '_wjecf_apply_discount_to', 
			'label' => __( 'Limit discount to', 'woocommerce' ), 
			'options' => array( 
				'all' => __( '(default)', 'woocommerce-jos-autocoupon'), 
				'one_per_line' => __( 'One item per order line', 'woocommerce-jos-autocoupon'), 
				'cheapest_product' => __( 'Lowest priced product (single item)', 'woocommerce-jos-autocoupon'), 
				'cheapest_line' => __( 'Lowest priced order line (all items)', 'woocommerce-jos-autocoupon')
			),
			'description' => __( 'Please note that when the discount type is \'Product discount\' (see \'General\'-tab), the discount will only be applied to <em>matching</em> products.', 'woocommerce-jos-autocoupon' ),
			'desc_tip' => true
		) );

?>
<script>
	jQuery(function( $ ) {

		/**
		 * Coupon actions
		 */
		var wjecf_on_discount_type_change = {

			/**
			 * Initialize actions
			 */
			init: function() {
				$( 'select#discount_type' )
					.on( 'change', this.on_change )
					.change();
			},

			/**
			 * Show/hide fields by coupon type options
			 */
			on_change: function() {
				// Get value
				var select_val = $( this ).val();

				if ( select_val === 'fixed_cart' ) {
					$( '.wjecf_hide_on_fixed_cart_discount' ).hide();
				} else {
					$( '.wjecf_hide_on_fixed_cart_discount' ).show();
				}

				if ( select_val === 'fixed_product' || select_val === 'percent_product' ) {
					$( '.wjecf_hide_on_product_discount' ).hide();
				} else {
					$( '.wjecf_hide_on_product_discount' ).show();
				}

			}
		};

		wjecf_on_discount_type_change.init();
	});
</script>
<?php
        echo '</div>';

	}	

	public function process_shop_coupon_meta( $post_id, $post ) {
		//2.2.3
		$wjecf_allow_cart_excluded = isset( $_POST['_wjecf_allow_cart_excluded'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_wjecf_allow_cart_excluded', $wjecf_allow_cart_excluded );

		//2.3.1
		$wjecf_apply_discount_to = wc_clean( $_POST['_wjecf_apply_discount_to'] );
		update_post_meta( $post_id, '_wjecf_apply_discount_to', $wjecf_apply_discount_to );

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
		if ( ! is_admin() ) {
			//2.2.3 Allow coupon even if excluded products are not in the cart (required for Cart Discount with excluded products)
			if ( get_post_meta( $coupon->id, '_wjecf_allow_cart_excluded', true ) == 'yes' ) {
				//HACK: Overwrite the exclusions so WooCommerce will allow the coupon
				//These values are used in the WJECF_Controller->coupon_is_valid_for_product
				$this->overwrite_value( $coupon, 'exclude_product_ids', array() );
				$this->overwrite_value( $coupon, 'exclude_product_categories', array() );
				$this->overwrite_value( $coupon, 'exclude_sale_items', 'no' );
			}
		}
	}

	//2.3.1
	/**
	 * Hooked to the filter woocommerce_coupon_get_discount_amount.
	 * Will decrease discount if items are excluded.
	 * @param float $discount 
	 * @param float $discounting_amount 
	 * @param array $cart_item 
	 * @param bool $single 
	 * @param type $coupon 
	 * @return float
	 */
	public function woocommerce_coupon_get_discount_amount ( $discount, $discounting_amount, $cart_item, $single, $coupon ) {

		//Check for is_null because WC versions prior to 2.3.0 could pass null, 
		//also percent discount were handled differently and total values can be different
		if ( WJECF()->coupon_is_type( $coupon, array( 'fixed_cart' ) ) || is_null( $cart_item ) ) {
			return $discount;
		}

		if ( ! $this->coupon_is_valid_for_product( $coupon, $cart_item['data'], $cart_item ) ) {
			return 0;
		}		

		//echo $cart_item['data']->post->post_title . ' ' . $coupon->code . ' disc: ' .$discount. ' disc_am: ' .$discounting_amount. '<br>';
		// echo "<br>";

		//Number of discounted items on this order line ()
		$orig_discount_qty = $cart_item_qty = is_null( $cart_item ) ? 1 : $cart_item['quantity'];

		//FIX 2.3.3-b3: If limit_usage_to_x_items is 0 it may mean that not all items on the line were discounted
		//Using this trick we recalculate the original quantity of discounted items
		if ( $coupon->limit_usage_to_x_items === 0 ) {
			if (  WJECF()->coupon_is_type( $coupon, array( 'percent_product', 'percent' ) ) ) {
				$expected_discount = $coupon->coupon_amount * ( $discounting_amount / 100 );
				$orig_discount_qty *= $discount / $expected_discount; 
			} elseif ( WJECF()->coupon_is_type( $coupon, 'fixed_product' ) ) {
				$expected_discount = min( $coupon->coupon_amount, $discounting_amount );
				$expected_discount = $single ? $expected_discount : $expected_discount * $cart_item_qty;
				$orig_discount_qty *= $discount / $expected_discount;
			}
			$orig_discount_qty = round( $orig_discount_qty, 2 ); //just in case fractions are used in cart
		}
		$discount_qty = $orig_discount_qty;

		$apply_to = get_post_meta( $coupon->id, '_wjecf_apply_discount_to', true );
		switch ( $apply_to ) {
			default:
			case 'all': 
				//No limitation of discount
				break;
			case 'cheapest_product':
				if ( $cart_item == $this->get_cheapest_cart_item( WC()->cart->get_cart(), $coupon, false ) ) {
					$discount_qty = min(1, $discount_qty);
				} else {
					$discount_qty = 0;
				}
				break;
			case 'cheapest_line':
				if ( $cart_item == $this->get_cheapest_cart_item( WC()->cart->get_cart(), $coupon, true ) ) {
					//No limitation of discount on this line
				} else {
					$discount_qty = 0;
				}
				break;
			case 'one_per_line':
				$discount_qty = min(1, $discount_qty);
				break;
		}

		//Changed amount of items to apply discount to?
		if ( $discount_qty != $orig_discount_qty ) {
			$discount = $discount * $discount_qty / $orig_discount_qty;
			//Re-increase limit_usage_to_x_items
			if ( $coupon->limit_usage_to_x_items !== '' ) {
				$coupon->limit_usage_to_x_items += $orig_discount_qty - $discount_qty;
			}
		}

		//echo "<br>";
		// $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT ,5);
		// foreach ($bt as $bti)
		// 	echo $bti['function'] . '<br>';

		//print_r($cart_item);
		//echo ($single ? "single" : "multi" ). " " . $cart_item['data']->post->post_title . " ( " . $discount_qty . " / " . $orig_discount_qty . " / " . $cart_item_qty . ") = " . $discount . "<br>";

		// WC_ROUNDING_PRECISION does not exist in WC 2.6.3 (will be back in later versions)
		$discount = round( $discount, defined( 'WC_ROUNDING_PRECISION' ) ? WC_ROUNDING_PRECISION : wc_get_price_decimals() + 2 );

		return $discount;
	}

	/**
	 * Returns cheapest cart item from the cart
	 * @param WC_Cart $cart 
	 * @param WC_Coupon $coupon 
	 * @param bool $use_line_subtotal If true; use line subtotal. If false; use single item price
	 * @return array cart_item of the cheapest item in the cart. null if cart is empty
	 */
	public function get_cheapest_cart_item( $cart, $coupon, $use_line_subtotal ) {
		$cheapest_cart_item = null;
		$cheapest_price = 0;
		foreach ( $cart as $cart_item_key => $cart_item ) {
			if ( $this->coupon_is_valid_for_product( $coupon, $cart_item['data'], $cart_item ) ) {
				$_product = $cart_item['data'];			
				if ( ! WJECF()->coupon_is_type( $coupon, array( 'fixed_product', 'percent_product' ) ) || WJECF()->coupon_is_valid_for_product( $coupon, $_product, $cart_item ) ) {
					if ( $use_line_subtotal) {
						//line_subtotal might not be set before calculate_totals
						$price = isset( $cart_item['line_subtotal'] ) ? $cart_item['line_subtotal'] : $_product->get_price() * $cart_item['quantity'];;
					} else {
						$price = $_product->get_price();
					}

					if ( $price != 0 ) {
						if ( $cheapest_cart_item == null || $price < $cheapest_price ) {
							$cheapest_cart_item = $cart_item;
							$cheapest_price = $price;
						}
					}
				}
			}
		}
		return $cheapest_cart_item;
	}

	/**
	 * Include stylesheet
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'wjecf-style', plugins_url('assets/wjecf.css', dirname( __FILE__ ) ), array() ); 
	}


}
