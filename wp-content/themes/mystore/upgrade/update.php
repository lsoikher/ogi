<?php

/**
 * Add some custom Kaira update information to the update_themes transient.
 *
 * This ONLY applies when the user enters a valid premium order number. A user should be aware that the updates will be
 * coming from a different source after they upgrade to the premium version.
 *
 */

/*******************Child Theme******************
//Use this section to provide updates for a child theme
//If using on child theme be sure to prefix all functions properly to avoid
//function exists errors
if(function_exists('wp_get_theme')){
    $theme_data = wp_get_theme(get_option('stylesheet'));
    $theme_version = $theme_data->Version;  
} else {
    $theme_data = wp_get_theme(get_option('stylesheet'));
    $theme_version = $theme_data['Version'];
}    
$theme_base = get_option('stylesheet');
**************************************************/

/***********************Parent Theme**************/
if ( function_exists( 'wp_get_theme' ) ) {
    $theme_data = wp_get_theme( get_option( 'template' ) );
    $theme_version = $theme_data->Version;  
} else {
    $theme_data = wp_get_theme( get_option( 'stylesheet' ) );
    $theme_version = $theme_data['Version'];
}    
$theme_base = get_option( 'template' );
/**************************************************/

//Uncomment below to find the theme slug that will need to be setup on the api server
//var_dump($theme_base);

function mystore_theme_update_filter( $checked_data ) {
	global $wp_version, $theme_version, $theme_base, $api_url;
	
	$theme = basename( get_template_directory() ); // = mystore
	$order_number = get_theme_mod( $theme . '_user_order_number' );
	
	if ( empty( $order_number ) ) return $checked_data; // Skip if the user has not entered an order number.
	
	$request = array(
		'slug' => $theme_base,
		'version' => $theme_version 
	);
	// Start checking for an update
	$send_for_check = array(
		'body' => array(
			'action' => 'theme_update', 
			'order_number' => $order_number,
			'theme' => $theme,
			'request' => serialize( $request ),
			'api-key' => md5( home_url( '/' ) )
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' )
	);
	
	$raw_response = wp_remote_post( MYSTORE_UPDATE_URL . '/premium/' . $theme . '/' , $send_for_check );
	
	if ( !is_wp_error( $raw_response ) && ( $raw_response['response']['code'] == 200 ) )
		$response = unserialize( $raw_response['body'] );

	// Feed the update data into WP updater
	if ( !empty( $response ) ) 
		$checked_data->response[$theme_base] = $response;

	return $checked_data;
}
add_filter( 'pre_set_site_transient_update_themes', 'mystore_theme_update_filter' );


// Take over the Theme info screen on WP multisite
add_filter( 'themes_api', 'mystore_api_call', 10, 3 );

function mystore_api_call( $def, $action, $args ) {
	global $theme_base, $api_url, $theme_version, $api_url;

	if ( $args->slug != $theme_base )
		return false;

	// Get the current version

	$args->version = $theme_version;
	$request_string = wp_parse_args( $args ); // prepare_request( $action, $args );
	$request = wp_remote_post( MYSTORE_UPDATE_URL . '/premium/' . $theme . '/', $request_string );

	if ( is_wp_error( $request ) ) {
		$res = new WP_Error( 'themes_api_failed', __( 'An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>', 'mystore' ), $request->get_error_message() );
	} else {
		$res = unserialize( $request['body'] );

		if ( $res === false )
			$res = new WP_Error( 'themes_api_failed', __( 'An unknown error occurred', 'mystore' ), $request['body'] );
	}

	return $res;
}

if ( is_admin() )
	$current = get_transient( 'update_themes' );
?>