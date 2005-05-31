<?php

// This file is included only when the user-agent is claiming to be Google

// require_once(WP_BB_CWD . "/bad-behavior-accept.php");

if (matchCIDR($wp_bb_remote_addr, "66.249.64.0/19") === FALSE) {
	wp_bb_spammer();
}

?>
