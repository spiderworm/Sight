<?php

namespace Sight\DocumentParser;

class LoopParser {

	public static $loopRegExp = null;

	public static function init() {
		self::$loopRegExp = "/^\s*" . Parser::$varRegExp . " +as +" . Parser::$varRegExp . "(?: += +" . Parser::$varRegExp . ")?" . Parser::$anythingRegExp . "$/";
	}
	
	public static function parse($result,$data) {
		
		if($matches = $result->stripOff("\s*@(\w+(?:[\w\.]+\w+)*) +as +@(\w+(?:[\w\.]+\w+)*)(?: += +@(\w+(?:[\w\.]+\w+)*))?")) {
			
			$iterableVarName = $matches[1];
			$iterableVar = $data->get($iterableVarName);
			$varName = $matches[3] != "" ? $matches[2] : "";
			$varValue = $matches[3] != "" ? $matches[3] : $matches[2];
			$subText = $result->unparsed;

			$lastSubResult = null;

			foreach($iterableVar as $key=>$value) {
				if($varName != "")
					$data->set($varName,$key);
				$data->set($varValue,$value);
				$lastSubResult = new SubResult($subText);
				BlockParser::parseRightSide($lastSubResult,$data);
				$result->add($lastSubResult);
			}


			if(is_null($lastSubResult)) {
				$lastSubResult = new SubResult($subText);
				Parser::parseRightSide($lastSubResult,$data);
			}

			$result->unparsed = $lastSubResult->unparsed;

			//var_dump("after loop matching, text is now " . $result->unparsed);
			return true;
		}
		return false;
	}
	
}

LoopParser::init();
