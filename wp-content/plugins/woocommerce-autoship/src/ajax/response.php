<?php

function wc_autoship_ajax_set_response_code( $code ) {
	$message = '';
	if ( $code == 200 ) {
		$message = "OK";
	} elseif ( $code == 204 ) {
		$message = 'No Content';
	} elseif ( $code == 400 ) {
		$message = 'Bad Request';
	} elseif ( $code == 401 ) {
		$message = 'Unauthorized';
	} elseif ( $code == 403 ) {
		$message = 'Forbidden';
	} elseif ( $code == 404 ) {
		$message = 'Not Found';
	} elseif ( $code == 500 ) {
		$message = 'Error';
	}
	header( "HTTP/1.1 $code $message" );
}

function wc_autoship_ajax_result( $response_code, $data = NULL ) {
	wc_autoship_ajax_set_response_code( $response_code );
	if ( $data !== NULL ) {
		header( 'Content-Type: application/json; charset=utf-8' );
		$data = json_encode( $data );
		header( 'Content-Length: ' . strlen( $data ) );
		echo $data;
	}
	exit;
}