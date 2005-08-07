<?php

if (!defined('WP_BB_CWD'))
	die('');

// Analyze the Referer: field

function wp_bb_referer() {
	global $wp_bb_http_headers_mixed;

	// Referer, if it exists, must not be blank
	if (empty($wp_bb_http_headers_mixed['Referer'])) {
		wp_bb_spammer("Header 'Referer' present but blank");
	}

	// Referer, if it exists, must contain a :
	// While a relative URL is technically valid in Referer, all known
	// legit user-agents send an absolute URL
	if (strpos($wp_bb_http_headers_mixed['Referer'], ":") === FALSE) {
		wp_bb_spammer("Header 'Referer' is corrupt");
	}
	return;
}

?>
