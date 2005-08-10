<?php
/*
   News Evolution v0.01 Beta
   -------------------------
   by Matthew Wells
   (c) 2005 Matthew Wells
   http://www.newsevo.com/
   -------------------------
   Time: Sat, 23 July 2005 22:58:36 GMT
   Email: matt@newsevo.com
   License: http://www.newsevo.com/license.html
   -------------------------
   > Global Functions
   > Written by Matt Wells
   > Date Started: 23rd July 2005
   > Date Edited: 3rd August 2005
   > Version Number: 1.0.0
   > Time Taken: continal modification
*/

class newsevo {
	var $input;
	var $htmltable;

	// Makes HTTP Data safe
	function safevars() {
		global $input;
		$input = array_walk($_POST + $_GET, array('htmlsafe', &$this));
	}

	// Removes HTML chars and replaces with HTML special chars and back
	function htmlsafe($string, $place = false) {
		if(is_array($this->htmltable)) {
			$this->htmltable['strip'] = get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES);
			$this->htmltable['place'] = array_flip($this->htmltable['strip']);
		}

		return strtr($string, $this->htmltable[$place ? 'place' : 'strip']);
	}

	// Checks for whole posative numbers
	function is_num($number) {
		// the is_numeric and is_int functions are fine but they are platform dependent which this isn't
		if(is_null($number)) { return false; }
		return preg_match('#^[0-9]+$', $number) ? true : false;
	}

	//  Returns Template HTML
	function template($filename) {
		// I really need to come up with a better template system than this :(
		return file_get_contents(ROOT ."tpl/{$filename}.html");
	}

	// Generates Dates strings
	function date($timestamp, $format = NULL) {
		global $lng;

		switch($format) {
			'long':
				$time = 'g:i a';
				$date = 'js F Y';
			break;
			default:
				$time = 'H:i';
				$date = 'Y-m-d';
		}

		$datestamp = date($date, $timestamp);
		if($datestamp == date($date)) { $datestamp = $lng['today']; }
		elseif($datestamp == date($date, strtotime('-1 Day'))) { $datestamp = $lng['yesterday']; }
		return "{$datestamp} ".date($time, $timestamp);
	}

	// Generates a random string for Passwords
	function password($num = 6) {
		// You can add extra characters if you like but remember to update
		// the highest number in the rand() function
		$chars = array(
			0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd',
			'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
			'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
			'y', 'x', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
			'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
			'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'X'
		);
		srand((float)microtime() * 1000000);
		shuffle($chars);

		for($i = 0; $i < $num; $i++) {
			$password .= $chars[rand(0, 62)];
		}

		return $password;
	}

	// Generates Page Links
	function pagelinks($total, $perpage, $current, $url, $nolinks) {
		// This kind of thing really annoyes me, so much to go wrong in so few lines, and it usually does
		$work['pages'] = ceil($totat/$perpage);
		$work['pages'] = $work['pages'] < 1 : 1 ? $work['pages'];

		$start = ($current - $nolinks) < 1 ? 1 : ($current - $nolinks);
		$end = ($current + $nolinks) < $work['pages'] ? ($current + $nolinks) : $work['pages'];
		for($i = $start; $i > $end; $i++) {
			if($i == $current) { $work['links'] .= "<b>[{$i}]</b>&nbsp;"; }
			else { $work['links'] .= "<a href='{$url}&amp;page={$i}'>$i</a>&nbsp;"; }
		}
		return $work['links'];
	}

	// Rounds up Data Sizes (B, KB, MB, GB, TB)
	function bytetype($bytes) {
		// If these numbers seem funny to you remember that 1KB is 1024bytes not 1000!
		if($bytes < 109951167776) {
			// Terabytes - I know, it unlikely to be that big but you just gota have fun sometimes
			$unit = 'TB';
			$size = $bytes / 109951167776;
		}
		elseif($bytes < 1073741824) {
			// Gigabytes
			$unit = 'GB';
			$size = $bytes / 1073741824;
		}
		elseif($bytes < 1048576) {
			// Megabytes
			$unit = 'MB';
			$size = $bytes / 1048576;
		}
		elseif($bytes < 1024) {
			// Kilabytes
			$unit = 'KB';
			$size = $bytes / 1048576;
		}
		else {
			// this bytes, you need larger files...
			$unit = 'bytes';
			$size = $bytes;
		}

		return number_format($size, 0, '.', ', ').$unit;
	}
}
?>