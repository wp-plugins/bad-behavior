<?php

// Analyze user agents claiming to be Opera

require_once($wp_bb_cwd . "/bad-behavior-accept.php");

wp_bb_check_accept();
wp_bb_check_accept_encoding();

?>
