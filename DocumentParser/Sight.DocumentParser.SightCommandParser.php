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
		if(preg_match(self::$parserOffRegExp,$result->unparsed,$matches) > 0) {			

			$contents = $matches[1];
			$unparsed = "";

			$i = strpos($matches[1],"@sight.parser.on");

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