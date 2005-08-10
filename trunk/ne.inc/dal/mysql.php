<?php
/*
   News Evolution v0.01 Beta
   -------------------------
   by Matthew Wells
   (c) 2005 Matthew Wells
   http://www.newsevo.com/
   -------------------------
   Time: Sat, 30 July 2005 14:59:01 GMT
   Email: matt@newsevo.com
   License: http://www.newsevo.com/license.html
   -------------------------
   > Database Controls
   > Written by Matt Wells
   > Data Started: 30th July 2005
   > Version Number: 1.0.0
   > Time Taken: 9 hours
*/

class mysql {
	var $count;
	var $link;
	var $obj;
	var $queries;
	var $result;

	// Connect to MySQL Sever and Select Database
	function connect() {
		global $info;

		$this->link = mysql_connect($info['db_hostname'], $info['db_username'], $info['db_password']) or $this->error(__FILE__, __LINE__);
		mysql_select_db($info['db_database'], $this->link) or $this->error(__FILE__, __LINE__);

		return $this->link;
	}

	// Sends a Query to the Database
	function query($query, $file, $line) {
		$this->result = mysql_query($query, $this->link) or $this->error($file, $line);

		if(DEBUG) { $this->queries[] = $query; }

		return $this->result;
	}

	// Fetches a row as an Associated Array
	function fetch($result = NULL, $file = NULL, $line = NULL) {
		$result = is_null($result) ? $this->result : is_resource($result) ? $result : $this->query($result, $file, $line);

		return mysql_fetch_assoc($result);
	}

	// Get Number of Rows in a Result
	function numrows($result = NULL) {
		$result = is_null($result) ? $this->result : $result;

		return mysql_num_rows($result);
	}

	// Close MySQL Connection
	function close() {
		mysql_close($this->link) or $this->error(__FILE__, __LINE__);
		if(DEBUG == true) { foreach($this->queries as $query) { echo "{$query}<br />\n"; } }
	}

	// Returns the text of the Error Message
	function error($file, $line) {
		trigger_error('<b>Database Error</b><br /><br />'.mysql_error($this->link).'<br /><br />In file &quot;<b>'.$file.'</b>&quot; on line <b>'.$line.'</b>', E_USER_ERROR);
	}

	// Makes MySQL Data Safe
	function add_slashes($string) {
		$string = str_replace('\\', '\\\\', $string);
        $string = str_replace('\'', '\\\'', $string);
        $string = str_replace("\r", '\r'  , $string);
        $string = str_replace("\n", '\n'  , $string);
        
        return $string;
	}

	// Fetchs an Array of Tables in a Database
	function table_names() {
		global $info;

		$result = mysql_list_tables($info['db_database'], $this->link);
		$num_tables = $this->numrows($result);
		for($i = 0; $i < $num_tables; $i++) { $tables[] = mysql_tablename($result, $i); }
		mysql_free_result($result);

		return $tables;
	}

	// Fetchs an Array of Fields in a Table
	function field_names($table) {
		$result = mysql_list_fields($info['db_database'], $table, $this->link);
		$num_fields = $this->numrows($result);
		for($i = 0; $i < $num_fields; $i++) { $fields[] = mysql_field_name($result, $i); }
		mysql_free_result($result);

		return $fields;
	}

	// Fetchs an Array of Fields in a result
	function result_fields($result = NULL) {
		$result = is_null($result) ? $this->result : $result;

		while($field = mysql_fetch_field($result)) { $fields[] = $field; }

		return $fields;
	}

	// Creates Backup SQL for a Table
	function show_table($table, $data = true) {
		global $info;

		$ctable = $this->fetch("SHOW CREATE TABLE `".$info['db_database'].".".$table."`", __FILE__, __LINE__);
		echo $ctable['Create Table'].";\n";

		if($data == true) {
			// Get the Table Data
			$this->query("SELECT * FROM {$table}", __FILE__, __LINE__);
	
			$num_rows = $this->numrows();
			if($num_rows < 1) { return true; }
	
			// Get Field Names
			$flist = NULL;
			$fields = $this->field_names($table);
			foreach($fields as $field) { $flist .= is_null($flist) ? "`{$field}`" : ", `{$field`}"; }
	
			// Get Table Data (Finally!)
			while($row = $this->fetch()) {
				for($i = 0; $i < $num_rows; $i++) {
					if(!isset($row[$fields[$i]])) { $dlist = is_null($dlist) ? 'NULL' : "{$dlist}, NULL"; }
					elseif($row[$fields[$i]])) {
						$cell = $this->add_slashes($row[$fields[$i]]);
						$dlist = is_null($dlist) ? "'{$cell}'" : "{$dlist}, '{$cell}'";
					}
					else { $dlist = is_null($dlist) ? "''" : "{$dlist}, ''"; }
				}
				echo "INSERT INTO `{$table}` ({$flist}) VALUES ({$dlist});\n";
			}
		}
		return true;
	}
}
?>