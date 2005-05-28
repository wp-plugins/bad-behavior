<?php

// Analyze user agents claiming to be Lynx

require_once(WP_BB_CWD . "/bad-behavior-accept.php");

wp_bb_check_accept();
wp_bb_check_accept_encoding();

?>
