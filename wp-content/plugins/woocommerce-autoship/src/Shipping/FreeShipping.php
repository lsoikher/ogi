<?php

class WC_Autoship_Shipping_FreeShipping extends WC_Shipping_Method {

	public function __construct( $instance_id = 0 ) {
		$this->id = 'wc_autoship_free_shipping';
		$this->instance_id = absint( $instance_id );
		$this->method_title = __( 'Autoship Free Shipping (No edit)', 'wc-autoship' );
		$this->title = $this->method_title;
		$this->method_description = __( 'Free shipping for Autoship orders.', 'wc-autoship' );
		$this->enabled = 'yes';
		$this->supports = array(
			'shipping-zones'
		);
	}

	public function calculate_shipping( $package = array() ) {
		$rate = array(
			'label' => __( "Free Shipping", 'wc-autoship' ),
			'cost' => 0.0,
			'taxes' => false,
			'package' => $package
		);
		$this->add_rate( $rate );
	}

	public function is_available( $package ) {
		return true;
	}

}