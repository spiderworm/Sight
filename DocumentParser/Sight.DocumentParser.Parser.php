<?

namespace Sight\DocumentParser;

require_once("Sight.DocumentParser.IncludeParser.php");
require_once("Sight.DocumentParser.TemplateParser.php");
require_once("Sight.DocumentParser.LoopParser.php");
require_once("Sight.DocumentParser.VariableParser.php");
require_once("Sight.DocumentParser.ConditionParser.php");

class Parser {

	public static $varRegExp = "@(\w+[\w\.]+\w+)";
	public static $anythingRegExp = "((?:.|\s)*)";
	public static $rightSideRegExp;
	public static $textRegExp;
	public static $chunkEndRegExp;

	public static function init() {
		self::$rightSideRegExp = "/^(?:\s*@\{" . self::$anythingRegExp . "$| *([^\r\n\f]*)@\{" . self::$anythingRegExp . "$|([^\r\n\f]*)" . self::$anythingRegExp . "$)/";
		self::$textRegExp = "/^([^@]*)" . self::$anythingRegExp . "$/";
		self::$chunkEndRegExp = "/^@\\}" . self::$anythingRegExp . "$/";
	}

	public static function parse($result,$data,$includes = array()) {
		foreach($includes as $includePath) {
			IncludeParser::parseInclude($result,$data,$includePath);
		}
	
		while($result->unparsed != "") {
			self::parseText($result,$data);

			if(self::parseEscapedSymbol($result)) {
				continue;
			}
			
			if(self::parseChunkEnd($result,$data)) {
				break;
			}
			
			if(ConditionParser::parse($result, $data)) {
				continue;
			}
				
			if(LoopParser::parse($result, $data)) {
				continue;
			}

			if(IncludeParser::parse($result, $data)) {
				continue;
			}
			
			if(TemplateParser::parse($result, $data)) {
				$result->documentTemplateParsed = true;
				continue;
			}
			
			if(VariableParser::parse($result, $data)) {
				continue;
			}
		}
		
		if(!is_null($result->defaultDocumentTemplatePath) && $result->documentTemplateParsed == false) {
			//echo "<p>no template found, going with default template which is " . $result->defaultDocumentTemplatePath . "</p>";
			TemplateParser::parseTemplate($result,$data,$result->defaultDocumentTemplatePath);
		}
	}
	
	public static function parseRightSide($subResult,$data) {
		//var_dump("parsing right side");
		if(preg_match(self::$rightSideRegExp,$subResult->unparsed,$matches) > 0) {
			if(count($matches) == 2) {
				$subResult->unparsed = $matches[1];
				self::parse($subResult,$data);
			} else if(count($matches) == 4) {
				$subResult->unparsed = $matches[2] . $matches[3];
				self::parse($subResult,$data);
			} else if(count($matches) == 6) {
				$subResult->unparsed = $matches[4];
				self::parse($subResult,$data);
				$subResult->unparsed .= $matches[5];
			}
		}
	}

	function parseEscapedSymbol($result) {
		if(preg_match("/^@@" . self::$anythingRegExp . "$/",$result->unparsed,$matches) > 0) {
			$result->contents .= "@";
			$result->unparsed = $matches[1];
			return true;
		}
		return false;
	}
	
	function parseText($result) {
		preg_match(self::$textRegExp,$result->unparsed,$matches);
		$result->contents .= $matches[1];
		$result->unparsed = $matches[2];
		//var_dump("after pre-variable matching, text is now " . $result->unparsed);
	}
	
	function parseChunkEnd($result) {
		if(preg_match(self::$chunkEndRegExp,$result->unparsed,$matches) > 0) {
			$result->unparsed = $matches[1];
			//var_dump("chunk end found, text is now " . $result->unparsed);
			return true;
		}
		return false;
	}

	
}

Parser::init();


?>