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
   > Database Controls
   > Written by Matt Wells
   > Date Started: 29th July 2005
   > Date Edited: 4th August 2005
   > Version Number: 1.0.0
   > Time Taken: 8 hours
*/

if(!defined('ROOT')) { dir('Can I help you?'); }

@set_time_limit(1200);

function show_structure($fields, $rows) {
	$skin->table_start($fields);
	foreach($rows as $row) { $skin->add_td_rows($row); }
}

// Backup Database, most of the work is done in the DAL though...
if($input['code'] == 'backup') {
	if($input['download'] == 1) {
		@header("Pragma: no-cache");
		$filename = is_null($input['table']) ? 'ne_backup' : $input['table'];
		header("Content-Type: text/x-delimtext; name=\"{$filename}.sql\"");
		header("Content-disposition: attachment; filename={$filename}.sql");

		if(is_array($input['table'] && $inpput['all'] == 0) {
			foreach($input['table'] as $table) { $dal->show_table(urldecode($table)); }
		}
		else {
			$tmp_tables = $dal->table_names();
			$info['db_prefix'] = preg_grep('#', $info['db_prefix']);
			foreach($tmp_tables as $tbl) {
				// We only want NE tables
				if(preg_match('#^'.$info['db_prefix'].'#', $tbl)) { $dal->show_table($tbl, ($tbl == "{$prefix}sessions" ? false : true)); }
			}
		}
		$dal->close();
		exit();
	}
	else {
		$skin->form_start(array('act' => 'sql', 'code' => 'backup', 'download' => 1), 'backupform');
		$skin->table_start(array(array($lng['backup_all_tables'], NULL, '70%'), array($skin->form_yes_no('all', 'y'), NULL, '30%')));
		$tables = $dal->table_names();
		foreach($tables as $table) {
			if(preg_match('#^'.$info['db_prefix'].'#', $tbl)) { $skin->add_td_row(array($table, $skin->form_checkbox('table[]', true, urlencode($table)))); }
		}
		$skin->form_end($lng['backup']);
	}
	// That bit might look easy but I did have to add 3 new functions in the DAL aswell
}
elseif($input['code'] == 'optimize') {
	if(is_array($input['tables'] && $input['all'] != 1)) {
		foreach($input['tables'] as $tbl) {
			$tbl = urldecode($tbl);
			$tbl_list = is_null($tbl) ? "`{$tbl}`" : "{$tbl_list}, `{$tbl}`";
		}
	}
	else {
		$tbls = $dal->table_names();
		foreach($tbls as $tbl) {
			if(preg_match("#^".preg_quote($info['db_prefix'], '#'), $tbl)) { $tbl_list = is_null($tbl) ? "`{$tbl}`" : "{$tbl_list}, `{$tbl}`"; }
		}
	}
	$dal->query("OPTIMIZE TABLE {$tbl_list}", __FILE__, __LINE__);
	$skin->table_start($dal->result_fields());
	while($data = $dal->fetch()) {
		$row = array();
		foreach($data as $cell) { $row[] = htmlspecialchars($cell); }
		$skin->add_td_row($row);
	}
	$skin->table_end();
}
elseif($input['code'] == 'runtime' || $input['code'] == 'system' || $input['code'] == 'processes') {
	if($input['code'] == 'runtime') { $dal->query("SHOW STATUS"); }
	elseif($input['code'] == 'system') { $dal->query("SHOW VARIABLES"); }
	else { $dal->query("SHOW PROCESSLIST"); }

	$fields = $dal->result_fields();
	$skin->table_start($fields);

	while($data = $dal->fetch()) {
		$row = array();
		foreach($data as $cell) { $row[] = htmlspecialchars($cell); }
		$skin->add_td_row($row);
	}
	$skin->table_end();
	// I don't beleve that took me so long its only 10 lines of code :/
	// then again the CHM version of the MySQL Manual isn't that good at searching...
}
else {
	// Set total size
	$total_index_size = 0;
	$total_table_size = 0;

	$skin->table_start(array($lng['table_name'], $lng['rows'], $lng['table_size'], $lng['index_size'], '{none}'));

	$dal->query("SHOW TABLE STATUS `".$info['db_database']."`", __FILE__, __LINE__);
	while($t = $dal->fetch()) {
		// Only NE tables
		if(!preg_match('#^'.preg_quote($info['db_prefix'], '#').'#', $t['Name'])) { continue; }

		// Add to total size count
		$total_index_size += $t['Index_length'];
		$total_table_size += $t['Data_length'];

		// This tables sizes
		$index_size = $ne->bytetype($t['Index_length']);
		$table_size = $ne->bytetype($t['Data_length']);

		$name_encode = urlencode($t['Name']);

		$skin->add_td_row(array($t['Name'], $t['Rows'], $table_size, $index_size, $skin->form_checkbox('table[]', false, $name_encode)));
	}
}
// God bless the guys at phpMyAdmin for creating a script that helped me so much over
// the last few hours. I really should donate some money to them some time...
?>