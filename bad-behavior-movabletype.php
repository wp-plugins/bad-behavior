<?php

// Analyze user agents claiming to be MovableType

// Is it a trackback? If so, do a quick check on the URI
// It must resolve to the same IP as the blog sending it.
// TODO: DNS lookups take too long; skip the check for now :(
if (strcasecmp($wp_bb_request_method, "POST")) {
	if (strcmp($wp_bb_http_headers_mixed['Range'], "bytes=0-99999")) {
		wp_bb_spammer();
	}
//	$uri = parse_url($_POST['url']);
//	$host = $uri['host'];
//	$ip = gethostbyname($host);
//	if ($ip != $host && $ip != $wp_bb_remote_addr) {
//		wp_bb_spammer();
//	}
}

?>
