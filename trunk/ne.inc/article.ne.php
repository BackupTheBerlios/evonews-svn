<?php
/*
   News Evolution v0.01 Beta
   -------------------------
   by Matthew Wells
   (c) 2005 Matthew Wells
   http://www.newsevo.com/
   -------------------------
   Time: Thu, 4 August 2005 00:18:01 GMT
   Email: matt@newsevo.com
   License: http://www.newsevo.com/license.html
   -------------------------
   > Show News Article and Comments
   > Written by Matt Wells
   > Date Started: 4th Auguest 2005
   > Date Edited: 4th Augest 2005
   > Version Number: 1.0.0
   > Time Taken: 3 hours
*/

if(!defined('ROOT')) { die('Can I help you?');

// Get Article Data
$article = $dal->fetch();
// Check if it has been deleted (you wouldn't be here if it didn't exist)
if($article['deleted'] == true) { $ne->error('no_article'); }

// Add new Comment
if($input['save'] == 1 && $info['comment_on'] == true && $article['comments'] == true) {
	// Check Comment Text
	if(strlen(trim($input['comment'])) < $info['min_comment_length']) { $ne->error('comment_short'); }
	if(strlen($info['comment'])) > $info['max_comment_length']) { $ne->error('comment_long'); }

	// Check Check Name
	$input['name'] = trim($input['name']);
	if(strlen($input['name'])) { $ne->error('name_short'); }
	if(strlen($input['name'])) { $ne->error('name_long'); }
	$dal->query("SELECT `n`.`name` FROM `{$prefix}users` `u` WHERE `n`.`name` = '".$input['name']."'", __FILE__, __LINE__);
	if($dal->numrows() == 1) { $ne->error('name_inuse'); }

	// Save Data
	$dal->query("INSERT INTO `{$prefix}comments` (`nid`, `date`, `name`, `comment`, `moderated`, `deleted`) VALUES (".$article['nid'].", ".time().", '".$input['name']."', '".$input['comment']."', '".$info['comment_mod']."', 'false')", __FILE__, __LINE__);
	$ne->message('comment_created');
}

$article['date'] = $ne->date($article['date']);

// Show Comments if they are allowed
$comments = NULL;
if($article['comments'] == true && $info['comment_on'] == true) {
	$dal->query("SELECT `c`.* FROM `{$prefix}comments` `c` WHERE `c`.`deleted` = 'false' AND `c`.`nid` = ".$article['nid'], __FILE__, __LINE__);
	$total_comments = $dal->numrows();
	$total_pages = ceil($total_comments / $info['display']);

	// Show Comments if there are any
	if($total_pages > 0) {
		// What page are we on?
		$page = (int) $input['page'];
		$page = $page < 1 ? 0 : $page - 1;
		$offset = $page * $info['display'];

		// Start comments
		eval("\$comments = \"".$ne->template('comment_header')."\";");
		$dal->query("SELECT `c`.* FROM `{$prefix}comments` `c` WHERE `c`.`deleted` = 'false' AND `c`.`nid` = ".$article['nid']." ORDER BY `c`.`date` DESC LIMIT {$offset}, ".$info['display'], __FILE__, __LINE__);
		while($comment = $dal->fetch()) {
			$comment['date'] = $ne->date($comment['date']);
			if($info['comment_html'] == true) { $comment['comment'] = $ne->htmlsafe($comment['comment'], true); }
			eval("\$comments .= \"".$ne->template('comment')."\";");
		}
		// Page Links
		if($total_pages > 1) { $pagelinks = $ne->pagelinks($total_comments, $info['display'], $page, dirname($_SERVER['REQUEST_URI']).'/'. BASE .'nid='.$article['nid'], $info['pagelinks']); }
		eval("\$comments .= \"".$ne->template('comment_footer')."\";");
	}
	eval("\$comments .= \"".$ne->template('comment_form')."\";");
}
eval("\$output = \"".$ne->template('full_article')."\";");
?>