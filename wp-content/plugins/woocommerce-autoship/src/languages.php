<?php

function wc_autoship_load_textdomain() {
	load_plugin_textdomain( 'wc-autoship', false, dirname( plugin_basename( WC_AUTOSHIP_PLUGIN_FILE ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'wc_autoship_load_textdomain' );

function wc_autoship_get_language_msgids() {
	$po_file = fopen( WC_AUTOSHIP_PLUGIN_DIR . '/languages/wc-autoship-en_US.po', 'r' );
	if ( ! $po_file ) {
		return array();
	}

	$msgids = array();

	$line = fgets( $po_file );
	$in_msgid = false;
	$msgid_buffer = array();
	while ( false !== $line ) {
		if ( 0 === strpos( $line, 'msgid' ) ) {
			$in_msgid = true;
			$matches = null;
			preg_match( '/^msgid "(.+)"$/', $line, $matches );
			if ( $matches ) {
				$msgid_buffer[] = $matches[1];
			}
		} elseif ( $in_msgid && 0 === strpos( $line, '"' ) ) {
			$matches = null;
			preg_match( '/^"(.+)"$/', $line, $matches );
			if ( $matches ) {
				$msgid_buffer[] = $matches[1];
			}
		} else {
			$in_msgid = false;
			$msgid = implode( '', $msgid_buffer );
			if ( '' != $msgid ) {
				$msgids[] = $msgid;
			}
			$msgid_buffer = array();
		}
		$line = fgets( $po_file );
	}
	fclose( $po_file );

	return $msgids;
}