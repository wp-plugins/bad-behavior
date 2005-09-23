<?php
// Contains PHP 5 functions reimplemented for PHP 4

// Helper functions
// stripos() needed because stripos is only present on PHP 5
if (!function_exists('stripos')) {
	function stripos($haystack,$needle,$offset = 0) {
		return(strpos(strtolower($haystack),strtolower($needle),$offset));
	}
}

?>
