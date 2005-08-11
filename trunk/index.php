<?php
/*
   News Evolution v0.01 Beta
   -------------------------
   by Matthew Wells
   (c) 2005 Matthew Wells
   http://www.newsevo.com/
   -------------------------
   Time: Sat, 23 July 2005, 22:14:51 GMT
   Email: matt@newsevo.com
   License: http://www.newsevo.com/license.html
   -------------------------
   > Database Controls
   > Written by Matt Wells
   > Date Started: 29th July 2005
   > Version Number: 1.0.0
   > Time Taken: 4 hours
*/

// Error Reporting you should not need to change this.
error_reporting(E_ALL ^ E_NOTICE);

// If you are having with locating files then change this to the full
// path of the Evo News folder including a trail slash.
define('ROOT', dirname(__FILE__).'/');

// This is to detect if you have changed the filename so it will work
// what ever you call it. However, you will have to edit it if you are
// including this file into another. The ? is required at the end.
define('BASE', basename(__FILE__).'?');

// You should not need to change this, it will show information like
// queries which is very dangerous.
define('DEBUG', true);

// Include $info Configeration
require ROOT .'ne.inc/config.inc.php';
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

// Check for SEO Friendly 
if($ne->is_num($ne->input['newsid'])) {
	$dal->query("SELECT `n`.*, `u`.*, `c`.* FROM `{$prefix}news` `n` LEFT JOIN `{$prefix}categories` USING(`cid`) LEFT JOIN `{$prefix}users` `u` ON(`n`.`uid` = `u`.`id`) WHERE `n`.`nid` = ".$en->input['nid'], __FILE__, __LINE__);
	$input['act'] = $dal->numrows() == 1 ? 'article' : $input['act'];
}

// NE Action
$modules = array(
	'article',
	'archive',
	'feeds',
	'headlines',
);
$ne->input['act'] = strtolower($ne->input['act']);
$action = in_array($ne->input['act'], $modules) ? $ne->input['act'] : 'headlines';
require ROOT ."ne.inc/{$action}.ne.php";

// Close up Shop
$dal->close();
?>