<?php

function wc_autoship_product_autoship_tab( $tabs ) {
	$tabs['autoship'] = array(
		'label'  => __( 'Autoship', 'wc-autoship' ),
		'target' => 'autoship_product_data',
		'class'  => array( 'show_if_simple', 'show_if_variable', 'hide_if_grouped' )
	);

	return $tabs;
}
add_action( 'woocommerce_product_data_tabs', 'wc_autoship_product_autoship_tab', 10, 1 );

function wc_autoship_product_autoship_tab_content() {
	?>
	<div id="autoship_product_data" class="panel woocommerce_options_panel">
		<div class="options_group show_if_simple show_if_variable hide_if_grouped">
			<?php do_action( 'woocommerce_product_options_autoship_product_data' ); ?>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_product_data_panels', 'wc_autoship_product_autoship_tab_content' );

function wc_autoship_print_product_custom_fields() {

	echo '<div class="options_group">';

	// Enable autoship
	woocommerce_wp_checkbox(
		array(
			'id' => '_wc_autoship_enable_autoship',
			'label' => __( 'Enable Autoship', 'wc-autoship' )
		)
	);

	// Autoship price
	echo '<div class="show_if_simple hide_if_variable">';
	woocommerce_wp_text_input( array(
		'id' => '_wc_autoship_price',
		'label' => __( 'Autoship Price (' . get_woocommerce_currency_symbol() . ')', 'wc-autoship' ),
		'placeholder' => __( '(Optional)', 'wc-autoship' ),
		'desc_tip'  => true,
		'description' => __( 'The price of the product for recurring autoship orders.', 'wc-autoship' ),
		'data_type' => 'price'
	) );
	echo '</div>';

	$days = array();
	for ( $d = WC_AUTOSHIP_MIN_FREQUENCY; $d <= WC_AUTOSHIP_MAX_FREQUENCY; $d++ ) {
		$days[ $d ] = __( ( $d > 1 ) ? "$d days" : "$d day", 'wc-autoship' );
	}

	// Minimum autoship frequency
	woocommerce_wp_select( array(
		'id' => '_wc_autoship_min_frequency',
		'label' => __( 'Min Autoship Frequency', 'wc-autoship' ),
		'options' => $days,
		'desc_tip'  => true,
		'description' => __( 'Select the minimum number of days in which an autoship can occur.', 'wc-autoship' )
	) );

	// Maximum autoship frequency
	woocommerce_wp_select( array(
		'id' => '_wc_autoship_max_frequency',
		'label' => __( 'Max Autoship Frequency', 'wc-autoship' ),
		'options' => $days,
		'desc_tip'  => true,
		'description' => __( 'Select the maximum number of days in which an autoship can occur.', 'wc-autoship' )
	) );

	// Default autoship frequency
	$default_days = array( '' => '' );
	for ( $d = WC_AUTOSHIP_MIN_FREQUENCY; $d <= WC_AUTOSHIP_MAX_FREQUENCY; $d++ ) {
		$default_days[ $d ] = __( ( $d > 1 ) ? "$d days" : "$d day", 'wc-autoship' );
	}
	woocommerce_wp_select( array(
		'id' => '_wc_autoship_default_frequency',
		'label' => __( 'Default Autoship Frequency', 'wc-autoship' ),
		'options' => $default_days,
		'desc_tip'  => true,
		'description' => __( 'Select the default autoship frequency', 'wc-autoship' )
	) );

	echo '</div>';

}
add_action( 'woocommerce_product_options_autoship_product_data', 'wc_autoship_print_product_custom_fields' );

function wc_autoship_print_variable_product_custom_fields( $loop, $variation_data, $variation ) {
	?>

	<!-- Autoship price -->
	<?php $autoship_price = get_post_meta( $variation->ID, '_wc_autoship_price', true ); ?>
	<div class="variable_pricing">
		<p class="form-row form-row-full">
			<label for="wc_autoship_variable_price_<?php echo $loop; ?>"><?php echo __( 'Autoship Price:', 'wc-autoship' ) . ' (' . get_woocommerce_currency_symbol() . ')'; ?></label>
			<input type="text" size="5" id="wc_autoship_variable_price_<?php echo $loop; ?>" name="wc_autoship_variable_price[<?php echo $loop; ?>]" value="<?php echo esc_attr( $autoship_price ); ?>" class="wc_input_price" placeholder="<?php _e( '(optional)', 'wc-autoship' ); ?>" />
		</p>
	</div>

	<?php
}
add_action( 'woocommerce_product_after_variable_attributes', 'wc_autoship_print_variable_product_custom_fields', 10, 3 );

function wc_autoship_save_product_custom_fields( $post_id ) {
	$autoship_field_names = array(
		'_wc_autoship_enable_autoship',
		'_wc_autoship_price',
		'_wc_autoship_min_frequency',
		'_wc_autoship_max_frequency',
		'_wc_autoship_default_frequency'
	);
	foreach ( $autoship_field_names as $name ) {
		$value = isset( $_POST[ $name ] ) ? $_POST[ $name ] : '';
		update_post_meta( $post_id, $name, $value );
	}
}
add_action( 'woocommerce_process_product_meta', 'wc_autoship_save_product_custom_fields', 10, 1 );

function wc_autoship_save_product_variations( $product_id ) {
	if ( ! empty( $_POST['variable_post_id'] ) ) {
		$autoship_variable_field_names = array(
			'wc_autoship_variable_price' => '_wc_autoship_price'
		);
		$variable_post_id = $_POST['variable_post_id'];
		$max_variation_loop = max( array_keys( $variable_post_id ) );
		for ( $i = 0; $i <= $max_variation_loop; $i++ ) {
			if ( ! isset( $variable_post_id[ $i ] ) ) {
				continue;
			}

			$variation_id = absint( $variable_post_id[ $i ] );
			foreach ( $autoship_variable_field_names as $name => $meta_key ) {
				$value = isset( $_POST[ $name ][ $i ] ) ? $_POST[ $name ][ $i ] : '';
				update_post_meta( $variation_id, $meta_key, $value );
			}
		}
	}
}
add_action( 'woocommerce_ajax_save_product_variations', 'wc_autoship_save_product_variations', 10, 1 );

function wc_autoship_product_variable_scripts() {
	// Product
	global $post;
	if ( ! empty( $post ) && $post->post_type == 'product' ) {
		$product = wc_get_product( $post );
		if ( ! empty( $product ) && $product->is_type( 'variable' ) ) {
			wp_register_script( 'autoship-product-variable', plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) . 'js/product/variable.js', array( 'wc-add-to-cart-variation' ), WC_AUTOSHIP_VERSION, true );
			$variations = $product->get_available_variations();
			foreach ( $variations as $v => $variation ) {
				// Get autoship price
				$autoship_price = apply_filters( 'wc_autoship_price',
					get_post_meta( $variation['variation_id'], '_wc_autoship_price', true ),
					$variation['variation_id'],
					0,
					get_current_user_id(),
					0
				);
				// Filter template autoship price
				$autoship_price = apply_filters( 'wc_autoship_product_autoship_options_variable_price',
					$autoship_price,
					$variation['variation_id'],
					get_current_user_id()
				);
				$variations[ $v ][ 'autoship_price' ] = $autoship_price;
				$variations[ $v ][ 'autoship_price_formatted' ] = wc_price( $autoship_price );
			}
			wp_localize_script( 'autoship-product-variable', 'AUTOSHIP_PRODUCT_VARIABLE', array(
				'variations' => $variations,
				'currency_symbol' => get_woocommerce_currency_symbol()
			) );
			wp_enqueue_script( 'autoship-product-variable' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'wc_autoship_product_variable_scripts' );

function wc_autoship_get_product_display_name( WC_Product $product ) {
	$display_name = '';
	if ( null == $product ) {
		$display_name = '';
	} elseif ( method_exists( $product, 'get_name' ) ) {
		$display_name = $product->get_name();
	} else {
		$display_name = $product->get_title();
	}
	if ( $product->is_type( 'variation' ) ) {
		if ( version_compare( WC()->version, '3.0', '<' ) ) {
			// WC 2.6
			$display_name .= ' (' . trim( strip_tags( str_replace( '><', ' > <', str_replace( '</dd><dt>', ', ', $product->get_formatted_variation_attributes() ) ) ) ) . ')';
		} else {
			// WC 3.0
			$display_name .= ' (' . wc_get_formatted_variation( $product, true, true ) . ')';
		}
	}
	return apply_filters( 'wc_autoship_product_display_name', $display_name, $product->get_id() );
}