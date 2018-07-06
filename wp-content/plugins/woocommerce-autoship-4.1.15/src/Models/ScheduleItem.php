<?php

require_once('DbEntity.php');

class WC_Autoship_Models_ScheduleItem extends WC_Autoship_Models_DbEntity implements JsonSerializable {
	/**
	 * Editable fields
	 * @var array
	 * @see WC_Autoship_DB_Entity::_editable_fields
	 */
	protected $_editable_fields = array(
		'schedule_id' => false,
		'product_id' => false,
		'variation_id' => false,
		'qty' => false,
		'customer_label' => false
	);
	
	/**
	 * Table name
	 * @var string
	 * @see WC_Autoship_DB_Entity::_table_name
	 */
	protected $_table_name = 'schedule_items';
	
	public function __construct( $id = NULL ) {
		parent::__construct( $id );
	}
	
	public function get_customer_label_title() {
		$product_id = $this->get_product_id();
		if ( $product_id ) {
			return get_post_meta( 
				$product_id, 
				WC_Autoship_Models_DbEntity::PREFIX . 'customer_label_title',
				true 
			);
		}
		return '';
	}
	
	/**
	 * Get the product for this item.
	 * @return WC_Product_Simple
	 */
	public function get_product() {
		$product_id = $this->get_product_id();
		$variation_id = $this->get_variation_id();
		if ( ! empty( $variation_id ) && 'product_variation' === get_post_type( $variation_id ) ) {
			return wc_get_product( $variation_id );
		} elseif ( ! empty( $product_id  ) ) {
			return wc_get_product( $product_id );
		}

		return NULL;
	}
	
	public function get_product_id() {
		return @$this->_data['product_id'];
	}
	
	public function set_product_id( $product_id ) {
		$this->_data['product_id'] = $product_id;
	}
	
	public function get_variation_id() {
		return @$this->_data['variation_id'];
	}
	
	public function set_variation_id( $variation_id ) {
		$this->_data['variation_id'] = $variation_id;
	}
	
	public function get_quantity() {
		return @$this->_data['qty'];
	}
	
	public function set_quantity( $quantity ) {
		$this->_data['qty'] = $quantity;
	}

	/**
	 * Get the autoship price
	 * @return float
	 */
	public function get_autoship_price() {
		global $wpdb;
		
		$autoship_frequency = 0;
		$schedule_id = $this->get_schedule_id();
		if ( $schedule_id != NULL ) {
			$autoship_frequency = $wpdb->get_var( $wpdb->prepare(
				"SELECT autoship_frequency
				FROM {$wpdb->prefix}wc_autoship_schedules
				WHERE id = %d",
				$schedule_id
			) );
		}
		
		$product_id = $this->get_product_id();
		$variation_id = $this->get_variation_id();
		$price_product_id = empty( $variation_id ) ? $product_id : $variation_id;
		$schedule = $this->get_schedule();
		$customer_id = ( $schedule != NULL ) ? $schedule->get_customer_id() : 0;
		$autoship_price = apply_filters( 'wc_autoship_price',
			get_post_meta( $price_product_id, '_wc_autoship_price', true ),
			$price_product_id,
			$autoship_frequency,
			$customer_id,
			$this->get_id()
		);
		return (float) $autoship_price;
	}

	/**
	 * Get the price of the item. If autoship_price has been set, return autoship_price.
	 * @return float
	 */
	public function get_price() {
		$price = $this->get_autoship_price();
		if ( empty( $price ) ) {
			$product = $this->get_product();
			if ( $product != null ) {
				$price = $product->get_price();
			} else {
				$price = 0.0;
			}
		}
		// Autoship line discount
		// Deprecated 3.2.0, use wc_autoship_schedule_item_discount
		$autoship_line_discount = apply_filters( 'wc_autoship_line_discount',
			0.0,
			$this->get_id(),
			$price
		);
		$price -= ( $autoship_line_discount / $this->get_quantity() );
		// Autoship schedule item discount
		$item_discount = apply_filters( 'wc_autoship_schedule_item_discount',
			0.0,
			$this->get_id(),
			$price
		);
		$price -= $item_discount;
		
		// Return price
		return apply_filters( 'wc_autoship_schedule_item_price', (float) $price, $this->get_id() );
	}

	/**
	 * Get the tax of the item
	 * @return float
	 */
	public function get_tax() {
		$tax = 0.0;
		$product = $this->get_product();
		if ( $product != null ) {
			$price = $this->get_price();
			$quantity = $this->get_quantity();
			$tax = $product->get_price_including_tax( $quantity, $price ) - $product->get_price_excluding_tax( $quantity, $price );
		}
		return apply_filters( 'wc_autoship_schedule_item_tax', $tax, $this->get_id() );
	}
	
	/**
	 * Get autoship options for a product.
	 * @return array|NULL
	 * @deprecated
	 */
	public function get_product_autoship_options() {
		$product_id = $this->get_product_id();
		if ( $product_id ) {
			$options_key = WC_Autoship_Models_DbEntity::PREFIX . 'options';
			$options = get_post_meta( $product_id, $options_key, true );
			return $options;
		}
		return NULL;
	}
	
	/**
	 * Get the parent schedule object.
	 * @return WC_Autoship_Models_Schedule|NULL
	 */
	public function get_schedule() {
		$schedule_id = $this->get_schedule_id();
		if ( $schedule_id ) {
			require_once('Schedule.php');
			return new WC_Autoship_Models_Schedule( $schedule_id );
		}
		return NULL;
	}
	
	public function get_schedule_id() {
		return @$this->_data['schedule_id'];
	}
	
	public function set_schedule_id( $schedule_id ) {
		$this->_data['schedule_id'] = $schedule_id;
	}

	public function get_min_frequency() {
		$product_id = $this->get_product_id();
		$frequency = get_post_meta( $product_id, '_wc_autoship_min_frequency', true );
		if ( empty ( $frequency ) ) {
			return WC_AUTOSHIP_MIN_FREQUENCY;
		}
		return (int) $frequency;
	}

	public function get_max_frequency() {
		$product_id = $this->get_product_id();
		$frequency = get_post_meta( $product_id, '_wc_autoship_max_frequency', true );
		if ( empty ( $frequency ) ) {
			return WC_AUTOSHIP_MAX_FREQUENCY;
		}
		return (int) $frequency;
	}
	
	public function is_pending_autoship() {
		if ( ! $this->get( 'autoship_status' ) == WC_AUTOSHIP_STATUS_ACTIVE ) {
			return false;
		}
		$next_order_date = $this->get( 'next_order_date' );
		if ( ! $next_order_date ) {
			return false;
		}
		return ( time() > strtotime( $next_order_date ) );
	}

	public function jsonSerialize() {
		$data = $this->get_data();
		$data['price'] = 0.0;
		$data['qty'] = (int) $data['qty'];
		$product = $this->get_product();
		if ( ! empty( $product ) ) {
			$data['product_price'] = (float) $product->get_price();
			$data['product_sale_price'] = (float) $product->get_sale_price();
			$data['product_autoship_price'] = (float) $this->get_autoship_price();
			if ( ! empty( $data['product_autoship_price'] ) ) {
				$data['price'] = $data['product_autoship_price'];
			} else {
				$data['price'] = $data['product_price'];
			}
			if ( $product->is_type( 'variation' ) ) {
				$product_title = wc_autoship_get_product_display_name( $product );
				$data['product_title'] = html_entity_decode( strip_tags( str_replace( '><', ' > <', $product_title . ' ' ) ) );
			} else {
				$product_title = wc_autoship_get_product_display_name( $product );
				$data['product_title'] = $product_title;
			}
			$data['product_meta'] = $product->get_attributes();
			$data['product_url'] = $product->get_permalink();
			$thumbnail_id = get_post_thumbnail_id( $product->get_id() );
			$attachment_image_array = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail', false );
			if ( ! empty( $attachment_image_array ) ) {
				$data['product_thumbnail'] = $attachment_image_array[0];
			} else {
				$data['product_thumbnail'] = wc_placeholder_img_src();
			}
		}
		$data['line_price_formatted'] = wc_autoship_format_currency( $data['price'] * $this->get_quantity() );
		return $data;
	}
}