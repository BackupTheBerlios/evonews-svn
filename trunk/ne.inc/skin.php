<?php
/*
   News Evolution v0.01 Beta
   -------------------------
   by Matthew Wells
   (c) 2005 Matthew Wells
   http://www.newsevo.com/
   -------------------------
   Time: Mon, 25 July 2005 20:27:01 GMT
   Email: matt@newsevo.com
   License: http://www.newsevo.com/license.html
   -------------------------
   > News CP Skin
   > Written by Matt Wells
   > Date Started: 29th July 2005
   > Version Number: 1.0.0
   > Time Taken: 3 hours
*/

class skin {
	var $colspan;
	var $output;
	var $url;

	function form_skin() {
		global $session;

		$this->url = dirname($_SERVER['REQUEST_URI']).'/'. BASE .'hash='.$session['hash'];
	}

	// Form Elements
	function form_start($hidden = NULL, $name = 'evonewsform', $js) {
		global $session;

		$this->output .= "<form action='{$this->url}' method='post' name='{$name}' {$js}>\n";
		$this->output .= "<input type='hidden' name='hash' value='".$session['hash']."' />\n";
		$this->form_hidden($hidden);
	}

	function form_hidden($hidden = NULL) {
		if(is_array($hidden)) {
			foreach($hidden as $k => $v) {
				$this->output .= "<input type='hidden' name='{$k}' value='{$v}' />\n";
			}
		}
	}

	function form_end($text = NULL, $js = NULL) {
		if($text != NULL) {
			if($this->colspan > 1) { $colspan = " colspan='{$this->colspan}'"; }
			$this->output .= "	<tr>\n		<td align='center'{$colspan}><input type='submit' value='{$text}' {$js} accesskey='s' /></td>\n	</tr>\n";
		}
		$this->output .= "</table>\n</form>\n";
	}

	function form_end_alone($text = NULL, $js = NULL) {
		if($text != NULL) { $this->output .= "<div align='center'><input type='hidden' value='$text' {$js} accesskey='s' /></div>\n"; }
		$this->output .= "</form>\n";
	}

	function form_upload($name = 'FILE_UPLOAD', $js = NULL) {
		$this->output .= "<input type='file' size='30' name='{$name}' {$js} />\n";
	}

	function form_input($name, $value = NULL, $type = 'text', $js = NULL, $size = 30) {
		$this->output .= "<input type='{$type}' name='{$name}' value='{$value}' size='{$size}' {$js} />\n";
	}

	function form_textarea($name, $value = NULL, $cols = 60, $rows = 5, $wrap = 'soft') {
		$this->output .= "<textarea name='{$name}' cols='{$cols}' rows='{$rows}' wrap='{$wrap}'>{$value}</textarea>\n";
	}

	function form_select($name, $list = array(), $default = NULL, $js = NULL) {
		global $lng;

		$this->output .= "<select name='{$name}' {$js}>\n";
		if($default == NULL) { $this->output .= "	<option value='' selected='selected'>".$lng['select_one']."</option>>\n"; }
		foreach($list as $k => $v) {
			$selected = $k == $default ? " selected='selected'" : NULL;
			$this->output .= "	<option value='{$k}'>{$v}</option>\n";
		}
		$this->output .= "</select>\n";
	}

	function form_yes_no($name, $default = NULL) {
		global $lng;

		$yes = $lng['yes']." &nbsp; <input type='radio' name='{$name}' value='1' />\n";
		$no = $lng['no']." &nbsp; <input type='radio' name='{$name}' value='0' />\n";

		if($default == 'n') { $no = $lng['no']." &nbsp; <input type='radio' name='{$name}' value='false' />\n"; }
		elseif($default = 'y') { $yes = $lng['yes']." &nbsp; <input type='radio' name='{$name}' value='true' />\n"; }

		$this->output .= "{$yes}&nbsp;&nbsp;&nbsp;&nbsp;{$no}";
	}

	function form_checkbox($name, $checked = false, $value = 1) {
		if($checked == true) { $this->output .= "<input type='checkbox' name='{$name}' value='{$value}' checked='checked' />\n"; }
		else { $this->output .= "<input type='checkbox' name='{$name}' value='{$value}' />\n"; }
	}

	// Table Elements
	function table_start($headers = NULL) {
		$this->output .= "<table width='100%' cellspacing='0' cellpadding='5' align='center' boarder='0'>\n";
		if(is_array($headers)) {
			$this->output .= "	<tr>\n";
			foreach($headers as $header) {
				if(is_array($header)) {
					$width = $header[2] ? " width='{$header[2]}'" : NULL;
					$colspan = is_int($header[1]) ? " colspan='{$header[1]}'" : NULL;
					$header = $header[0];
				}
				else {
					$width = NULL;
					$colspan = NULL;
				}
				
				if($header != '{none}') { $this->output .= "		<th {$width}{$colspan}align='center'>{$header}</th>\n"; }
				
				$this->colspan++;
			}
			$this->output .= "	</tr>\n";
		}
	}

	function add_td_row($rowdata, $css = NULL, $valign = 'middle') {
		if(is_array($rowdata)) {
			$this->output .= "	<tr>\n";
			$this->colspan = count($rowdata);
			$css = $css != NULL ? " class='{$css}'" : NULL;
			foreach($rowdata as $data) {
				if(is_array($data)) {
					$colspan = $data[1] > 1 ? " colspan='{$data[1]}'" : NULL;
					$cell = $data[0];
				}
				else {
					$colspan = NULL;
					$cell = $data;
				}

				$this->output .= "		<td{$colspan} valign='{$valign}'{$css}>{$cell}</td>\n";
			}
		}
	}

	function table_end() {
		$this->output .= "</table>";
	}

	// Misc.
	// Menu Category
	function menu_links($title, $links) {
		$this->output .= "<table width='90%' cellspacing='0' cellpadding='5' align='center' boarder='0'>\n	<tr>\n		<th>{$title}</th>\n	</tr>\n";
		foreach($links as $link) { $this->output .= "	<tr>\n		<td><a href='{$this->url}&amp;{$link[1]}' target='body'>{$link[0]}</a></td>\n	</tr>\n"; }
		$this->output .= "</table>\n";
	}

	// Login Form
	function show_login($error = false) {
		global $lng;

		$this->form_start(NULL, 'loginform');
		$this->table_start(array(array(0 => $lng['login_form'], 1 => 2);
		if($error == true) { $this->add_td_row(array(array(0 => $lng['wrong_user_password'], 2))); }
		$this->add_td_row(array($lng['name'], $this->form_input('name'));
		$this->add_td_row(array($lng['password'], $this->form_input('password', NULL, 'password')));
		$this->form_end();
		$this->table_end();
	}

	// Control Panel Frames
	function frame_set() {
			$this->output = "<html>
<head><title>News Evoluation Control Panel</title></head>
<frameset cols='185, *' frameborder='no' border='0' framespacing='0'>
	<frame name='menu' noresize scrolling='auto' src='{$this->url}&act=menu'>
	<frame name='body' noresize scrolling='auto' src='{$this->url}&act=index'>
</frameset>
</html>";
	}
}
?>