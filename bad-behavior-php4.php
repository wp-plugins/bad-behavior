<?php
// Contains 

// Helper functions
// stripos() needed because stripos is only present on PHP 5
function stripos($haystack,$needle,$offset = 0) {
	return(strpos(strtolower($haystack),strtolower($needle),$offset));
}

?>
