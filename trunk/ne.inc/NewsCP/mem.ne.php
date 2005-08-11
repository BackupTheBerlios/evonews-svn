<?php
/*
   News Evolution v0.01 Beta
   -------------------------
   by Matthew Wells
   (c) 2005 Matthew Wells
   http://www.newsevo.com/
   -------------------------
   Time: Mon, 25 July 2005 23:40:39 GMT
   Email: matt@newsevo.com
   License: http://www.newsevo.com/license.html
   -------------------------
   > User Management
   > Written by Matt Wells
   > Date Started: 25th July 2005
   > Version Number: 1.0.0
   > Time Taken: 8 hours
*/

if(!defined('ROOT')) { die('Can I help you?'); }

function check_user() {
	global $input, $ne, $prefix;

	// Check Name
	$input['name'] = trim($input['name']);
	if(strlen($input['name']) < 1) { $ne->error('no_name'); }
	if(strlen($input['name']) > 50) { $ne->error('name_long'); }
	if($input['code'] != 'new') { $where = " AND `n`.`uid` != ".$input['uid']; }
	$dal->query("SELECT `uid` FROM `{$prefix}users` WHERE `name` = '".$input['name']."'{$where}", __FILE__, __LINE__);
	if($dal->numrows() != 0) { $ne->error('name_in_use'); }
	// Check User Title
	if(strlen(trim($input['title'])) > 50) { $ne->error('title_long'); }
	// Check Password
	if($input['code'] == 'new' && strlen($input['password']) < 6) { $ne->error('password_short'); }
	if(($input['code'] == 'edit' && isset($input['password'])) && strlen($input['password']) < 6) { $ne->error('password_short'); }
	if(isset($input['password']) && strlen($input['password'] > 10) { $ne->error('password_long'); }
	if(isset($input['password'])) { $input['password'] = md5($input['password']); }
	// Check Email
	$input['email'] = trim($input['email']);
	if(strlen($input['email']) < 8) { $ne->error('email_short'); }
	if(strlen($input['email']) > 100) { $ne->email('email_long'); }
}

if($input['code'] == 'list') {
	// Pagination
	$page = (int) $input['page'];
	$page = $page < 1 ? 0 : $page - 1;
	$start = $page * $info['display'];

	// Pull User from Table
	$dal->query("SELECT `u`.* FROM `{$prefix}users` LIMIT {$start}, ".$info['display'], __FILE__, __LINE__);
	$skin->form_start(array('act' => 'mem', 'code' => 'delete'), 'usersform');
	$skin->table_start(array($lng['options'], $lng['name'], $lng['email_address'], '{none}'));
	while($mem = $dal->fetch()) {
		$skin->add_td_row(array('options', $mem['name'], "<a href='mailto:".$mem['email']."'>".$mem['email']."</a>", $skin->form_checkbox('user_id[]', false, $mem['uid'])));
	}
	$skin->table_end();
	$skin->form_input('submit', $lng['delete_selected'], 'submit');
	$skin->form_end();
}
elseif($input['code'] == 'new') {
	if($input['save'] == 1) {
		check_user();
		$dal->query("INSERT INTO `{$prefix}users` (`name`, `utitle`, `password`, `email`, `permissions`) VALUES ('".$input['name']."', '".$input['title']."', '".$input['password']."', '".$input['email']."', '".serialize($input['permissions'])."')", __FILE__, __LINE__);
		$ne->message('user_created', $skin->url.'&amp;act=mem');
	}
	else {
		$skin->form_start(array('act' => 'mem', 'code' => 'new', 'save' => 1), 'newuserform');
		$skin->table_start(array(array($lng['create_user'], 2)));
		$skin->add_td_row(array($lng['name'], $skin->form_input('name')));
		$skin->add_td_row(array($lng['title'], $skin->form_input('title')));
		$skin->add_td_row(array($lng['password'], $skin->form_input('password', NULL, 'password')));
		$skin->add_td_row(array($lng['email_address'], $skin->form_input('email')));
		$skin->permissions();
		$skin->form_end($lng['save_user']);
	}
}
elseif($input['code'] == 'edit' || $ne->is_num($input['uid'])) {
	if($input['save'] == 1) {
		check_user();
		$dal->query("UPDATE `{$prefix}users`  SET `name` = '".$input['name']."', `utitle` = '".$input['title']."', `email` = '".$input['email']."', `permissions` = '".serialize($input['permissions'])."'".(isset($input['password']) ? ", `password` = '".$input['password']."'" : NULL)." WHERE `uid` = ".$input['uid'], __FILE__, __LINE__);
		$ne->message('user_updated', $skin->url.'&amp;act=mem');
	}
	else {
		$mem = $dal->fetch("SELECT `u`.* FROM `{$prefix}users` `u` WHERE `u`.`uid` = ".$input['uid'], __FILE__, __LINE__);
		$skin->form_start(array('act' => 'mem', 'code' => 'new', 'save' => 1, 'uid' => $mem['uid']), 'newuserform');
		$skin->table_start(array(array($lng['edit_user'], 2)));
		$skin->add_td_row(array($lng['name'], $skin->form_input('name', $mem['name'])));
		$skin->add_td_row(array($lng['title'], $skin->form_input('title', $mem['utitle'])));
		$skin->add_td_row(array($lng['password'], $skin->form_input('password', NULL, 'password')));
		$skin->add_td_row(array($lng['email_address'], $skin->form_input('email', $mem['email'])));
		$skin->permissions(unserialize($mem['permissions']));
		$skin->form_end($lng['update_user']);
	}
}
else {
	if(is_array($input['search'])) {
		$where = NULL;
		if(isset($input['search']['name'])) { $where = " `u`.`name` LIKE '".$input['search']['name']."'"; }
		if(isset($input['search']['title'])) { $where = (is_null($where) ? NULL : "{$where} AND")." `u`.`utitle` LIKE '".$input['search']['name']."'";
		if(is_null($where)) { $ne->error('no_search_data'); }
		$dal->query("SELECT `u`.* FROM `{$prefix}users` `u` WHERE{$where}", __FILE__, __LINE__);
		if($dal->numrows() < 1) { $ne->error('no_matches'); }
		$skin->table_start(array($lng['options'], $lng['name'], $lng['title'], $lng['email']));
		while($mem = $dal->fetch()) {
			$skin->add_td_row(array('options', $mem['name'], $mem['utitle'], "<a href='mailto:".$mem['email']."'>".$mem['email']."</a>"));
		}
		$skin->table_end();
	}
	else {
		$skin->form_start(array('act' => 'mem'), 'usersearchform');
		$skin->table_start(array(array($lng['find_user'])));
		$skin->add_td_row(array($lng['name'], $skin->form_input('search[name]'));
		$skin->add_td_row(array($lng['title'], $skin->form_input('search[title]'));
		$skin->form_end($lng['search']);
	}
}
?>