<?php

namespace Sight\DocumentParser;

class LoopParser {

	public static $loopRegExp = null;

	public static function init() {
		self::$loopRegExp = "/^\s*" . Parser::$varRegExp . " +as +" . Parser::$varRegExp . "(?: += +" . Parser::$varRegExp . ")?" . Parser::$anythingRegExp . "$/";
	}
	
	public static function parse($result,$data) {
		
		if($matches = $result->stripOff("\s*@(\w+(?:[\w\.]+\w+)*) +as +@(\w+(?:[\w\.]+\w+)*)(?: += +@(\w+(?:[\w\.]+\w+)*))?")) {
			
			$count = count($matches);
			$varName = "";
			$varValue = "";

			$iterableVarName = $matches[1];
			$iterableVar = $data->get($iterableVarName);

			if($count === 3) {
				$varValue = $matches[2];
			} elseif($count === 4) {
				$varName = $matches[2];
				$varValue = $matches[3];
			}
			$subText = $result->unparsed;

			$lastSubResult = null;

			if(!is_array($iterableVar) && !is_object($iterableVar))
				$iterableVar = array();

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
				BlockParser::parseRightSide($lastSubResult,$data);
			}

			$result->unparsed = $lastSubResult->unparsed;

			//var_dump("after loop matching, text is now " . $result->unparsed);
			return true;
		}
		return false;
	}
	
}

LoopParser::init();
