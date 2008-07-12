<?php if (!defined('BB2_CORE')) die('I said no cheating!');

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
	}
}

function bb2_manage() {
	$settings = bb2_read_settings();
	$rows_per_page = 100;
	$where = "";

	// Get query variables desired by the user

	// Query the DB based on variables selected
	$r = bb2_db_query("SELECT COUNT(*) FROM `" . $settings['log_table'] . "` WHERE 1=1 " . $where);
	$count = bb2_db_num_rows($r);
	$r = bb2_db_query("SELECT * FROM `" . $settings['log_table'] . "` WHERE 1=1 " . $where . "ORDER BY `date` DESC LIMIT 0," . $rows_per_page);
	$results = bb2_db_rows($r);

	// Display rows to the user
?>
<div class="wrap">
<h2><?php _e("Bad Behavior"); ?></h2>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="widefat">
	<thead>
	<tr>
	<th scope="col" class="check-column"><input type="checkbox" onclick="checkAll(document.getElementById('request-filter'));" /></th>
	<th scope="col"><?php _e("IP"); ?></th>
	<th scope="col"><?php _e("Date"); ?></th>
	<th scope="col"><?php _e("Method"); ?></th>
	<th scope="col"><?php _e("URL"); ?></th>
	<th scope="col"><?php _e("User Agent"); ?></th>
	<th scope="col"><?php _e("Headers"); ?></th>
	<th scope="col"><?php _e("Entity"); ?></th>
	</tr>
	</thead>
	<tbody>
<?php
	$alternate = 0;
	$ahref = "<a href=\"" . $_SERVER['REQUEST_URI'] . "?";
	foreach ($results as $result) {
		$alternate++;
		if ($alternate % 2) {
			echo "<tr id=\"request-" . $result["id"] . "\" valign=\"top\">\n";
		} else {
			echo "<tr id=\"request-" . $result["id"] . "\" class=\"alternate\" valign=\"top\">\n";
		}
		echo "<th scope=\"row\" class=\"check-column\"><input type=\"checkbox\" name=\"submit[]\" value=\"" . $result["id"] . "\" /></th>\n";
		echo "<td>${ahref}ip=" . $result["ip"] . "\">" . $result["ip"] "</a></td>\n";
		echo "<td>" . $result["date"] . "</td>\n";
		echo "<td>${ahref}request_method=" . $result["request_method"] . "\">" . $result["request_method"] . "</a></td>\n";
		echo "<td>" . $result["request_uri"] . "</td>\n";
		echo "<td>${ahref}user_agent=" . $result["user_agent"] . "\">" . $result["user_agent"] . "</a></td>\n";
		echo "<td>" . str_replace("\n", "<br/>\n", $result["http_headers"]) . "</td>\n";
		echo "<td>" . str_replace("\n", "<br/>\n", $result["request_entity"]) . "</td>\n";
		echo "</tr>\n";
	}
?>
	</tbody>
</table>
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
