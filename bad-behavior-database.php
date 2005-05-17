<?php

// Database functions. This needs review to ensure it isn't MySQL specific;
// it certainly is right now.

$wp_bb_log = "bad_behavior_log";

function wp_bb_db_create_tables() {
	global $wp_bb_log, $wp_bb_db_failure;

	$query = "CREATE TABLE IF NOT EXISTS `$wp_bb_log` (
		`id` int(11) NOT NULL auto_increment,
		`ip` text NOT NULL,
		`date` datetime NOT NULL default '0000-00-00 00:00:00',
		`request_method` text NOT NULL,
		`http_host` text,
		`request_uri` text NOT NULL,
		`server_protocol` text NOT NULL,
		`http_referer` text,
		`http_user_agent` text,
		`http_headers` text NOT NULL,
		`request_entity` text,
		`http_response` int(3) NOT NULL,
		PRIMARY KEY (`id`) );";
	if (wp_bb_db_query($query) === FALSE) {
		$wp_bb_db_failure = TRUE;
	}
	// Upgrades from 1.0
	$query = "DESCRIBE `bad_behavior_log` `request_entity`;";
	if (wp_bb_db_query($query) == 0) {
		$query = "ALTER TABLE `bad_behavior_log` ADD `request_entity` TEXT AFTER `http_headers`;";
		if (wp_bb_db_query($query) === FALSE) {
			$wp_bb_db_failure = TRUE;
		}
	}
}

function wp_bb_db_clear_old_entries() {
	global $wp_bb_log, $wp_bb_logging_duration;

	$query = "DELETE FROM `$wp_bb_log` WHERE
		`date` < DATE_SUB('" . gmstrftime("%Y-%m-%d %H:%M:%S") .
		"', INTERVAL $wp_bb_logging_duration DAY)";
	if (wp_bb_db_query($query) === FALSE) {
		$wp_bb_db_failure = TRUE;
	}
}

// This sanitizes input for SQL! not necessarily for anything else
function wp_bb_db_sanitize($untrusted_input) {
	return addslashes($untrusted_input);
}

function wp_bb_db_log($response) {
	global $wp_bb_remote_addr, $wp_bb_request_method, $wp_bb_http_host;
	global $wp_bb_request_uri, $wp_bb_server_protocol, $wp_bb_http_referer;
	global $wp_bb_http_user_agent, $wp_bb_headers, $wp_bb_log;
	global $wp_bb_request_entity;

	// Sanitize input
	$request_uri = wp_bb_db_sanitize($wp_bb_request_uri);
	$referer = wp_bb_db_sanitize($wp_bb_http_referer);
	$user_agent = wp_bb_db_sanitize($wp_bb_http_user_agent);
	$headers = wp_bb_db_sanitize($wp_bb_headers);
	$request_entity = wp_bb_db_sanitize($wp_bb_request_entity);

	$date = wp_bb_date();
	$query = "INSERT INTO `$wp_bb_log`
		(`ip`, `date`, `request_method`, `http_host`, `request_uri`, `server_protocol`, `http_referer`, `http_user_agent`, `http_headers`, `request_entity`, `http_response`) VALUES
		('$wp_bb_remote_addr', '$date', '$wp_bb_request_method', '$wp_bb_http_host', '$request_uri', '$wp_bb_server_protocol', '$referer', '$user_agent', '$headers', '$request_entity', '$response')";
	if (wp_bb_db_query($query) === FALSE) {
		$wp_bb_db_failure = TRUE;
	}
}

wp_bb_db_create_tables();
wp_bb_db_clear_old_entries();
?>
