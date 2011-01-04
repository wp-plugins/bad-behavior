<?php if (!defined('BB2_CORE')) die('I said no cheating!');

// Analyze user agents claiming to be Googlebot

require_once(BB2_CORE . "/roundtripdns.inc.php");

function bb2_google($package)
{
	if (match_cidr($package['ip'], array("66.249.64.0/19", "64.233.160.0/19", "72.14.192.0/18")) === FALSE) {
		return "f1182195";
	}
#	Disabled due to http://bugs.php.net/bug.php?id=53092
#	if (!bb2_roundtripdns($package['ip'], "googlebot.com")) {
#		return "f1182195";
#	}
	return false;
}

?>
