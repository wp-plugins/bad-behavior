<?php

// Database functions. This needs review to ensure it isn't MySQL specific;
// it certainly is right now.

if (!defined('WP_BB_CWD'))
	die('');

function wp_bb_db_create_tables() {
	global $wp_bb_db_failure;

	if (defined("WP_BB_NO_CREATE"))
		return;

	// Determine if we can skip all this
	// If this exists, table structure is presumed up to date
	$query = "DESCRIBE `" . WP_BB_LOG . "` `denied_reason`;";
	if (wp_bb_db_query($query) != 0) return;

	// Create everything
	$query = "CREATE TABLE IF NOT EXISTS `" . WP_BB_LOG . "` (
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
		`request_entity` text NOT NULL,
		`denied_reason` text NOT NULL,
		`http_response` int(3) NOT NULL,
		PRIMARY KEY (`id`) );";
	if (wp_bb_db_query($query) === FALSE) {
		$wp_bb_db_failure = TRUE;
	}
	// Upgrades from 1.1
	$query = "DESCRIBE `" . WP_BB_LOG . "` `denied_reason`;";
	if (wp_bb_db_query($query) == 0) {
		$query = "ALTER TABLE `" . WP_BB_LOG . "` ADD `denied_reason` TEXT AFTER `request_entity`;";
		if (wp_bb_db_query($query) === FALSE) {
			$wp_bb_db_failure = TRUE;
		}
	}
	// Upgrades from 1.0
	$query = "DESCRIBE `" . WP_BB_LOG . "` `request_entity`;";
	if (wp_bb_db_query($query) == 0) {
		$query = "ALTER TABLE `" . WP_BB_LOG . "` ADD `request_entity` TEXT AFTER `http_headers`;";
		if (wp_bb_db_query($query) === FALSE) {
			$wp_bb_db_failure = TRUE;
		}
	}
}

function wp_bb_db_clear_old_entries() {
	global $wp_bb_logging_duration;

	$query = "DELETE FROM `" . WP_BB_LOG . "` WHERE
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

function wp_bb_db_log($response, $denied_reason = '') {
	global $wp_bb_remote_addr, $wp_bb_request_method, $wp_bb_http_host;
	global $wp_bb_request_uri, $wp_bb_server_protocol, $wp_bb_http_referer;
	global $wp_bb_http_user_agent, $wp_bb_headers;
	global $wp_bb_request_entity;

	// Sanitize input
	$remote_addr = wp_bb_db_sanitize($wp_bb_remote_addr);
	$request_method = wp_bb_db_sanitize($wp_bb_request_method);
	$host = wp_bb_db_sanitize($wp_bb_http_host);
	$request_uri = wp_bb_db_sanitize($wp_bb_request_uri);
	$server_protocol = wp_bb_db_sanitize($wp_bb_server_protocol);
	$referer = wp_bb_db_sanitize($wp_bb_http_referer);
	$user_agent = wp_bb_db_sanitize($wp_bb_http_user_agent);
	$headers = wp_bb_db_sanitize($wp_bb_headers);
	$request_entity = wp_bb_db_sanitize($wp_bb_request_entity);
	$denied_reason = wp_bb_db_sanitize($denied_reason);
	$response = intval($response);

	$date = wp_bb_date();
	$query = "INSERT INTO `" . WP_BB_LOG . "`
		(`ip`, `date`, `request_method`, `http_host`, `request_uri`, `server_protocol`, `http_referer`, `http_user_agent`, `http_headers`, `request_entity`, `denied_reason`, `http_response`) VALUES
		('$remote_addr', '$date', '$request_method', '$host', '$request_uri', '$server_protocol', '$referer', '$user_agent', '$headers', '$request_entity', '$denied_reason', '$response')";
	if (wp_bb_db_query($query) === FALSE) {
		$wp_bb_db_failure = TRUE;
	}
}

// See if we've seen this spammer before.
function wp_bb_db_search() {
	global $wp_bb_remote_addr;

	$query = "SELECT `ip` FROM `" . WP_BB_LOG . "` WHERE `ip` LIKE `" . $wp_bb_remote_addr . "` AND `http_response` = 403";
	return wp_bb_db_query($query);
}

wp_bb_db_create_tables();
wp_bb_db_clear_old_entries();
?>
