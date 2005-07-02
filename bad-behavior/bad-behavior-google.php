<?php

// This file is included only when the user-agent is claiming to be Google

if (!defined('WP_BB_CWD'))
	die('');

// require_once(WP_BB_CWD . "/bad-behavior-accept.php");

if (matchCIDR($wp_bb_remote_addr, "66.249.64.0/19") === FALSE) {
	wp_bb_spammer();
}

?>
