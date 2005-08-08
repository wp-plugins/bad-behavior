<?php

// Set to false to NOT notify Bad Behavior Blackhole
// You would do this if it's being slow or whatever
$wp_bb_ping_blackhole = true;

// Set the connection timeout in seconds.
$wp_bb_ping_timeout = 15;

if (!defined('WP_BB_CWD'))
	die('');

function wp_bb_blackhole_ping($response, $denied_reason) {
	global $wp_bb_remote_addr, $wp_bb_request_method, $wp_bb_http_host;
	global $wp_bb_request_uri, $wp_bb_server_protocol, $wp_bb_http_referer;
	global $wp_bb_http_user_agent, $wp_bb_headers, $wp_bb_request_entity;

	$ping = "http://gw.ioerror.us/ping";

	$remote_addr = urlencode($wp_bb_remote_addr);
	$request_method = urlencode($wp_bb_request_method);
	$http_host = urlencode($wp_bb_http_host);
	$request_uri = urlencode($wp_bb_request_uri);
	$server_protocol = urlencode($wp_bb_server_protocol);
	$http_referer = urlencode($wp_bb_http_referer);
	$user_agent = urlencode($wp_bb_http_user_agent);
	$headers = urlencode($wp_bb_headers);
	$request_entity = urlencode($wp_bb_request_entity);
	$denied = urlencode($denied_reason);

	$query = "remote_addr=$remote_addr&request_method=$request_method&http_host=$http_host&request_uri=$request_uri&server_protocol=$server_protocol&http_referer=$http_referer&user_agent=$user_agent&headers=$headers&request_entity=$request_entity&denied_reason=$denied&http_response=$response";

	$ping_url = parse_url($ping);
	if ('' == $ping_url['port']) $ping_url['port'] = 80;

	$post_request = "POST " . $ping_url['path'] . ($ping_url['query'] ? "?".$ping_url['query'] : "") . " HTTP/1.0\r\n";
	$post_request .= "Host: " . $ping_url['host'] . "\r\n";
	$post_request .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";
	$post_request .= "Content-Length: " . strlen($query) . "\r\n";
	$post_request .= "User-Agent: Bad Behavior/" . WP_BB_VERSION . "\r\n";
	$post_request .= "\r\n" . $query;

	// Now determine HOW to send the ping
	$ping_sent = false;
	if (ini_get('allow_url_fopen') != false) {
		$fs = @fsockopen($ping_url['host'], $ping_url['port'], $errno, $errstr, $wp_bb_ping_timeout);
		if ($fs) {
			@fputs($fs, $post_request);
			// Don't bother with a response
			@fclose($fs);
			$ping_sent = true;
		}
	}
	if (!$ping_sent && is_callable('curl_init')) {
		$ch = curl_init($ping);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $wp_bb_ping_timeout);
		curl_setopt($ch, CURLOPT_PORT, $ping_url['port']);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		curl_setopt($ch, CURLOPT_USERAGENT, "Bad Behavior/" . WP_BB_VERSION);
		curl_exec($ch);
		$r = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($r >= 200 && $r <= 399) {
			$ping_sent = true;
		}
	}
}
?>
