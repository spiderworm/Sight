<?php

namespace Sight\DocumentParser;

class ConditionParser {

	public static function init() {}
	
	public static function parse($result,$data) {
		if($matches = $result->stripOff("\s*@if *\(([^\)]*)\) *")) {

			$positive = ExpressionEvaluator::evaluate($matches[1],$data);

			if($positive) {
				BlockParser::parseRightSide($result,$data);
			} else {
				BlockParser::skipRightSide($result,$data);
			}

			return true;
		}
		return false;
	}
		
	public static function skip($result) {
		if($matches = $result->stripOff("\s*@if *\(([^\)]*)\) *")) {

			BlockParser::skipRightSide($result);

			return true;
		}
		return false;
	}
	
}

ConditionParser::init();
