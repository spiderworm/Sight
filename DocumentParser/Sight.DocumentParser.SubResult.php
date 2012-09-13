<?php

namespace Sight\DocumentParser;

class SubResult {
	var $before = "";
	var $after = "";
	var $contents = "";
	var $unparsed = "";
	var $defaultDocumentTemplatePath = null;
	var $documentTemplateParsed = false;
	
	function __construct($text) {
		$this->unparsed = $text;
	}
	function add($parseSubResult) {
		$this->before = $parseSubResult->before . $this->before;
		$this->after = $this->after . $parseSubResult->after;
		$this->contents = $this->contents . $parseSubResult->contents;
	}
	function toString() {
		return $this->before . $this->contents . $this->after;
	}
	function isReady() {
		return $this->unparsed == "";
	}
	function stripOff($regex) {
		$chunkSize = 2000;
		$str = "";
		if(strlen($this->unparsed) > $chunkSize) {
			$str = substr($this->unparsed, 0, $chunkSize);
		} else {
			$str = $this->unparsed;
		}
		if(preg_match("/^" . $regex . "/", $str, $matches)) {
			$this->unparsed = substr($this->unparsed,strlen($matches[0]));
			return $matches;
		}
		return false;
	}
	function pullSubParse() {
		$sub = new SubResult($this->unparsed);
		$this->unparsed = "";
		return $sub;
	}
	function foldInSubParse($subResult) {
		$this->unparsed = $subResult->unparsed . $this->unparsed;
		$this->contents .= $subResult->contents;
	}
}
