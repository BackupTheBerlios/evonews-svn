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
   > Database Controls
   > Written by Matt Wells
   > Date Started: 4th Auguest 2005
   > Date Edited: 4th Augest 2005
   > Version Number: 1.0.0
   > Time Taken: 1 hour
*/

if(!defined('ROOT')) { die('Can I help you?'); }

if($input['save'] == 1) {
	$set['display'] = (int) $input['info']['display'];
	$set['max_article_length'] = (int) $input['info']['max_article_display'];
	$set['max_title_length'] = (int) $input['info']['max_title_length'];
	$set['pagelinks'] = (int) $input['info']['pagelinks'];

	if($set['display'] < 6) { $ne->error('display_low'); }
	if($set['max_article_length'] < 1000) { $ne->error('max_article_length_low'); }
	if($set['max_title_length'] < 10) { $ne->error('max_title_length_low'); }
	if($set['pagelinks'] < 1) { $ne->error('pagelinks_low'); }

	$dal->query("UPDATE `{$prefix}info` SET `display` = ".$set['display'].", `max_article_length` = ".$set['max_article_length'].", `max_title_length` = ".$set['max_title_length'].", `pagelinks` = ".$set['pagelinks'], __FILE__, __LINE__);
	$ne->message('options_updated', 'act=home');
}
else {
	$skin->form_start(array('act' => 'op', 'save' => 1), 'optionsForm');
	$skin->table_start(array(array($lng['options'], 2)));
	$skin->add_td_row(array($lng['display']."<br />\n".$lng['display_explain'], $skin->form_input('info[display]', $info['display'], 'text', NULL, 15)));
	$skin->add_td_row(array($lng['max_article_length']."<br />\n".$lng['max_article_length_explain'], $skin->form_input('info[max_article_length]', $info['max_article_length'], 'text', NULL, 15)));
	$skin->add_td_row(array($lng['max_title_length']."<br />\n".$lng['max_title_length_explain'], $skin->form_input('info[max_title_length]', $info['max_title_length'], 'text', NULL, 15)));
	$skin->add_td_row(array($lng['pagelinks']."<br />\n".$lng['pagelinks_explain'], $skin->form_input('info[pagelinks]', $info['pagelinks'], 'text', NULL, 15)));
}
?>