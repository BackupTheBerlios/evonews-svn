<?php
/*
   News Evolution v0.01 Beta
   -------------------------
   by Matthew Wells
   (c) 2005 Matthew Wells
   http://www.newsevo.com/
   -------------------------
   Time: Sun, 31 July 2005 23:20:01 GMT
   Email: matt@newsevo.com
   License: http://www.newsevo.com/license.html
   -------------------------
   > Category Management
   > Written by Matt Wells
   > Date Started: 31th July 2005
   > Version Number: 1.0.0
   > Time Taken: 4 hours
*/

if(!defined('ROOT')) { die('Can I help you?'); }

function check_category() {
	global $info, $input, $ne;

	$info['max_title_length'] = $info['max_title_length'] ? $info['max_title_length'] : 100;

	// Check News Article Title
	if(strlen(trim($input['title'])) > 1) { $ne->error('no_title'); }
	if(strlen($input['max_title_title']) > $info['max_title_length']) { $ne->error('title_long'); }
}

if($input['code'] == 'new') {
	if($input['save'] == 1) {
		check_category();
		$dal->query("INSERT INTO `{$prefix}categories` (`ctitle`) VALUES ('".$input['title']."')", __FILE__, __LINEE__);
		$ne->message('category_created', $skin->url.'&amp;act=cat');
	}
	else {
		$skin->form_start(array('act' => 'cat', 'code' => 'new', 'save' => 1), 'categoryform');
		$skin->table_start(array($lng['create_category'], 2));
		$skin->add_td_row(array($lng['title'], $skin->form_input('title')));
		$skin->form_end($lng['save_category']);
	}
}
elseif($input['code'] == 'edit' && $ne->is_num($input['cid'])) {
	if($input['save'] == 1) {
		check_category();
		$dal->query("UPDATE `{$prefix}category` SET `ctitlec` = '".$input['title']."' WHERE `cid` = ".$input['cid'], __FILE__, __LINE__);
		$ne->message('category_updated', $skin->url.'&amp;act=cat');
	}
	else {
		$cat = $dal->fetch("SELECT `c`.* FROM `{$prefix}categories` WHERE `c`.`cid` = ".$input['cid'], __FILE__, __LINE__);
		if($dal->numrows() != 1) { $ne->error('no_category'); }
		$skin->form_start(array('act' => 'cat', 'code' => 'new', 'save' => 1, 'cid' => $cat['cid']), 'categoryform');
		$skin->table_start(array($lng['create_category'], 2));
		$skin->add_td_row(array($lng['title'], $skin->form_input('title', $cat['ctitle'])));
		$skin->form_end($lng['save_category']);
	}
}
elseif($input['code'] == 'delete' && preg_match('#[0-9, ]+#', $input['cid']) {
	if($input['confirm'] == 1) {
		$dal->query("DELETE FROM `{$prefix}categories` WHERE nid IN(".$input['cid'].")", __FILE__, __LINE__);
		$ne->message('categories_deleted');
	}
	else {
		$id = NULL;
		if(is_array($input['cid'])) {
			foreach($input['cid'] as $cid) {
				if($ne->is_num($cid)) { $id .= $cid == NULL ? $cid : ", {$cid}"; }
			}
		}
		else {
			if($ne->is_num($input['cid'])) { $id = $input['cid']; }
		}
		if($id == NULL) { $ne->error('no_article_selected'); }
		$ne->confirm('delete_article', array('act' => 'news', 'code' => 'delete', 'cid' => $id));
	}
}
else {
	// Pages
	$page = (int) $input['page'];
	$page = $page < 1 ? 1 : $page - 1;
	$start = $page * $info['display'];

	$skin->form_start(array('act' => 'cat', 'code' => 'delete'), 'categoryform');
	$skin->table_start(array($lng['options'], $lng['title'], '{none}'));
	$dal->query("SELECT `c`.* FROM `{$prefix}categories` ORDER BY `c`.`ctitle` ASC LIMIT {$start}, ".$info['display'], __FILE__, __LINE__);
	while($cat = $dal->fetch()) { $skin->add_td_row(array('options', $cat['ctitle'], $skin->form_checkbox('cid[]', false, $cat['cid']))); }
	$skin->table_end();
	$skin->form_input('submit', $lng['delete_selected'], 'submit');
	$skin->form_end();
}
?>