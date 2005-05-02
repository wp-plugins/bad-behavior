<?php

// Analyze the headers of all user-agents
// These checks apply to any user-agent regardless of identification

// Range: field exists and begins with 0
// Real user-agents do not start ranges at 0
// NOTE: this blocks the whois.sc bot. No big loss.
// FIXME: whitelist whois.sc netblocks when rwhoisd.ccom.net becomes available
if (array_key_exists('Range', $wp_bb_http_headers_mixed) &&
		strpos($wp_bb_http_headers_mixed['Range'], "=0-") !== FALSE) {
	wp_bb_spammer();
}

// Lowercase via is used by open proxies/referrer spammers
if (array_key_exists('via', $wp_bb_http_headers)) {
	wp_bb_spammer();
}
// pinappleproxy is used by referrer spammers
if (array_key_exists('Via', $wp_bb_http_headers_mixed) &&
		stripos($wp_bb_http_headers['Via'], "pinappleproxy") !== FALSE) {
	wp_bb_spammer();
}

// Headers which are not seen from normal user agents; only malicious bots
if (array_key_exists('X-Aaaaaaaaaaaa', $wp_bb_http_headers_mixed) ||
    array_key_exists('X-Aaaaaaaaaa', $wp_bb_http_headers_mixed)) {
	wp_bb_spammer();
}

?>
