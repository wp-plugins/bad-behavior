<?php

// Analyze user agents claiming to be Mozilla

// Bots:
// Yahoo! Slurp passes these checks

if (!defined('WP_BB_CWD'))
	die('');

require_once(WP_BB_CWD . "/bad-behavior-accept.php");

// First off, workaround for Google Desktop, until they fix it FIXME
// Always check accept header for Mozilla user agents
if (strpos($wp_bb_http_user_agent, "Google Desktop") === FALSE)
	wp_bb_check_accept();

// AvantGo mobile browser needs a different check
// FIXME: Temporarily disable check; Danger's latest update sucks balls
//if (strpos($wp_bb_http_user_agent, "AvantGo") !== FALSE) {
//	if (!array_key_exists('X-Avantgo-Screensize', $wp_bb_http_headers_mixed)) {
//		wp_bb_spammer("User-Agent claimed to be AvantGo, claim appears false");
//	}
//} else {
	wp_bb_check_accept_encoding();
//}

?>
