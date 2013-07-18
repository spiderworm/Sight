<?php

namespace Sight\DocumentParser;

class BlockParser {

	public static $blockRegExp;
	public static $rightSideRegExp;
	public static $rightSideEndRegExp;

	public static function init() {
		self::$blockRegExp = "/^\s*@\{" . Parser::$anythingRegExp . "$/";
		self::$rightSideRegExp = "/^(?:\s*@\{" . Parser::$anythingRegExp . "$| *([^\r\n\f]*)@\{" . Parser::$anythingRegExp . "$|([^\r\n\f]*)" . Parser::$anythingRegExp . "$)/";
		self::$rightSideEndRegExp = "/^@\}" . Parser::$anythingRegExp . "$/";
	}
	
	public static function parse($result,$data) {
		if($result->stripOff("\s*@\{")) {
			$sub = $result->pullSubParse();
			Parser::parse($sub,$data);
			$result->foldInSubParse($sub);
			return true;
		}
		return false;
	}

	public static function skip($result) {
		if($result->stripOff("\s*@\{")) {
			$sub = $result->pullSubParse();
			Parser::skip($sub);
			$result->foldInSubParse($sub);
			return true;
		}
		return false;

	}

	public static function skipBlock($result,$data) {
		if($result->stripOff("\s*@\{")) {
			$sub = $result->pullSubParse();
			Parser::skip($sub);
			$result->contents = "";
			$result->foldInSubParse($sub);
			return true;
		}
		return false;
	}


	static public function parseRightSide($result,$data) {
		if(self::parse($result,$data)) {
			return true;
		}
		
		if($matches = $result->stripOff("[^\r\n\f]*")) {
			$sub = new SubResult($matches[0]);
			Parser::parse($sub,$data);
			$result->unparsed = $sub->unparsed . $result->unparsed;
			$result->contents .= $sub->contents;
			return true;
		}

		return false;
	}

	static public function skipRightSide($result,$data) {
		if(self::skipBlock($result,$data)) {
			return true;
		}
		
		if($matches = $result->stripOff("[^\r\n\f]*")) {
			$sub = new SubResult($matches[0]);
			Parser::skip($sub);
			$result->foldInSubParse($sub);
			return true;
		}

		return false;
	}

	static public function parseBlockEnd($result) {
		if($result->stripOff("@\}")) {
			return true;
		}
		return false;
	}

	static public function skipBlockEnd($result) {
		return self::parseBlockEnd($result);
	}
	
}

BlockParser::init();