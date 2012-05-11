<?

namespace Sight\DocumentParser;

class LoopParser {

	public static $loopRegExp = null;

	public static function init() {
		self::$loopRegExp = "/^\s*" . Parser::$varRegExp . " +as +" . Parser::$varRegExp . "(?: += +" . Parser::$varRegExp . ")?" . Parser::$anythingRegExp . "$/";
	}
	
	public static function parse($result,$data) {
		if(preg_match(self::$loopRegExp,$result->unparsed,$matches) > 0) {

			$iterableVarName = $matches[1];
			$iterableVar = $data->get($iterableVarName);
			$varName = $matches[3] != "" ? $matches[2] : "";
			$varValue = $matches[3] != "" ? $matches[3] : $matches[2];
			$subText = $matches[4];

			$lastSubResult = null;
			
			//if(is_array($iterableVar)) {
				foreach($iterableVar as $key=>$value) {
					if($varName != "")
						$data->set($varName,$key);
					$data->set($varValue,$value);
					$lastSubResult = new SubResult($subText);
					Parser::parseRightSide($lastSubResult,$data);
					$result->add($lastSubResult);
				}
			//}

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

?>