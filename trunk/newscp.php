<?php
/*
   News Evolution v0.01 Beta
   -------------------------
   by Matthew Wells
   (c) 2005 Matthew Wells
   http://www.newsevo.com/
   -------------------------
   Time: Sat, 23 July 2005 22:14:51 GMT
   Email: matt@newsevo.com
   License: http://www.newsevo.com/license.html
   -------------------------
   > News CP Root
   > Written by Matt Wells
   > Date Started: 29th July 2005
   > Version Number: 1.0.0
   > Time Taken: 6 hours
*/

// Error Reporting you should not need to change this.
error_reporting(E_ALL ^ E_NOTICE);

// If you are having with locating files then change this to the full
// path of the News Evo folder including a trail slash.
define('ROOT', dirname(__FILE__).'/');

// This is to detect if you have changed the filename so it will work
// what ever you call it. However, you will have to edit it if you are
// including this file into another. The ? is required at the end.
define('BASE', basename(__FILE__).'?');

// Do not set this as true in a online version, it shows the queries 
// and other information about the script which could be dangerous.
define('DEBUG', true);

// Version Number
define('VERSION', 0.01);

// Include $info Configeration
require ROOT .'config.ne.php';
$prefix = $info['db_prefix'];

// Connect to Database
require ROOT .'ne.inc/dal/'.$info['db_driver'].'.php';
$dal = new $info['db_driver'];
$dal->connect();

// Fetch Other Information from Database
$info += $dal->fetch("SELECT `i`.* FROM `{$prefix}info`", __FILE__, __LINE__);

// News Evo Global
require ROOT .'ne.inc/newsevo.php';
$ne = news newsevo;

// Make Safe HTTP vars
$ne->safevars();

// Admin Skin
require ROOT .'ne.inc/skin.php';
$skin = new skin;
$skin->form_skin();

// Login Check
$is_user = false;
// Check Sessions
$input['hash'] = strip_tags($input['hash']);
if(strlen($input['hash']) == 32) {
	$session = $dal->fetch("SELECT `s`.`hash` FROM `{$prefix}sessions` `s` WHERE `s`.`time` > ".(time() - 1800)." AND `s`.`ip_address` = '".$_SERVER['REMOTE_ADDR']."' AND `s`.`hash` = '".$input['hash']."'", __FILE__, __LINE__);
	if($dal->numrows() == 1) {
		$dal->query("SELECT `u`.* FROM `{$prefix}users` `u` WHERE `u`.`uid` = ".$session['uid'], __FILE__, __LINE__);
		$is_user = true;
	}
}
if($is_user == false && ($input['name'] && $input['password'])) {
	$dal->query("DELETE FROM `{$prefix}sessions` WHERE `time` < ".(time() - 1800), __FILE__, __LINE__);
	$dal->query("SELECT `u`.* FROM `{$prefix}users` `u` WHERE `u`.`password` = '".md5($input['password'])."' AND `u`.`name` = '".$input['password']."'", __FILE__, __LINE__);
	if($dal->numrows() == 1) {
		$is_user = true;
		$session['hash'] = md5(microtime().$ne->password(8));
	}
	else { $skin->show_login(true); }
}
if($is_user == true) {
	$user = $dal->fetch();
	$dal->query("REPLACE INTO `{$prefix}session` (`hash`, `uid`, `time`, `ip_address`) VALUES ('".$session['hash']."', ".$user['uid'].", ".time().", '".$_SERVER['REMOTE_ADDR']."')", __FILE__, __LINE__);
}
else { $skin->show_login(); }

// NECP Actions
$necp_actions = array(
	'cat',
//	'feed',
	'mem',
	'news',
	'op',
	'sql',
//	'up',
);
$ne->input['act'] = strtolower($ne->input['act']);
$action = in_array($ne->input['act'], $necp_actions) ? $ne->input['act'] : 'home';
require ROOT ."ne.inc/NewsCP/{$action}.ne.php";

// Close up Shop
$dal->close();
$skin->output();
?>