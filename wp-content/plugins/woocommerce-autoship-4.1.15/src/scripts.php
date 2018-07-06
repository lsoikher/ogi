<?php

function wc_autoship_enqueue_css() {
	wp_enqueue_style( 'pickaday', plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) . 'styles/pickaday.css', array(), WC_AUTOSHIP_VERSION );
	wp_enqueue_style( 'wc-autoship', plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) . 'styles/style.css', array(), WC_AUTOSHIP_VERSION );
}
add_action( 'wp_enqueue_scripts', 'wc_autoship_enqueue_css' );

function wc_autoship_enqueue_js() {
	wp_enqueue_script( 'wc-autoship-angular', plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) . 'js/angular.min.js', array(), WC_AUTOSHIP_VERSION );

	wp_register_script( 'wc-autoship-schedules', plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) . 'js/autoship-schedules.js', array(), WC_AUTOSHIP_VERSION );
	$msgids = wc_autoship_get_language_msgids();
	$schedules_messages = array();
	foreach ( $msgids as $id ) {
		$schedules_messages[$id] = __( $id, 'wc-autoship' );
	}
	wp_localize_script( 'wc-autoship-schedules', 'WC_AUTOSHIP_SCHEDULES_MESSAGES', $schedules_messages );
	wp_enqueue_script( 'wc-autoship-schedules' );

	wp_enqueue_script( 'moment', plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) . 'js/moment.js', array(), WC_AUTOSHIP_VERSION );
	wp_enqueue_script( 'pickaday', plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) . 'js/pickaday.js', array('moment'), WC_AUTOSHIP_VERSION );
	wp_enqueue_script( 'wc-autoship-datepicker', plugin_dir_url( WC_AUTOSHIP_PLUGIN_FILE ) . 'js/datepicker.js', array( 'pickaday', 'jquery' ), WC_AUTOSHIP_VERSION );
}
add_action( 'wp_enqueue_scripts', 'wc_autoship_enqueue_js' );

function wc_autoship_head_scripts() {
	echo "<script>\n// <![CDATA[\n";
	echo "var WC_AUTOSHIP_SITE_URL = ", json_encode( site_url('/') ), ";\n";
	echo "var WC_AUTOSHIP_AJAX_URL = ", json_encode( admin_url('/admin-ajax.php') ), ";\n";
	echo "// ]]>\n</script>\n";
}
add_action( 'wp_head', 'wc_autoship_head_scripts' );