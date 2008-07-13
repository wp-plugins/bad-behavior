<?php if (!defined('BB2_CORE')) die('I said no cheating!');

require_once("bad-behavior/responses.inc.php");

function bb2_admin_pages() {
	if (function_exists('current_user_can')) {
		// The new 2.x way
		if (current_user_can('manage_options')) {
			$bb2_is_admin = true;
		}
	} else {
		// The old 1.x way
		global $user_ID;
		if (user_can_edit_user($user_ID, 0)) {
			$bb2_is_admin = true;
		}
	}

	if ($bb2_is_admin) {
		add_options_page(__("Bad Behavior"), __("Bad Behavior"), 8, 'bb2_options', 'bb2_options');
		add_management_page(__("Bad Behavior"), __("Bad Behavior"), 8, 'bb2_manage', 'bb2_manage');
		@session_start();
	}
}

function bb2_clean_log_link($uri) {
	foreach (array("paged", "ip", "key", "blocked", "request_method", "user_agent") as $arg) {
		$uri = remove_query_arg($arg, $uri);
	}
	return $uri;
}

function bb2_httpbl_lookup($ip) {
	$engines = array(
		2 => "Bloglines",
		5 => "Googlebot",
		8 => "msnbot",
		9 => "Yahoo! Slurp",
	);
	$httpbl_key = "owwdrvbhklry";
	$r = $_SESSION['httpbl'][$ip];
	$d = "";
	if (!$r) {	// Lookup
		$find = implode('.', array_reverse(explode('.', $ip)));
		$result = gethostbynamel("${httpbl_key}.${find}.dnsbl.httpbl.org.");
		if (!empty($result)) {
			$r = $result[0];
			$_SESSION['httpbl'][$ip] = $r;
		}
	}
	if ($r) {	// Interpret
		$ip = explode('.', $r);
		if ($ip[0] == 127) {
			if ($ip[3] == 0) {
				if ($engines[$ip[2]]) {
					$d .= $engines[$ip[2]];
				} else {
					$d .= "Search engine ${ip[2]}<br/>\n";
				}
			}
			if ($ip[3] & 1) {
				$d .= "Suspicious<br/>\n";
			}
			if ($ip[3] & 2) {
				$d .= "Harvester<br/>\n";
			}
			if ($ip[3] & 4) {
				$d .= "Comment Spammer<br/>\n";
			}
			if ($ip[3] & 7) {
				$d .= "Threat level ${ip[2]}<br/>\n";
			}
			if ($ip[3] > 0) {
				$d .= "Age ${ip[1]} days<br/>\n";
			}
		}
	}
	return $d;
}

function bb2_manage() {
	global $wpdb;

	$request_uri = $_SERVER["REQUEST_URI"];
	$settings = bb2_read_settings();
	$rows_per_page = 100;
	$where = "";

	// Get query variables desired by the user
	$paged = 0 + $_GET['paged']; if (!$paged) $paged = 1;
	if ($_GET['key']) $where .= "AND `key` = '" . $wpdb->escape($_GET['key']) . "' ";
	if ($_GET['blocked']) $where .= "AND `key` != '00000000' ";
	if ($_GET['ip']) $where .= "AND `ip` = '" . $wpdb->escape($_GET['ip']) . "' ";
	if ($_GET['user_agent']) $where .= "AND `user_agent` = '" . $wpdb->escape($_GET['user_agent']) . "' ";
	if ($_GET['request_method']) $where .= "AND `request_method` = '" . $wpdb->escape($_GET['request_method']) . "' ";

	// Query the DB based on variables selected
	$r = bb2_db_query("SELECT COUNT(*) FROM `" . $settings['log_table']);
	$results = bb2_db_rows($r);
	$totalcount = $results[0]["COUNT(*)"];
	$r = bb2_db_query("SELECT COUNT(*) FROM `" . $settings['log_table'] . "` WHERE 1=1 " . $where);
	$results = bb2_db_rows($r);
	$count = $results[0]["COUNT(*)"];
	$pages = ceil($count / 100);
	$r = bb2_db_query("SELECT * FROM `" . $settings['log_table'] . "` WHERE 1=1 " . $where . "ORDER BY `date` DESC LIMIT " . ($paged - 1) * $rows_per_page . "," . $rows_per_page);
	$results = bb2_db_rows($r);

	// Display rows to the user
?>
<div class="wrap">
<h2><?php _e("Bad Behavior"); ?></h2>
<form method="post" action="<?php echo $request_uri; ?>">
	<p>For more information please visit the <a href="http://www.bad-behavior.ioerror.us/">Bad Behavior</a> homepage.</p>
	<p>If you find Bad Behavior valuable, please consider making a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=error%40ioerror%2eus&item_name=Bad%20Behavior%20<?php echo BB2_VERSION; ?>%20%28From%20Admin%29&no_shipping=1&cn=Comments%20about%20Bad%20Behavior&tax=0&currency_code=USD&bn=PP%2dDonationsBF&charset=UTF%2d8">financial contribution</a> to further development of Bad Behavior.</p>

<div class="tablenav">
<?php
	$page_links = paginate_links(array('base' => add_query_arg("paged", "%#%"), 'format' => '', 'total' => $pages, 'current' => $paged));
	if ($page_links) echo "<div class=\"tablenav-pages\">$page_links</div>\n";
?>
<div class="alignleft">
<?php if ($count < $totalcount): ?>
Displaying <strong><?php echo $count; ?></strong> of <strong><?php echo $totalcount; ?></strong> records filtered by:<br/>
<?php if ($_GET['ip']) echo "IP [<a href=\"" . remove_query_arg(array("paged", "ip"), $request_uri) . "\">X</a>] "; ?>
<?php if ($_GET['key']) echo "Status [<a href=\"" . remove_query_arg(array("paged", "key"), $request_uri) . "\">X</a>] "; ?>
<?php if ($_GET['blocked']) echo "Blocked [<a href=\"" . remove_query_arg(array("paged", "blocked"), $request_uri) . "\">X</a>] "; ?>
<?php if ($_GET['user_agent']) echo "User Agent [<a href=\"" . remove_query_arg(array("paged", "user_agent"), $request_uri) . "\">X</a>] "; ?>
<?php if ($_GET['request_method']) echo "GET/POST [<a href=\"" . remove_query_arg(array("paged", "request_method"), $request_uri) . "\">X</a>] "; ?>
<?php else: ?>
Displaying all <strong><?php echo $totalcount; ?></strong> records<br/>
<?php endif; ?>
<?php if (!$_GET['key'] && !$_GET['blocked']) { ?><a href="<?php echo add_query_arg(array("blocked" => "true", "paged" => false), $request_uri); ?>">Show Blocked</a><?php } ?>
</div>
</div>

<table class="widefat">
	<thead>
	<tr>
	<th scope="col" class="check-column"><input type="checkbox" onclick="checkAll(document.getElementById('request-filter'));" /></th>
	<th scope="col"><?php _e("IP/Date/Status"); ?></th>
	<th scope="col"><?php _e("Headers"); ?></th>
	<th scope="col"><?php _e("Entity"); ?></th>
	</tr>
	</thead>
	<tbody>
<?php
	$alternate = 0;
	foreach ($results as $result) {
		$key = bb2_get_response($result["key"]);
		$alternate++;
		if ($alternate % 2) {
			echo "<tr id=\"request-" . $result["id"] . "\" valign=\"top\">\n";
		} else {
			echo "<tr id=\"request-" . $result["id"] . "\" class=\"alternate\" valign=\"top\">\n";
		}
		echo "<th scope=\"row\" class=\"check-column\"><input type=\"checkbox\" name=\"submit[]\" value=\"" . $result["id"] . "\" /></th>\n";
		$httpbl = bb2_httpbl_lookup($result["ip"]);
		echo "<td><a href=\"" . add_query_arg("ip", $result["ip"], remove_query_arg("paged", $request_uri)) . "\">" . $result["ip"] . "</a><br/><br/>\n" . $result["date"] . "<br/><br/><a href=\"" . add_query_arg("key", $result["key"], remove_query_arg(array("paged", "blocked"), $request_uri)) . "\">" . $key["log"] . "</a>\n";
		if ($httpbl) echo "<br/><br/>http:BL:<br/>$httpbl\n";
		echo "</td>\n";
		$headres = $result['http_headers'];
		if (strpos($headers, $result['user_agent']) !== FALSE) $headers = substr_replace($headers, "<a href=\"" . add_query_arg("user_agent", $result["user_agent"], remove_query_arg("paged", $request_uri)) . "\">", strpos($headers, $result['user_agent']), strlen($result['user_agent']));
		if (strpos($headers, $result['request_method']) !== FALSE) $headers = substr_replace($headers, "<a href=\"" . add_query_arg("request_method", $result["request_method"], remove_query_arg("paged", $request_uri)) . "\">", strpos($headers, $result['request_method']), strlen($result['request_method']));
		echo "<td>" . htmlspecialchars($headers) . "</td>\n";
		echo "<td>" . htmlspecialchars(str_replace("\n", "<br/>\n", $result["request_entity"])) . "</td>\n";
		echo "</tr>\n";
	}
?>
	</tbody>
</table>
<div class="tablenav">
<?php
	$page_links = paginate_links(array('base' => add_query_arg("paged", "%#%"), 'format' => '', 'total' => $pages, 'current' => $paged));
	if ($page_links) echo "<div class=\"tablenav-pages\">$page_links</div>\n";
?>
<div class="alignleft">
</div>
</div>
</form>
</div>
<?php
}

function bb2_options()
{
	$settings = bb2_read_settings();

	if ($_POST) {
		if ($_POST['display_stats']) {
			$settings['display_stats'] = true;
		} else {
			$settings['display_stats'] = false;
		}
		if ($_POST['strict']) {
			$settings['strict'] = true;
		} else {
			$settings['strict'] = false;
		}
		if ($_POST['verbose']) {
			$settings['verbose'] = true;
		} else {
			$settings['verbose'] = false;
		}
		if ($_POST['logging']) {
			if ($_POST['logging'] == 'verbose') {
				$settings['verbose'] = true;
				$settings['logging'] = true;
			} else if ($_POST['logging'] == 'normal') {
				$settings['verbose'] = false;
				$settings['logging'] = true;
			} else {
				$settings['verbose'] = false;
				$settings['logging'] = false;
			}
		} else {
			$settings['verbose'] = false;
			$settings['logging'] = false;
		}
		bb2_write_settings($settings);
?>
	<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php
	}
?>
	<div class="wrap">
	<h2><?php _e("Bad Behavior"); ?></h2>
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<p>For more information please visit the <a href="http://www.bad-behavior.ioerror.us/">Bad Behavior</a> homepage.</p>
	<p>If you find Bad Behavior valuable, please consider making a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=error%40ioerror%2eus&item_name=Bad%20Behavior%20<?php echo BB2_VERSION; ?>%20%28From%20Admin%29&no_shipping=1&cn=Comments%20about%20Bad%20Behavior&tax=0&currency_code=USD&bn=PP%2dDonationsBF&charset=UTF%2d8">financial contribution</a> to further development of Bad Behavior.</p>

	<h3><?php _e('Statistics'); ?></h3>
	<?php bb2_insert_stats(true); ?>
	<table class="form-table">
	<tr><td><label><input type="checkbox" name="display_stats" value="true" <?php if ($settings['display_stats']) { ?>checked="checked" <?php } ?>/> <?php _e('Display statistics in blog footer'); ?></label></td></tr>
	</table>

	<h3><?php _e('Logging'); ?></h3>
	<table class="form-table">
	<tr><td><label><input type="radio" name="logging" value="verbose" <?php if ($settings['verbose'] && $settings['logging']) { ?>checked="checked" <?php } ?>/> <?php _e('Verbose HTTP request logging'); ?></label></td></tr>
	<tr><td><label><input type="radio" name="logging" value="normal" <?php if ($settings['logging'] && !$settings['verbose']) { ?>checked="checked" <?php } ?>/> <?php _e('Normal HTTP request logging (recommended)'); ?></label></td></tr>
	<tr><td><label><input type="radio" name="logging" value="false" <?php if (!$settings['logging']) { ?>checked="checked" <?php } ?>/> <?php _e('Do not log HTTP requests (not recommended)'); ?></label></td></tr>
	</table>

	<h3><?php _e('Strict Mode'); ?></h3>
	<table class="form-table">
	<tr><td><label><input type="checkbox" name="strict" value="true" <?php if ($settings['strict']) { ?>checked="checked" <?php } ?>/> <?php _e('Strict checking (blocks more spam but may block some people)'); ?></label></td></tr>
	</table>

	<p class="submit"><input class="button" type="submit" name="submit" value="<?php _e('Update &raquo;'); ?>" /></p>
	</form>
	</div>
<?php
}

add_action('admin_menu', 'bb2_admin_pages');

?>
