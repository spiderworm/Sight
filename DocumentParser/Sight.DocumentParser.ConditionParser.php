<?php

namespace Sight\DocumentParser;

class ConditionParser {

	public static $ifRegex;
	public static $ifElseRegex;

	public static function init() {
		self::$ifRegex = "/^\s*" . Parser::$varRegExp . " +\? ?([^\r\n\f:]*): ?([^\r\n\f]*)" . Parser::$anythingRegExp . "$/";
		//self::$ifElseRegex = "/^ *: *" . Parser::$anythingRegExp . "$/";
	}
	
	public static function parse($result,$data) {
		if($matches = $result->stripOff("\s*@(\w+(?:[\w\.]+\w+)*) *\? *([^\r\n\f:]*) *: *([^\r\n\f]*)")) {

			$value = $data->get($matches[1]);

			if($value) {
				$rightSide = new SubResult($matches[2]);
			} else {
				$rightSide = new SubResult($matches[3]);
			}

			BlockParser::parseRightSide($rightSide,$data);

			$result->contents .= $rightSide->contents;

			return true;
		}
		return false;
	}
	
}

ConditionParser::init();
