<?php
/*
   News Evolution v0.01 Beta
   -------------------------
   by Matthew Wells
   (c) 2005 Matthew Wells
   http://www.newsevo.com/
   -------------------------
   Time: Thu, 11 August 2005 00:00:50 GMT
   Email: matt@newsevo.com
   License: http://www.newsevo.com/license.html
   -------------------------
   > Parese XML Files
   > Written by Matt Wells
   > Based on KXParse/2.0 (my XML is bad)
   > Date Started: 11th Auguest 2005
   > Date Edited: 11th Augest 2005
   > Version Number: 1.0.0
   > Time Taken: 3 hours
*/

class xmlparser {
	var $document;
	var $dpos;
	var $file;
	var $parser;
	var $tag;
	var $xml;

	// == XML File Open and Save Functions == \\

	// Open and Cache XML File
	function open($filename) {
		$this->file = file_get_contents($filename);
	}
	// Save XML to File
	function save($filename) {
		$handle = fopen($filename, 'wb');
		$success = fwrite($handle, $this->file) == -1 ? false : true;
		fclose($handle);
		return $success;
	}

	// == XML to Tree Map == \\

	// Start XML Map Parsing
	function mapper() {
		$this->document['pi'] = array();
		$this->document['children'] = array();
		$this->tag = &$this->document;

		$this->parser = xml_parser_create();
		xml_set_object($this->parser, &$this);
		xml_set_element_handler($this->parser, 'tag_open', 'tag_close');
		xml_set_character_data_handler($this->parser, 'char_data');
		xml_set_processing_instruction_handler($this->parser, 'processing');
		xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, 0);
		$this->parse();
	}
	// XML Tag Open Handler
	function tag_open($parser, $tag, $attr) {
		if(!isset($this->tag['children']['?'])) { $this->tag['children']['?'] = array(); }
		if(!isset($this->tag['children'][$name])) { $this->tag['children'][$name] = array(); }

		$lastchild = count($this->tag['children'][$name]);
		$anonchild = count($this->tag['children']['?']);

		$this->tag['children'][$name][$lastchild] = array(
			'attribs'		=> $attr,
			'children'		=> array('?' => array()),
			'parent'		=> &$this->tag,
			'internal'		=> false,
			'name'			=> $name,
			'anon_index'	=> $anonchild,
			'index'			=> $lastchild
		);
		$this->tag['children']['?'][$anonchild] = &$this->tag['children'][$name][$lastchild];
		$this->tag = &$this->tag['children'][$name][$lastchild];
	}
	// XML Tag Close Handler
	function tag_close() {
		if(substr($this->xml, xml_get_current_byte_index($this->parser) - 2, 1) == '/') { $this->tag['internal'] = true; }
		$this->tag = &$this->tag['parent'];
	}
	// Character Data Handler
	function char_data($parser, $data) {
		$this->tag['text'] .= $data;
	}
	// Processing Instruction Handler
	function prossessing($parser, $target, $data) {
		$this->document['pi'][$target] = $data;
	}
	// Parse XML Map
	function parse() {
		xml_parse($this->parser, $this->xml);
		$this->xml = NULL;
		$this->tag = &$this->document;
	}

	// == Tree Map to XML -- \\

	// Compile XML
	function compile() {
		$this->xml = NULL;
		$this->dpos = 0;
		$this->tag = &$this->document;

		$pikeys = array_keys($this->document['pi']);
		foreach($pikeys as $key) {
			$this->xml .= "<?{$key} ".$this->document['pi'][$key].'?>';
			$this->dpos += strlen($key.$this->document['pi'][$key]) + 5;
		}
		unset($pikeys, $key);

		$this->compile_tag(&$this->document['children']['?'][0]);
	}
	// Compile XML Tag
	function compile_tag(&$tag) {
		if($this->dpost < strlen($this->xml)) {
			if($tag['internal'] == false) { $this->xml = substr_replace($this->xml, '<'.$tag['name'].'></'.$tag['name'].'>', $this->dpos, 0); }
			else { $this->xml = substr_replace($this->xml, '<'.$tag['name'].'/>', $this->dpos, 0); }
		}
		else {
			if($tag['internal'] == false) { $this->xml .= '<'.$tag['name'].'></'.$tag['name'].'>'; }
			else { $this->xml .= '<'.$tag['name'].'/>'; }
		}
	}
}
?>