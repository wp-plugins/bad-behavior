<?php

// Analyze user-agent conformance to HTTP protocol.

if (!defined('WP_BB_CWD'))
	die('');

// Is it claiming to be HTTP/1.1?  Then it shouldn't do HTTP/1.0 things
if (!strcmp($wp_bb_server_protocol, "HTTP/1.1")) {
	if (array_key_exists('Pragma', $wp_bb_http_headers_mixed) && strpos($wp_bb_http_headers_mixed['Pragma'], 'no-cache') !== FALSE && !array_key_exists('Cache-Control', $wp_bb_http_headers_mixed)) {
		wp_bb_spammer("Header 'Pragma' without 'Cache-Control' prohibited for HTTP/1.1 requests");
	}
}

// Is it claiming to be HTTP/1.0?  Then it shouldn't do HTTP/1.1 things
if (!strcmp($wp_bb_server_protocol, "HTTP/1.0")) {
	if (array_key_exists('Expect', $wp_bb_http_headers_mixed) && strpos($wp_bb_http_headers_mixed['Expect'], '100-continue') !== FALSE) {
		wp_bb_spammer("Header 'Expect' prohibited in HTTP/1.0 requests");
		// TODO: return 417 for Expect: on HTTP/1.1?
	}
}

?>
