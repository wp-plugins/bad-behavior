<?php
// This function is called when there is absolutely no hope for redemption for
// the offending spammer.
function wp_bb_banned() {
	global $wp_bb_request_uri, $wp_bb_remote_addr, $wp_bb_server_signature;
	global $wp_bb_email;

	wp_bb_log(403);
	header("HTTP/1.0 412 Precondition Failed");
	header("Status: 412 Precondition Failed");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>412 Precondition Failed</title>
</head>
<body>
<h1>Precondition Failed</h1>
<p>We're sorry, but we could not fulfill your request for
<?php echo htmlspecialchars($wp_bb_request_uri) ?> on this server.</p>
<p>We have established rules for access to this server, and any person or robot
that violates these rules will be unable to access this site.</p>
<p>To resolve this problem, please try the following steps:</p>
<ul>
<li>Ensure that your computer is free of viruses, Trojan horses, spyware or
any other sort of malicious software.</li>
<li>If you are using any sort of personal firewall or browser privacy
software, check to ensure that its settings do not cause your web browser to
inadvertently violate any of the rules listed below.</li>
<li>If you are behind a Web proxy or corporate firewall, the proxy must
conform to the <a href="http://www.isi.edu/in-notes/rfc2616.txt">HTTP
specification</a> with respect to proxy servers. Contact your network
administrator if the trouble persists, or bypass the proxy and connect
directly if possible.</li>
<li>Disable any download accelerators you may be using. They don't speed up
your downloads anyway; in most cases, they actually run slower!</li>
<li>If all else fails, try using a different Web browser, such as
<a href="http://www.spreadfirefox.com/?q=affiliates&amp;id=32135&amp;t=71">Firefox</a>.</li>
</ul>
<p>If you still need assistance, please contact <a href="mailto:<?php echo htmlspecialchars(str_replace("@", "+nospam@nospam.", $wp_bb_email)); ?>"><?php echo htmlspecialchars(str_replace("@", " at ", $wp_bb_email)); ?></a>.</p>
<h2>More Information</h2>
<p>For your reference, the conditions for access to this server are:</p>
<h3>Robots:</h3>
<ul>
<li>MUST read and obey <a href="http://www.robotstxt.org/">robots.txt</a>.</li>
<li>MUST identify themselves properly; for example MUST NOT identify as Mozilla.</li>
<li>MUST NOT pretend to be a human.</li>
</ul>
<h3>Humans:</h3>
<ul>
<li>MUST NOT pretend to be a robot.</li>
<li>MUST NOT use a computer infected with viruses, Trojan horses or other
malicious software.</li>
</ul>
<h3>Both:</h3>
<ul>
<li>MUST NOT harvest email addresses.</li>
<li>MUST NOT attempt to send spam.</li>
<li>MUST NOT attempt to compromise server security.</li>
<li>MUST NOT use excessive amounts of bandwidth or other server resources.</li>
</ul>
<p>The precondition on the request for the URL
<?php echo htmlspecialchars($wp_bb_request_uri); ?> evaluated to false.</p>
<hr>
<?php echo htmlspecialchars($wp_bb_server_signature); ?>
</body>
</html>
<?php
	die('');
}
?>
