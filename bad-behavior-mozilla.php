<?php

// Analyze user agents claiming to be Mozilla

// Bots:
// Yahoo! Slurp passes these checks

require_once(WP_BB_CWD . "/bad-behavior-accept.php");

wp_bb_check_accept();
// AvantGo mobile browser needs a different check
if (strpos($wp_bb_http_user_agent, "AvantGo") !== FALSE) {
	if (!array_key_exists('X-Avantgo-Screensize', $wp_bb_http_headers_mixed)) {
		wp_bb_spammer();
	}
} else {
	wp_bb_check_accept_encoding();
}

?>
