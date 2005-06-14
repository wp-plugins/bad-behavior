<?php

// This file is included only when the user-agent is claiming to be msnbot

if (!defined('WP_BB_CWD'))
	die('');

// require_once(WP_BB_CWD . "/bad-behavior-accept.php");

if (matchCIDR($wp_bb_remote_addr, "207.46.0.0/16") === FALSE &&
    matchCIDR($wp_bb_remote_addr, "65.52.0.0/14") === FALSE &&
    matchCIDR($wp_bb_remote_addr, "207.68.128.0/18") === FALSE &&
    matchCIDR($wp_bb_remote_addr, "207.68.192.0/20") === FALSE &&
    matchCIDR($wp_bb_remote_addr, "64.4.0.0/18") === FALSE) {
	wp_bb_spammer();
}

?>
