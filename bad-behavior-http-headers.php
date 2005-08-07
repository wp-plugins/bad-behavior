<?php

if (!defined('WP_BB_CWD'))
	die('');

// Analyze the headers of all user-agents
// These checks apply to any user-agent regardless of identification

// Range: field exists and begins with 0
// Real user-agents do not start ranges at 0
// NOTE: this blocks the whois.sc bot. No big loss.
// FIXME: whitelist whois.sc netblocks when rwhoisd.ccom.net becomes available
if (array_key_exists('Range', $wp_bb_http_headers_mixed) &&
		strpos($wp_bb_http_headers_mixed['Range'], "=0-") !== FALSE) {
	if (strncmp($wp_bb_http_user_agent, "MovableType", 11))
		wp_bb_spammer("Prohibited header 'Range' present");
}

// Lowercase via is used by open proxies/referrer spammers
if (array_key_exists('via', $wp_bb_http_headers)) {
	wp_bb_spammer("Prohibited header 'via' present");
}
// pinappleproxy is used by referrer spammers
if (array_key_exists('Via', $wp_bb_http_headers_mixed)) {
	if (stripos($wp_bb_http_headers_mixed['Via'], "pinappleproxy") !== FALSE ||
	    stripos($wp_bb_http_headers_mixed['Via'], "PCNETSERVER") !== FALSE ||
	    stripos($wp_bb_http_headers_mixed['Via'], "Invisiware") !== FALSE) {
		wp_bb_spammer("Banned proxy server '" . $wp_bb_http_headers_mixed['Via'] . "' in use");
	}
}

// TE: if present must have Connection: TE
// RFC 2616 14.39
if (array_key_exists('Te', $wp_bb_http_headers_mixed)) {
	if (!preg_match('/\bTE\b/', $wp_bb_http_headers_mixed['Connection'])) {
		wp_bb_spammer("Header 'TE' present but TE not specified in 'Connection' header");
	}
}

// Headers which are not seen from normal user agents; only malicious bots
if (array_key_exists('X-Aaaaaaaaaaaa', $wp_bb_http_headers_mixed) ||
    array_key_exists('X-Aaaaaaaaaa', $wp_bb_http_headers_mixed)) {
	wp_bb_spammer("Prohibited header 'X-Aaaaaaaaaa' or 'X-Aaaaaaaaaaaa' present");
}

?>
