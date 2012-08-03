<?

namespace Sight\DocumentParser;

class SightCommandParser {

	public static $parserOffRegExp;

	public static function init() {
		$parseStatementEnd = "[^\w\.]+";
		//self::$parserOffRegExp = "/^\s*@sight.parser.off(?:" . $parseStatementEnd . "@sight.parser.on" . $parseStatementEnd . "$|" . $parseStatementEnd . "$|$)/";
		self::$parserOffRegExp = "/^\s*@sight\.parser\.off([^\w\.](?:.|\s)*)?$/";
	}
	
	public static function parse($result,$data) {
		if($matches = $result->stripOff("\s*@sight\.parser\.off([^\w\.]|$)")) {
			
			$contents = $matches[1] . $result->unparsed;
			$unparsed = "";

			$i = strpos($contents,"@sight.parser.on");

			if($i != false) {
				$unparsed = substr($contents,$i+16);
				$contents = substr($contents,0,$i);
			}
			
			$result->contents .= $contents;
			$result->unparsed = $unparsed;
			
			return true;
		}
		return false;
	}

}

SightCommandParser::init();