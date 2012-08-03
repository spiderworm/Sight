<?

namespace Sight\DocumentParser;

class IncludeParser {

	public static $includeRegex;

	public static function init() {
		self::$includeRegex = "/^\s*@include *" . Parser::$anythingRegExp . "$/";
	}
	
	public static function parse($result,$data) {
		if($matches = $result->stripOff("\s*@include *([^\r\n\f]*)")) {
			$rightSide = new SubResult($matches[1]);

			BlockParser::parseRightSide($rightSide,$data);

			$path = $rightSide->contents;

			self::parseInclude($result,$data,$path);

			return true;
		}

		return false;
	}

	public static function parseInclude($result,$data,$path) {
		if(file_exists($path)) {
			$includeResult = new SubResult(file_get_contents($path));
			Parser::parse($includeResult,$data);
			$result->add($includeResult);
			return true;
		}
		return false;
	}
	
}

IncludeParser::init();

?>