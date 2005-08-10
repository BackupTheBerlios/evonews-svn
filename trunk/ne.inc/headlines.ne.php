<?php
/*
   News Evolution v0.01 Beta
   -------------------------
   by Matthew Wells
   (c) 2005 Matthew Wells
   http://www.newsevo.com/
   -------------------------
   Time: Thu, 4 August 2005 14:50:14 GMT
   Email: matt@newsevo.com
   License: http://www.newsevo.com/license.html
   -------------------------
   > Show Latest News/Headlines
   > Written by Matt Wells
   > Date Started: 4th Auguest 2005
   > Date Edited: 4th Augest 2005
   > Version Number: 1.0.0
   > Time Taken: 2 hours
*/

if(!defined('ROOT')) { die('Can I help you?'); }

$dal->query("SELECT `n`.* FROM `{$prefix}news` `n`", __FILE__, __LINE__);
$total_articles = $dal->numrows();
$total_pages = ceil($total_articles / $info['dislay']);

// What page are we on?
$page = (int) $input['page'];
$page = $page < 1 ? 0 : $page - 1;
$offset = $page * $info['display'];

// Filter Category
$where = NULL;
if($ne->is_num($input['cid'])) { $where = " WHERE `n`.`cid` = ".$input['cid']; }

$dal->query("SELECT `n`.*, `u`.*, `c`.* FROM `{$prefix}news` `n` LEFT JOIN `{$prefix}users` `u` USING(`uid`) LEFT JOIN `{$prefix}categories` `c` ON(`c`.`cid` = `n`.`cid`){$where} ORDER BY `n`.`date` DESC LIMIT {$offset}, ".$info['display'], __FILE__, __LINE__);
eval("\$output = \"".$ne->template('short_header')."\";");
while($article = $dal->fetch()) {
	$article['date'] = $ne->date($article['date']);
	eval("\$output .= \"".$ne->template('short_article')."\";");
}
$pagelinks = $ne->pagelinks($total_articles, $info['display'], $page, dirname($_SERVER['REQUEST_URI']).'/'. BASE .($ne->is_num($input['cid']) ? 'cid='.$input['cid'] : NULL), $info['pagelinks']);
eval("\$output .= \"".$ne->template('short_footer')."\";");
?>