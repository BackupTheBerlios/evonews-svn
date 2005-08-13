<?php
/*
   News Evolution v0.01 Beta
   -------------------------
   by Matthew Wells
   (c) 2005 Matthew Wells
   http://www.newsevo.com/
   -------------------------
   Time: Sat, 13 August 2005, 17:00:03 GMT
   Email: matt@newsevo.com
   License: http://www.newsevo.com/license.html
   -------------------------
   > Database Controls
   > Written by Matt Wells
   > Date Started: 13th August 2005
   > Version Number: 1.0.0
   > Time Taken: 30 mins
*/

if(!defined('ROOT')) { die('Can I help you?');

// Run Query
$dal->query("SELECT `n`.*, `c`.* FROM `{$prefix}news` LEFT JOIN `{$prefix}categories` USING(`cid`) ORDER BY `n`.`date` DESC LIMIT ".$info['display'], __FILE__, __LINE__);

// Check Feed Type
if($code == 'atom') {
	require ROOT .'ne.inc/lib/atom.php';
	$atom = new atom;

	while($article = $dal->fetch()) {
		
	}
}
else {
	require ROOT .'ne.inc/lib/rss.php';
	$rss = new rss;

	while($article = $dal->fetch()) {
		$rss->add_item(array('title' => $article['title'], 'link' => 'shownews='.$article['nid'], 'description' => $article['short'], 'category' => $article['ctitle'], 'pubDate' => $article['date']));
	}
}
$dal->close();
exit();
?>