<?php
/*
http://www.ioerror.us/software/bad-behavior/

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
*/

// This file is the entry point for Bad Behavior.
// See below to customize it for your particular CMS or PHP application.
// Preferably by the time this file is loaded, everything in your app will
// be initialized, but no output sent to the browser.  If output has been
// sent, Bad Behavior will fail.

###############################################################################
###############################################################################

// Configuration
// Nothing will be written to a DB unless you implement DB queries below

// Log failed requests to the database.
$wp_bb_logging = TRUE;

// Log all requests to the database, not just failed requests.
$wp_bb_verbose_logging = TRUE;

// How long to keep the logs around (in days).
// Lowering this may have profound negative effects!
$wp_bb_logging_duration = 7;

// Email address to contact you in case of problems
// This will be shown to users on the error page, which means it will
// be exposed to spammers! Bad Behavior will munge it automatically; you
// should NOT munge it here!
$wp_bb_email = "badbots@ioerror.us";

// The database table name to use.
// You can customize the table name if necessary.
define('WP_BB_LOG', /* $software_specific_prefix . */ 'bad_behavior_log');

###############################################################################

#                          DO NOT EDIT BELOW THIS LINE

###############################################################################

define('WP_BB_CWD', dirname(__FILE__));

// Callbacks

// generic code; you should reimplement these if you want logging and
// database functions

// return a UTC date in the format preferred by your database
function wp_bb_date() {
	return gmdate('Y-m-d H:i:s');
}

// run a SQL query and return # of rows affected, or FALSE if query failed
function wp_bb_db_query($query) {
	return FALSE;
}

// Load core functions and do initial checks
require_once(WP_BB_CWD . "/bad-behavior-core.php");

?>
