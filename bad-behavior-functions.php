<?php

// Convert a string to mixed-case on word boundaries
function uc_all($string) {
	$temp = preg_split('/(\W)/', str_replace("_", "-", $string), -1, PREG_SPLIT_DELIM_CAPTURE );
	foreach ($temp as $key=>$word) {
		$temp[$key] = ucfirst(strtolower($word));
	}
	return join ('', $temp);
}

// Obtain all the HTTP headers.
// NB: on PHP-CGI we hve to fake it out a bit, since we can't get the raw
// headers
function getheaders() {
	if (!is_callable('getallheaders')) {
		$headers = array();
		foreach($_SERVER as $h=>$v)
			if(ereg('HTTP_(.+)',$h,$hp))
				$headers[str_replace("_", "-", uc_all($hp[1]))]=$v;
		return $headers;
	} else {
		return getallheaders();
	}
}

// $addr should be an ip address in the format '0.0.0.0'
// $cidr should be a string in the format '100/8'
//      or an array where each element is in the above format
function matchCIDR($addr, $cidr) {
       $output = false;

       if ( is_array($cidr) ) {
	       foreach ( $cidr as $cidrlet ) {
		       if ( matchCIDR( $addr, $cidrlet) ) {
			       $output = true;
		       }
	       }
       } else {
	       list($ip, $mask) = explode('/', $cidr);
	       $mask = 0xffffffff << (32 - $mask);
	       $output = ((ip2long($addr) & $mask) == (ip2long($ip) & $mask));
       }
       return $output;
}

?>
