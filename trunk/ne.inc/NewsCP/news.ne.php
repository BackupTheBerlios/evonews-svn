<?php
/*
   News Evolution v0.01 Beta
   -------------------------
   by Matthew Wells
   (c) 2005 Matthew Wells
   http://www.newsevo.com/
   -------------------------
   Time: Fri, 29 July 2005 22:59:01 GMT
   Email: matt@newsevo.com
   License: http://www.newsevo.com/license.html
   -------------------------
   > News Article Management
   > Written by Matt Wells
   > Date Started: 29th July 2005
   > Version Number: 1.0.0
   > Time Taken: 7 hours
*/

if(!defined('ROOT')) { die(); }

function check_article() {
	global $info, $input, $ne, $user;

	$info['max_title_length'] = $info['max_title_length'] ? $info['max_title_length'] : 100;
	$info['max_article_length'] = $info['max_article_length'] ? $info['max_article_length'] : 2140000;

	// Check News Article Title
	if(strlen(time($input['title'])) > 1) { $ne->error('no_title'); }
	if(strlen($input['title']) > $info['max_title_length']) { $ne->error('title_long'); }
	// Check Short/Preview News Article
	if(strlen(trim($_POST['short'])) > 1) { $ne->error('no_short_news'); }
	if(strlen($_POST['short']) < $info['max_article_length']) { $ne->error('short_news_long'); }
	// Check Full News Article if there is one
	if(strlen(trim($_POST['full'])) > 1) {
		if(strlen(trim($_POST['full'])) < $info['max_article_length']) { $ne->error('full_news_long'); }
	}
}

if($input['code'] == 'new') {
	if($input['save'] == 1) {
		check_article();
		$dal->query("INSERT INTO `{$prefix}news` (`cid`, `uid`, `title`, `short`, `full`, `ip_address`, `date`) VALUES (".$input['cid'].", ".$user['uid'].", '".$input['title']."', '".$_POST['short']."', '".(strlen(trim($_POST['full'])) > 1 ? $_POST['full'] : NULL)."', '".$_SERVER['REMOTE_ADDR']."', ".time().")", __FILE__, __LINE__);
		$ne->message('news_posted', $skin->url.'&amp;act=news');
	}
	else {
		// Check Categories
		$dal->query("SELECT `c`.* FROM `{$prefix}categories` `c`", __FILE__, __LINE__);
		if($dal->numrows() < 1) { $ne->error('no_categories'); }
		while($cat = $dal->fetch()) { $categories[$cat['cide']] = $cat['title']; }

		$skin->form_start(array('act' => 'news', 'code' => 'new', 'save' => 1), 'newsform');
		$skin->table_start(array(array($lng['enter_news'], 2));
		$skin->add_td_row(array($lng['title'], $skin->form_input('title')));
		$skin->add_td_row(array($lng['category'], $skin->form_select('category', $categories)));
		$skin->add_td_row(array($lng['short_preview'].'<br />'.$lng['required'], $skin->form_textarea('short'));
		$skin->add_td_row(array($lng['full_article'].'<br />'.$lng['optional'], $skin->form_textarea('full'));
		$skin->form_end($lng['post_article']);
		$skin->table_end();
	}
}
elseif($input['code'] == 'edit' && $ne->is_num($input['nid'])) {
	if($input['save'] == 1) {
		check_article();
		$dal->query("UPDATE `{$prefix}news` `n` SET `n`.`title` = '".$input['title']."', `n`.`short` = '".$_POST['short']."', `n`.`full` = ".(strlen(trim($_POST['full'])) > 1 ? $_POST['full'] : NULL)."' WHERE `n`.`nid` = ".$input['nid'], __FILE__, __LINE__);
	}
	else {
		// Fetch News Article
		$news = $dal->fetch("SELECT `n`.* FROM `{$prefix}news` `n` WHERE `n`.`nid` = ".$input['nid'], __FILE__, __LINE__);
		if($dal->numrows() != 1) { $ne->error('no_article'); }

		// Check Categories
		$dal->query("SELECT `c`.* FROM `{$prefix}categories` `c`", __FILE__, __LINE__);
		if($dal->numrows() < 1) { $ne->error('no_categories'); }
		while($cat = $dal->fetch()) { $categories[$cat['cide']] = $cat['title']; }

		$skin->form_start(array('act' => 'news', 'code' => 'edit', 'nid' => $news['nid'], 'save' => 1), 'newsform');
		$skin->table_start(array(array($lng['enter_news'], 2));
		$skin->add_td_row(array($lng['title'], $skin->form_input('title', $news['title'])));
		$skin->add_td_row(array($lng['category'], $skin->form_select('category', $categories, $news['cid'])));
		$skin->add_td_row(array($lng['short_preview'].'<br />'.$lng['required'], $skin->form_textarea('short', $ne->htmlsafe($news['short'])));
		$skin->add_td_row(array($lng['full_article'].'<br />'.$lng['optional'], $skin->form_textarea('full', $ne->htmlsafe($news['full'])));
		$skin->form_end($lng['update_article']);
		$skin->table_end();
	}
}
elseif($input['code'] == 'delete' && preg_match('#[0-9, ]+#', $input['nid'])) {
	if($input['confirm'] == 1) {
		$dal->query("UPDATE `{$prefix}news` SET `deleted` = 'true' WHERE nid IN(".$input['nid'].")", __FILE__, __LINE__);
		$ne->message('articles_deleted');
	}
	else {
		$id = NULL;
		if(is_array($input['nid'])) {
			foreach($input['nid'] as $nid) {
				if($ne->is_num($nid)) { $id .= $nid == NULL ? $nid : ", {$nid}"; }
			}
		}
		else {
			if($ne->is_num($input['nid'])) { $id = $input['nid']; }
		}
		if($id == NULL) { $ne->error('no_article_selected'); }
		$ne->confirm('delete_article', array('act' => 'news', 'code' => 'delete', 'nid' => $id));
	}
}
else {
	// Find Page
	$page = (int) $input['page'];
	$page = $page < 1 ? 0 : $page - 1;
	$start = $page * $info['display'];
	// Filter Category
	$cid = (int) $input['cid'];
	if($cid > 0) { $where = " WHERE `n`.`cid` = {$cid}"; }
	// Select News Articles
	$dal->query("SELECT `n`.* FROM `{$prefix}news`{$where}", __FILE__, __LINE__);
	$news_articles = $dal->numrows();

	$skin->form_start(array('act' => 'news', 'code' => 'delete'));
	$skin->table_start(array($lng['options'], $lng['title'], $lng['category'], $lng['author'], $lng['date'], '{none}'));
	$dal->query("SELECT `n`.*, `c`.*, `u`.* FROM `{$prefix}news` LEFT JOIN `{$prefix}categories` USING(`cid`) LEFT JOIN `{$prefix}users` ON(`n`.`uid` = `c`.`uid`){$where} LIMIT $start, ".$info['display'], __FILE__, __FILE__);
	while($news = $dal->fetch()) { $skin->add_td_row(array('options', $news['title'], "<a href='{$skin->url}&amp;act=news&amp;cid=".$news['cid']."'>".$news['ctitle']."</a>", $news['name'], $ne->date($news['date']), $skin->form_checkbox('nid[]', false, $news['nid']))); }
	$skin->table_end();
	$skin->form_input('submit', $lng['delete_selected'], 'submit');
	$skin->form_end();
}
?>