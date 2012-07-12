<?

namespace Sight\DocumentParser;

class IncludeParser {

	public static $includeRegex;

	public static function init() {
		self::$includeRegex = "/^\s*@include *" . Parser::$anythingRegExp . "$/";
	}
	
	public static function parse($result,$data) {
		if(preg_match(self::$includeRegex,$result->unparsed,$matches) > 0) {

			$rightSide = new SubResult($matches[1]);
			
			BlockParser::parseRightSide($rightSide,$data);
		
			$path = $rightSide->contents;

			$result->unparsed = $rightSide->unparsed;

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