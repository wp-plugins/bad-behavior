<?php

// Check for various Accept: headers

function wp_bb_check_accept() {
	global $wp_bb_http_headers_mixed;
	if (!array_key_exists('Accept', $wp_bb_http_headers_mixed)) {
		wp_bb_spammer();
	}
}

function wp_bb_check_accept_encoding() {
	global $wp_bb_http_headers, $wp_bb_http_headers_mixed;

	// FIXME: skip this check; too many examples of legit user agents
	// are omitting Accept-Encoding; come back to it later when more
	// data is available
	return;

	// In some cases we can skip this check
	// HTTP proxies munch the Accept-Encoding header
	if (array_key_exists('X-BlueCoat-Via', $wp_bb_http_headers_mixed) ||
	    array_key_exists('X-Forwarded-For', $wp_bb_http_headers) ||
	    array_key_exists('Via', $wp_bb_http_headers)) {
		return;
	}

	// Check for Accept-Encoding
	if (!array_key_exists('Accept-Encoding', $wp_bb_http_headers_mixed) &&
	    !array_key_exists('~~~~~~~~~~~~~~~', $wp_bb_http_headers) &&
	    !array_key_exists('---------------', $wp_bb_http_headers) &&
	    !array_key_exists('XXXXXXXXXXXXXXX', $wp_bb_http_headers)) {
		wp_bb_spammer();
	}
}

?>
