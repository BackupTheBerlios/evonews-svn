<?php
/*
   News Evolution v0.01 Beta
   -------------------------
   by Matthew Wells
   (c) 2005 Matthew Wells
   http://www.newsevo.com/
   -------------------------
   Time: Mon, 25 July 2005 23:56:10 GMT
   Email: matt@newsevo.com
   License: http://www.newsevo.com/license.html
   -------------------------
   > Home and Navigation
   > Written by Matt Wells
   > Date Started: 25th July 2005
   > Date Edited: 3rd August 2005
   > Version Number: 1.0.0
   > Time Taken: 3 hours
*/

if(!defined('ROOT')) { die(); }

if($input['act'] == 'menu') {
	// News Menu Links
	$news_links = array(
		array($lng['new_article'], 'act=news&amp;code=new'),
		array($lng['new_category'], 'act=cat&amp;code=new'),
		array($lng['manage_articles'], 'act=news'),
		array($lng['manage_categories'], 'act=cat'),
	);
	$skin->menu_links($lng['news'], $news_links);

	// News Feeds
/* Will be added later
	$feed_links = array(
		array($lng['feed_manager'], 'act=feed'),
		array($lng['new_feed'], 'act=feed&amp;code=new'),
		array($lng['view_feeds'], 'act=feed&amp;code=view'),
	);
	$skin->menu_links($lng['feeds'], $feed_links);
*/

	// Upload Manager
/* Will be added later
	$upload_links = array(
		array($lng['upload_manager'], 'act=up'),
	);
	$skin->menu_links($lng['upload_manager'], $upload_links);
*/

	// Users Menu
	$users_links = array(
		array($lng['new_user'], 'act=mem&amp;code=new'),
		array($lng['find_user'], 'act=mem'),
		array($lng['list_users'], 'act=mem&amp;code=list'),
	);
	$skin->menu_links($lng['user_controls'], $users_links);

	// System Menu
	$system_links = array(
		array($lng['general_config'], 'act=op'),
	);
	$skin->menu_links($lng['system_settings'], $system_links);

	// Database/SQL Menu
	$sql_links = array(
		array($lng['sql_toolbox'], 'act=sql'),
		array($lng['sql_backup'], 'act=sql&amp;code=backup'),
		array($lng['sql_runtime'], 'act=sql&amp;code=runtime'),
		array($lng['sql_system'], 'act=sql&amp;code=system'),
		array($lng['sql_processes'], 'act=sql&amp;code=processes'),
	);
	$skin->menu_links($lng['sql_management'], $sql_links);
}
elseif($input['act'] == 'index') {
	$latest_version = @file_get_contents('http://www.newsevo.com/version.txt');
	if($latest_version && $latest_version > VERSION) {
		$skin->table_start(array($lng['version_update']));
		$skin->add_td_row(array($lng['please_upgrade']));
		$skin->table_end();
	}
}
else { $skin->frame_set(); }
?>