<?php

// This file is included only when the user-agent is claiming to be MSIE
// (or some variant thereof).  We check the HTTP headers to see if it really
// is MSIE.

if (!defined('WP_BB_CWD'))
	die('');

require_once(WP_BB_CWD . "/bad-behavior-accept.php");

// MSIE always sends the Accept: header.
wp_bb_check_accept();

// MSIE 6.0+ always sends the Accept-Encoding: header.
// BecomeBot does not.
if (strpos($wp_bb_http_user_agent, "MSIE 6") !== FALSE) {
	if (stripos($wp_bb_http_user_agent, "BecomeBot") === FALSE ||
			matchCIDR($wp_bb_remote_addr, "64.124.85.0/24") === FALSE) {
		wp_bb_check_accept_encoding();
	}
}

// MSIE does NOT send "Windows ME" or "Windows XP" in the user agent
if (strpos($wp_bb_http_user_agent, "Windows ME") !== FALSE ||
    strpos($wp_bb_http_user_agent, "Windows XP") !== FALSE ||
    strpos($wp_bb_http_user_agent, "Windows 2000") !== FALSE) {
	wp_bb_spammer("User-Agent claimed to be MSIE, with invalid Windows version");
}

// MSIE does NOT send Connection: TE
if (preg_match('/\bTE\b/i', $wp_bb_http_headers_mixed['Connection'])) {
	wp_bb_spammer("Connection: TE present, not supported by MSIE");
}

?>
