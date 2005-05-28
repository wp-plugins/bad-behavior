<?php

// This file is included only when the user-agent is claiming to be Konqueror
// (or some variant thereof).  We check the HTTP headers to see if it really
// is Konqueror.

require_once(WP_BB_CWD . "/bad-behavior-accept.php");

// CafeKelsa is a dev project at Yahoo which indexes job listings for
// Yahoo! HotJobs. It identifies as Konqueror so we skip these checks.
if (stripos($wp_bb_http_user_agent, "YahooSeeker/CafeKelsa") === FALSE ||
    matchCIDR($wp_bb_remote_addr, "209.73.160.0/19") === FALSE) {
	// Konqueror always sends the Accept: header.
	wp_bb_check_accept();

	// Konqueror always sends the Accept-Encoding: header.
	wp_bb_check_accept_encoding();
}

?>
