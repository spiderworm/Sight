<?

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
		if(preg_match(self::$blockRegExp,$result->unparsed,$matches) > 0) {			

			$sub = new SubResult($matches[1]);
			
			Parser::parse($sub,$data);

			$result->contents .= $sub->contents;
			$result->unparsed = $sub->unparsed;

			return true;
		}
		return false;
	}


	static public function parseRightSide($result,$data) {
		if(preg_match(self::$rightSideRegExp,$result->unparsed,$matches) > 0) {
			if(count($matches) == 2) {
				$result->unparsed = $matches[1];
				Parser::parse($result,$data);
			} else if(count($matches) == 4) {
				$result->unparsed = $matches[2] . $matches[3];
				Parser::parse($result,$data);
			} else if(count($matches) == 6) {
				$result->unparsed = $matches[4];
				Parser::parse($result,$data);
				$result->unparsed .= $matches[5];
			}
		}
	}

	static public function parseBlockEnd($result) {
		if(preg_match(self::$rightSideEndRegExp,$result->unparsed,$matches) > 0) {	
			$result->unparsed = $matches[1];
			return true;
		}
		return false;
	}
	
}

BlockParser::init();