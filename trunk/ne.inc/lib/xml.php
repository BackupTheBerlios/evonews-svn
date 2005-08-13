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
   > Based on KXParse (my XML is bad)
   > Date Started: 11th Auguest 2005
   > Date Edited: 11th Augest 2005
   > Version Number: 1.0.0
   > Time Taken: 5 hours
*/

class xmlparser {
	var $document;
	var $file;
	var $parser;
	var $tag;
	var $xml;

	// == XML File Open and Save Functions == //

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

	// == XML to Tree Map == //

	// Start XML Map Parsing
	function mapper() {
		$this->document['pi'] = array();
		$this->document['children'] = array();
		$this->tag = &$this->document;

		$this->parser = xml_parser_create();
		xml_set_object($this->parser, &$this);
		xml_set_element_handler($this->parser, 'tag_open', 'tag_close');
		xml_set_character_data_handler($this->parser, 'tag_data');
		xml_set_processing_instruction_handler($this->parser, 'processing');
		xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, 0);
		$this->parse();
	}
	// XML Tag Open Handler
	function tag_open($parser, $tag, $attr) {
		if(!isset($this->tag['children'][$name])) { $this->tag['children'][$name] = array(); }

		$child = count($this->tag['children'][$name]) - 1;

		$this->tag['children'][$name][$child] = array(
			'attr'			=> $attr,
			'children'		=> array(),
			'parent'		=> &$this->tag,
			'internal'		=> false,
			'name'			=> $name,
		);
		$this->tag = &$this->tag['children'][$name][$lastchild];
	}
	// XML Tag Close Handler
	function tag_close() {
		if(substr($this->xml, xml_get_current_byte_index($this->parser) - 2, 1) == '/') { $this->tag['internal'] = true; }
		$this->tag = &$this->tag['parent'];
	}
	// Tag Data Handler
	function tag_data($parser, $data) {
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

	// == Tree Map to XML == //

	// Compile XML
	function compile() {
		$this->xml = NULL;

		$pikeys = array_keys($this->document['pi']);
		foreach($pikeys as $key) { $this->xml .= "<?{$key} ".$this->document['pi'][$key].'?>'; }
		unset($pikeys, $key);

		list($name) = array_keys($this->document['children']);
		$this->compile_tag(&$this->document['children'][$name][0]);
	}
	// Compile XML Tag
	function compile_tag(&$tag, $indent = NULL) {
		// Create Attributes if there are any
		$attrs = NULL;
		foreach($tag['attr'] as $key => $val) { $attrs .= " {$key}=\"{$val}\""; }

		// Create the Tag
		if($tag['internal'] == true) { $this->xml .= "\n{$indent}<".$tag['name'].$attrs.'/>'; }
		else {
			$this->xml .= "\n{$indent}<".$tag['name'].$attrs'>';
			if(isset($tag['text'])) { $this->xml .= $tag['text']; }
			$names = array_keys($tag['children']);
			foreach($names as $name)  {
				foreach($tag['children'][$name] as &$ctag) { $this->compile_tag(&$ctag, $indend.'	'); }
			}
			$this->xml .= '</'.$tag['name'].">\n";
		}
	}
}
?>