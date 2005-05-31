<?php

// This file is included only when the user-agent is claiming to be msnbot

// require_once(WP_BB_CWD . "/bad-behavior-accept.php");

if (matchCIDR($wp_bb_remote_addr, "207.46.0.0/16") === FALSE) {
	wp_bb_spammer();
}

?>
