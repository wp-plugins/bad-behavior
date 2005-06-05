<?php

if (!defined('WP_BB_CWD'))
	die('');

// Is the user-agent a known spambot?
// Occurs at the beginning of the string
$wp_bb_spambots_str0 = array(
	"8484 Boston Project",	// video poker/porn spam
	"autoemailspider",	// spam harvester
	"Digger",		// spam harvester
	"ecollector",		// spam harvester
	"EmailCollector",	// spam harvester
	"Email Extractor",	// spam harvester
	"Email Siphon",		// spam harvester
	"grub crawler",		// misc comment/email spam
//	"Java 1.",		// Some doubt about this one
	"libwww-perl",		// exploited boxes
	"LWP",			// exploited boxes
	"Microsoft URL",	// spam harvester
	"Missigua",		// spam harvester
	"Mozilla ",		// forum exploits
	"www.weblogs.com",	// referrer spam (not the real www.weblogs.com)
);
// Occurs anywhere in the string
$wp_bb_spambots_str = array(
	"compatible ; MSIE",	// misc comment/email spam
	"DTS Agent",		// misc comment/email spam
	"grub-client",		// search engine ignores robots.txt
	"Indy Library",		// misc comment/email spam
	"POE-Component-Client",	// free poker, etc.
	"WISEbot",		// spam harvester
	"WISEnutbot",		// spam harvester
);
// Regex matching
$wp_bb_spambots_reg = array(
	"/^[A-Z]{10}$/",	// misc email spam
	"/^Mozilla...0$/i",	// fake user agent/email spam
//	"/MSIE.*Windows XP/",	// misc comment spam
);

foreach ($wp_bb_spambots_str0 as $wp_bb_spambot) {
	$pos = stripos($wp_bb_http_user_agent, $wp_bb_spambot);
	if ($pos !== FALSE && $pos == 0) {
		wp_bb_spammer();	// does not return
	}
}
foreach ($wp_bb_spambots_str as $wp_bb_spambot) {
	if (stripos($wp_bb_http_user_agent, $wp_bb_spambot) !== FALSE) {
		wp_bb_spammer();	// does not return
	}
}
foreach ($wp_bb_spambots_reg as $wp_bb_spambot) {
	if (preg_match($wp_bb_spambot, $wp_bb_http_user_agent)) {
		wp_bb_spammer();	// does not return
	}
}

?>
