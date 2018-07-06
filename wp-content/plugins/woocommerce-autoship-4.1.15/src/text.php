<?php

function wc_autoship_format_currency( $amount ) {
	return wc_autoship_clean_price( wc_price( $amount ) );
}

function wc_autoship_clean_price( $price ) {
	return html_entity_decode( strip_tags( $price ) );
}