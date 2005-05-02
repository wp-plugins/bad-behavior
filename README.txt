=== Bad Behavior ===
Tags: comment,trackback,referrer,spam,robot,antispam
Contributors: MichaelHampton,MarkJaquith,FirasDurri
SeeAlso: http://www.ioerror.us/software/bad-behavior/

This README.txt file applies to WordPress installations. For other types of
installations, please see the documentation at
http://www.ioerror.us/software/bad-behavior/installing-and-using-bad-behavior/
.

Bad Behavior is a set of PHP scripts which prevents spambots from accessing your site by analyzing their actual HTTP requests and comparing them to profiles from known spambots. It goes far beyond User-Agent and Referer, however.

The problem: Spammers run automated scripts which read everything on your web site, harvest email addresses, and if you have a blog, forum or wiki, will post spam directly to your site. They also put false referrers in your server log trying to get their links posted through your stats page.

As the operator of a Web site, this can cause you several problems. First, the spammers are wasting your bandwidth, which you may well be paying for. Second, they are posting comments to any form they can find, filling your web site with unwanted (and unpaid!) ads for their products. Last but not least, they harvest any email addresses they can find and sell those to other spammers, who fill your inbox with more unwanted ads.

Bad Behavior intends to target any malicious software directed at a Web site, whether it be a spambot, ill-designed search engine bot, or system crackers. In that spirit, it is not limited to WordPress users; a generic interface has been provided whereby it can be integrated into virtually any PHP-based software.

== Installation ==
SeeAlso: http://www.ioerror.us/software/bad-behavior/installing-and-using-bad-behavior/

1. Bad Behavior installs like any other multi-file WordPress plugin. Unzip the bad-behavior.zip file, and you will have a bad-behavior folder containing all the Bad Behavior files.

2. Before uploading, edit the bad-behavior/bad-behavior-wordpress-plugin.php file and customize the configuration variables there. When logging is on, all blocked requests will be logged. When verbose logging is on, all requests - successful or not - will be logged. And the logging duration specifies how many days worth of logs will be stored in the database. I recommend not using verbose logging without a really good reason, as your database will fill up fast.

3. Upload the folder and its contents to your wp-content/plugins directory, taking care to use ASCII mode. Once on the server, activate the plugin from your admin page.

== Frequently Asked Questions ==
SeeAlso: http://www.ioerror.us/software/bad-behavior/bad-behavior-faq/

= I have been blocked by Bad Behavior! What do I do? =

In extremely rare circumstances, Bad Behavior may block actual human visitors. Bad Behavior was designed to target robots, not people. If this happens, the profile presented by your browser matched that seen from actual malicious robots. In most cases, this is caused by over-aggressive personal firewall/browser privacy software. In some cases, this is caused by improperly configured Web proxy server software.

Try disabling the browser privacy settings in your personal firewall/browser privacy software, and/or bypassing the Web proxy and making a direct connection. Disable any download accelerators in use, especially if you are on dialup and your ISP provided you one. (These so-called accelerators rarely speed up anything; our analysis of these accelerators indicate they usually slow things down!) If all else fails, try a different Web browser with a new user profile.

If you continue to have trouble, contact me and provide a copy of the logs which Bad Behavior stores in the database showing where your IP address was blocked. I will provide further assistance until the trouble is resolved.

= How can I view Bad Behavior's log files? =

To view the Bad Behavior log, you will need a copy of phpMyAdmin installed, or some other way to view the database. Bad Behavior stores its log in the bad_behavior_log table in your WordPress database. Browse or search through it with phpMyAdmin, the MySQL command line, or another tool. At this time Bad Behavior does not come with a built-in log viewer, though this feature is planned.
