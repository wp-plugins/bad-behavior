<?php
/*
Plugin Name: Bad Behavior
Version: 1.1.1
Plugin URI: http://www.ioerror.us/software/bad-behavior/
Description: Stop comment spam before it starts by trapping and blocking spambots before they have a chance to post comments.
Author: Michael Hampton
Author URI: http://www.ioerror.us/
License: GPL

Bad Behavior - detects and blocks unwanted Web accesses
Copyright (C) 2005 Michael Hampton

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.


This is beta software so please report any problems to
badbots AT ioerror DOT us
*/

###############################################################################
###############################################################################

// Configuration

// Log failed requests to the database.
$wp_bb_logging = TRUE;

// Log all requests to the database, not just failed requests.
$wp_bb_verbose_logging = FALSE;

// How long to keep the logs around (in days).
$wp_bb_logging_duration = 7;

// Email address to contact you in case of problems
// This will be shown to users on the error page, which means it will
// be exposed to spammers! Bad Behavior will munge it automatically; you
// should NOT munge it here!
$wp_bb_email = get_bloginfo('admin_email');
//$wp_bb_email = "badbots@ioerror.us";

// The database table name to use.
// You can customize the table name if necessary.
define('WP_BB_LOG', $table_prefix . 'bad_behavior_log');

###############################################################################

#                          DO NOT EDIT BELOW THIS LINE

###############################################################################

$wp_bb_mtime = explode(' ', microtime());
$wp_bb_timer_start = $wp_bb_mtime[1] + $wp_bb_mtime[0];
define('WP_BB_CWD', dirname(__FILE__));

// WordPress-specific code

function wp_bb_date() {
	return get_gmt_from_date(current_time('mysql'));
}

function wp_bb_db_query($query) {
	global $wpdb;

	$result = $wpdb->query($query);
	if (mysql_error()) {
		return FALSE;
	}
	return $result;
}

// Load core functions and do initial checks
require_once(WP_BB_CWD . "/bad-behavior-core.php");

$wp_bb_mtime = explode(' ', microtime());
$wp_bb_timer_stop = $wp_bb_mtime[1] + $wp_bb_mtime[0];
$wp_bb_timer_total = $wp_bb_timer_stop - $wp_bb_timer_start;

function wp_bb_timer_display() {
	global $wp_bb_timer_total;
	echo "\n<!-- Bad Behavior run time: " . number_format($wp_bb_timer_total, 3) . " seconds -->\n";
}

add_action('wp_head', 'wp_bb_timer_display');

?>
