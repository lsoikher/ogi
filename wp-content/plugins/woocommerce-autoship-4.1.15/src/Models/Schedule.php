<?php

require_once( 'DbEntity.php' );
require_once( 'ScheduleItem.php' );

class WC_Autoship_Models_Schedule extends WC_Autoship_Models_DbEntity implements JsonSerializable {
	/**
	 * Editable fields
	 * @var array
	 * @see WC_Autoship_DB_Entity::_editable_fields
	 */
	protected $_editable_fields = array(
		'customer_id' => false,
		'autoship_frequency' => false,
		'autoship_status' => false,
		'last_order_date' => false,
		'next_order_date' => false,
		'shipping_method_id' => false,
		'payment_token_id' => false,
		'coupon' => false,
	);
	
	/**
	 * Table name
	 * @var string
	 * @see WC_Autoship_DB_Entity::_table_name
	 */
	protected $_table_name = 'schedules';
	
	/**
	 * Schedule items
	 * @var array
	 */
	protected $_items;
	
	public function __construct( $id = NULL ) {
		parent::__construct( $id );
	}
	
	public function set_id( $id ) {
		parent::set_id( $id );
// 		$this->_items = NULL;
	}
	
	/**
	 * Get auto-ship frequency
	 * @return int
	 */
	public function get_autoship_frequency() {
		return intval( $this->get( 'autoship_frequency' ) );
	}
	
	/**
	 * Set auto-ship frequency
	 * @param int $autoship_frequency
	 */
	public function set_autoship_frequency( $autoship_frequency ) {
		$this->set( 'autoship_frequency', intval( $autoship_frequency ) );
	}
	
	/**
	 * Get auto-ship status
	 * @return int
	 */
	public function get_autoship_status() {
		return intval( $this->get( 'autoship_status' ) );
	}
	
	/**
	 * Set auto-ship status
	 * @param int $autoship_status
	 */
	public function set_autoship_status( $autoship_status ) {
		$this->set( 'autoship_status', intval( $autoship_status ) );
	}
	
	/**
	 * Get next order date
	 * @return string
	 */
	public function get_next_order_date() {
		return $this->get( 'next_order_date' );
	}
	
	/**
	 * Set next order date
	 * @param string $next_order_date
	 */
	public function set_next_order_date( $next_order_date ) {
		$this->set( 'next_order_date', $next_order_date );
	}
	
	/**
	 * Get last order date
	 * @return string
	 */
	public function get_last_order_date() {
		return $this->get( 'last_order_date' );
	}
	
	/**
	 * Set last order date
	 * @param string $last_order_date
	 */
	public function set_last_order_date( $last_order_date ) {
		$this->set( 'last_order_date', $last_order_date );
	}

	/**
	 * Get shipping method
	 * @return string
	 */
	public function get_shipping_method_id() {
		return $this->get( 'shipping_method_id' );
	}

	/**
	 * Set shipping method
	 * @param string $shipping_method_id
	 */
	public function set_shipping_method_id( $shipping_method_id ) {
		$this->set( 'shipping_method_id', $shipping_method_id );
	}

	/**
	 * Get coupon code
	 * @return string
	 */
	public function get_coupon_code() {
		return $this->get( 'coupon' );
	}

	public function set_coupon_code( $coupon_code ) {
		$this->set( 'coupon', $coupon_code );
	}
	
	/**
	 * Get coupon
	 * @return WC_Coupon
	 */
	public function get_coupon() {
		$coupon_code = $this->get_coupon_code();
		if ( ! empty( $coupon_code ) ) {
			//Make sure coupon hasn't been deleted
			$coupon = new WC_Coupon( $coupon_code );
			$coupon_id = method_exists( $coupon, 'get_id' ) ? $coupon->get_id() : $coupon->id;
			if ( $coupon_id > 0 ) {
				return $coupon;
			}
		}
		return null;
	}

	/**
	 * @return int
	 */
	public function get_coupon_expiration() {
		$coupon = $this->get_coupon();
		$coupon_id = method_exists( $coupon, 'get_id' ) ? $coupon->get_id() : $coupon->id;
		if ( ! empty( $coupon ) ) {
			// Expiration date
			$expiration_date = get_post_meta( $coupon_id, 'wc_autoship_expiration_date', true );
			if ( ! empty( $expiration_date ) ) {
				return strtotime( $expiration_date );
			}
		}
		return 0;
	}

	/**
	 * @return bool
	 */
	public function coupon_is_expired() {
		$coupon_expiration = $this->get_coupon_expiration();
		if ( $coupon_expiration > 0 ) {
			return time() > $coupon_expiration;
		}
		return false;
	}
	
	/**
	 * Set customer
	 * @param WP_User $customer
	 */
	public function set_customer( $customer ) {
		$this->set( 'customer_id', $customer->ID );
	}
	
	/**
	 * Get the customer for this schedule.
	 * @return WP_User
	 */
	public function get_customer() {
		$customer_id = $this->get( 'customer_id' );
		if ( $customer_id ) {
			$customer = new WP_User( $customer_id );
			if ( $customer->exists() ) {
				return $customer;
			}
		}
		return NULL;
	}
	
	/**
	 * Set customer ID
	 * @param int $customer_id
	 */
	public function set_customer_id( $customer_id ) {
		$this->set( 'customer_id', $customer_id );
	}
	
	/**
	 * Get customer ID
	 * @return int|NULL
	 */
	public function get_customer_id() {
		$customer_id = $this->get( 'customer_id' );
		if ( ! empty( $customer_id ) ) {
			$customer_id = (int) $customer_id;
		}
		return $customer_id;
	}

	/**
	 * @return string
	 */
	public function get_payment_token_id() {
		return $this->get( 'payment_token_id' );
	}

	/**
	 * @param string $token_id
	 */
	public function set_payment_token_id( $token_id ) {
		$this->set( 'payment_token_id', $token_id );
	}

	/**
	 * @return null|WC_Payment_Token
	 */
	public function get_payment_token() {
		return WC_Payment_Tokens::get( $this->get_payment_token_id() );
	}
	
	public function get_items() {
		if ( $this->_items == NULL ) {
			$this->load_items();
		}
		return $this->_items;
	}
	
	public function set_items( $items ) {
		foreach ( $items as $item ) {
			$item->set_schedule_id( $this->_id );
		}
		$this->_items = $items;
	}
	
	public function set_items_data( $items_data ) {
		if ( empty( $items_data ) ) {
			$this->_items = array();
			return $this->_items;
		}
		$items = array();
		foreach ( $items_data as $data ) {
			$item = new WC_Autoship_Models_ScheduleItem();
			$item->set_data( $data );
			$item->set_schedule_id( $this->_id );
			$items[] = $item;
		}
		$this->_items = $items;
		return $this->_items;
	}
	
	public function get_pending_autoship_items() {
		$items = $this->get_items();
		$pending_items = array();
		foreach ( $items as $item ) {
			if ( $item->is_pending_autoship() ) {
				$pending_items[] = $item;
			}
		}
		return $pending_items;
	}
	
	public function add_item(WC_Autoship_Models_ScheduleItem $item ) {
		if ( $this->_items == NULL ) {
			$this->load_items();
		}
		$item->set_schedule_id( $this->_id );
		$this->_items[] = $item;
	}
	
	public function add_item_data( $data ) {
		$item = new WC_Autoship_Models_ScheduleItem();
		$item->set_data( $data );
		$this->add_item( $item );
	}
	
	public function delete_item_by_id( $item_id ) {
		$items = $this->get_items();
		if ( $items == NULL ) {
			return;
		}
		$item_count = count( $items );
		for ( $i = 0; $i < $item_count; $i++ ) {
			if ( $item_id == $items[ $i ]->get_id() ) {
				$items[ $i ]->delete();
				unset( $items[ $i ] );
				$remaining_items = array_values( $items );
				$this->set_items( $remaining_items );
				return;
			}
		}
	}
	
	public function load_items() {
		global $wpdb;
	
		if ( $this->_id == NULL ) {
			return NULL;
		}
	
		$table_name = WC_Autoship_Models_ScheduleItem::get_full_table_name();
		$items_query = $wpdb->prepare(
			"SELECT *
			FROM {$table_name}
			WHERE schedule_id = %s",
			$this->_id
		);
		$items_data = $wpdb->get_results( $items_query, ARRAY_A );
		$this->set_items_data( $items_data );
	}
	
	public function get_fees() {
		$fees = apply_filters( 'wc_autoship_schedule_fees', array(), $this->get_id() );
		return $fees;
	}
	
	public function get_fees_total() {
		$total = 0.0;

		$fees = $this->get_fees();
		foreach ( $fees as $fee ) {
			$total += $fee->amount;
		}

		return $total;
	}
	
	public function get_items_subtotal() {
		$total = 0.0;

		foreach ( $this->get_items() as $item ) { /* @var $item WC_Autoship_Models_ScheduleItem */
			$total += $item->get_price() * $item->get_quantity();
		}

		return apply_filters( 'wc_autoship_schedule_items_subtotal', $total, $this->get_id() );
	}
	
	public function get_items_tax() {
		$tax = 0.0;

		foreach ( $this->get_items() as $item ) { /* @var $item WC_Autoship_Models_ScheduleItem */
			$tax += $item->get_tax();
		}

		return apply_filters( 'wc_autoship_schedule_items_tax', $tax, $this->get_id() );
	}
	
	public function get_discounts_total() {
		$discounts = 0.0;

		return apply_filters( 'wc_autoship_discount', $discounts, $this );
	}

	/**
	 * Get coupon discount total
	 * @return float
	 */
	public function get_coupon_discount_total() {
		// Autoship coupon
		$coupon = $this->get_coupon();
		$coupon_id = method_exists( $coupon, 'get_id' ) ? $coupon->get_id() : $coupon->id;
		if ( ! empty( $coupon ) ) {
			// Expiration date
			if ( ! $this->coupon_is_expired() ) {
				// Percent discount
				$percent_discount = get_post_meta( $coupon_id, 'wc_autoship_percent_discount', true );
				$percent_discount = apply_filters( 'wc_autoship_schedule_coupon_percent_discount', $percent_discount, $this );
				if ( ! empty( $percent_discount ) ) {
					$amount = ( (float) $percent_discount ) * $this->get_items_subtotal() / 100;
					return apply_filters( 'wc_autoship_schedule_coupon_discount', $amount, $this );
				}
			}
		}
		return 0.0;
	}
	
	public function get_tax_rates() {
		// Get customer
		$customer = $this->get_customer();
		
		// Calculate tax rates
		$tax = new WC_Tax();
		$tax_rates = apply_filters( 
			'wc_autoship_schedule_tax_rates', 
			$tax->find_rates( array(
				'country' => $customer->get( 'shipping_country' ),
				'state' => $customer->get( 'shipping_state' ),
				'city' => $customer->get( 'shipping_city' ),
				'postcode' => $customer->get( 'shipping_postcode' )
			) ), 
			$this->get_id()
		);
		return $tax_rates;
	}
	
	public function get_tax_total() {
		$tax_total = 0.0;

		if ( wc_tax_enabled() ) {
			$tax_rates = $this->get_tax_rates();
			$tax = new WC_Tax();
			$tax_total += $this->get_items_tax();
			$tax_total += $this->get_shipping_tax();
			$tax_total -= array_sum( $tax->calc_exclusive_tax( $this->get_coupon_discount_total(), $tax_rates ) );
			$tax_total -= array_sum( $tax->calc_exclusive_tax( $this->get_discounts_total(), $tax_rates ) );
		}

		return apply_filters( 'wc_autoship_schedule_tax_total', $tax_total, $this->get_id() );
	}
	
	public function get_shipping_total() {
		$shipping_total = 0.0;
		// Get shipping rate
		$shipping_rate = $this->get_shipping_rate();
		if ( ! empty( $shipping_rate ) ) {
			// Calculate cost
			$shipping_total = $shipping_rate->cost;
		}
		
		return apply_filters( 'wc_autoship_schedule_shipping_total', $shipping_total, $shipping_rate, $this->get_id() );
	}

	public function get_shipping_tax() {
		$shipping_tax = 0.0;
		if ( wc_tax_enabled() ) {
			// Get shipping rate
			$shipping_rate = $this->get_shipping_rate();
			if ( ! empty( $shipping_rate ) ) {
				// Calculate tax
				$shipping_tax = array_sum( $shipping_rate->taxes );
			}
		}

		return apply_filters( 'wc_autoship_schedule_shipping_tax', $shipping_tax, $shipping_rate, $this->get_id() );
	}

	public function get_shipping_rate() {
		// Get customer
		$customer = $this->get_customer();

		// Find shipping rate
		$shipping_rate = '';
		$shipping_methods = $this->get_shipping_methods();
		foreach ( $shipping_methods as $method ) {
			foreach ( $method->rates as $rate ) {
				if ( $rate->get_id() == $customer->get_shipping_method() ) {
					$shipping_rate = $rate;
					break 2;
				}
			}
		}

		return apply_filters( 'wc_autoship_schedule_shipping_rate', $shipping_rate, $this->get_id() );
	}

	public function get_total() {
		// Subtotal
		$total = $this->get_items_subtotal();
		// Shipping
		$total += $this->get_shipping_total();
		// Coupon discounts
		$total -= $this->get_coupon_discount_total();
		// Discounts
		$total -= $this->get_discounts_total();
		// Fees
		$total += $this->get_fees_total();
		// Taxes
		if ( wc_tax_enabled() ) {
			if ( ! wc_prices_include_tax() ) {
				$total += $this->get_items_tax();
			}
			$total += $this->get_shipping_tax();
		}
		// Return total
		return apply_filters( 'wc_autoship_schedule_total', $total, $this->get_id() );
	}
	
	/**
	 * Get products that are valid for this autoship schedule
	 * @return WC_Post[]
	 */
	public function get_available_products() {
		$products_query_args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => '_wc_autoship_enable_autoship',
					'value' => 'yes',
					'compare' => '='
				),
				array(
					'key' => '_wc_autoship_min_frequency',
					'value' => $this->get_autoship_frequency(),
					'compare' => '<=',
					'type' => 'NUMERIC'
				),
				array(
					'key' => '_wc_autoship_max_frequency',
					'value' => $this->get_autoship_frequency(),
					'compare' => '>=',
					'type' => 'NUMERIC'
				)
			),
			'orderby' => 'title',
			'order' => 'ASC'
		);
		$products_result = get_posts( $products_query_args );
		return $products_result;
	}
	
	public function delete() {
		do_action( 'wc_autoship_schedule_before_delete', $this->get_id() );
		$items = $this->get_items();
		foreach ( $items as $item ) {
			$item->delete();
		}
		$result = parent::delete();
		do_action( 'wc_autoship_schedule_delete', $result, $this->get_id() );
		return $result;
	}
	
	public function save( $additional_data = array() ) {
		global $wpdb;
		
		$affected_rows = parent::save();
		if ( false !== $affected_rows ) {
			if ( is_array( $this->_items ) ) {
				$items_table_name 
					= WC_Autoship_Models_ScheduleItem::get_full_table_name();
				$delete_items_query = $wpdb->prepare(
					"DELETE FROM {$items_table_name}
					WHERE schedule_id = %s",
					$this->_id
				);
				$wpdb->query( $delete_items_query );
				foreach ( $this->_items as $item ) {
					$item->set_schedule_id( $this->_id );
					//@todo add error handling
					$item->save();
				}
			}
		}
		return $affected_rows;
	}
	
	public static function get_pending_autoship_schedule_ids() {
		global $wpdb;
		require_once('ScheduleItem.php');
	
		$schedules_table = self::get_full_table_name();
		$items_table = WC_Autoship_Models_ScheduleItem::get_full_table_name();
		$query = $wpdb->prepare(
			"SELECT s.id
			FROM {$schedules_table} AS s
			WHERE s.autoship_status = %s
			AND s.next_order_date <= %s
			ORDER BY s.id ASC
			LIMIT 0, %d",
			WC_AUTOSHIP_STATUS_ACTIVE,
			current_time( 'Y-m-d' ),
			WC_AUTOSHIP_BATCH_SIZE
		);
		$schedule_ids = $wpdb->get_col( $query );
		return $schedule_ids;
	}

	public static function get_pending_autoship_schedules_count() {
		global $wpdb;

		$schedules_table = self::get_full_table_name();
		$query = $wpdb->prepare(
			"SELECT COUNT(*)
			FROM {$schedules_table} AS s
			WHERE s.autoship_status = %s
			AND s.next_order_date <= %s",
			WC_AUTOSHIP_STATUS_ACTIVE,
			current_time( 'Y-m-d' )
		);
		$count = $wpdb->get_var( $query );
		return $count;
	}
	
	/**
	 * Get the autoship schedules for a customer
	 * @param int $customer_id
	 * @return WC_Autoship_Models_Schedule[]
	 */
	public static function get_schedules_for_customer( $customer_id ) {
		global $wpdb;
	
		$table_name = self::get_full_table_name();
		$schedules_query = $wpdb->prepare(
			"SELECT id
			FROM {$table_name}
			WHERE customer_id = %s
			ORDER BY autoship_status DESC, next_order_date ASC",
			$customer_id
		);
		$schedule_ids = $wpdb->get_col( $schedules_query );
		$schedules = array(); 
		foreach( $schedule_ids as $id ) {
			$schedule = new WC_Autoship_Models_Schedule( $id );
			$schedules[] = $schedule;
		}
		return $schedules;
	}
	
	public static function get_schedules_shipping_in( $days ) {
		global $wpdb;
				
		$table_name = self::get_full_table_name();
		$schedules_query = $wpdb->prepare(
			"SELECT id
			FROM {$table_name}
			WHERE autoship_status > 0 AND next_order_date >= now() 
			AND DATEDIFF(next_order_date, now()) = %s
			ORDER BY next_order_date ASC",
			$days
		);
		$schedule_ids = $wpdb->get_col( $schedules_query );
		$schedules = array(); 
		foreach( $schedule_ids as $id ) {
			$schedule = new WC_Autoship_Models_Schedule( $id );
			$schedules[] = $schedule;
		}
		return $schedules;
	}

	public function jsonSerialize() {
		$data = $this->get_data();
		$data['items'] = $this->get_items();
		return $data;
	}
}