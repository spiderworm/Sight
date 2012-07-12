<?

namespace Sight\DocumentParser;

require_once("Sight.DocumentParser.IncludeParser.php");
require_once("Sight.DocumentParser.TemplateParser.php");
require_once("Sight.DocumentParser.LoopParser.php");
require_once("Sight.DocumentParser.VariableParser.php");
require_once("Sight.DocumentParser.ConditionParser.php");
require_once("Sight.DocumentParser.BlockParser.php");
require_once("Sight.DocumentParser.SightCommandParser.php");

class Parser {

	public static $varRegExp = "@(\w+(?:[\w\.]+\w+)*)";
	public static $anythingRegExp = "((?:.|\s)*)";
	public static $rightSideRegExp;
	public static $textRegExp;
	public static $chunkEndRegExp;

	public static function init() {		
		self::$rightSideRegExp = "/^(?:\s*@\{" . self::$anythingRegExp . "$| *([^\r\n\f]*)@\{" . self::$anythingRegExp . "$|([^\r\n\f]*)" . self::$anythingRegExp . "$)/";
		self::$textRegExp = "/^([^@]*)" . self::$anythingRegExp . "$/";
		self::$chunkEndRegExp = "/^@\}" . self::$anythingRegExp . "$/";
	}

	public static function parse($result,$data,$includes = array()) {
		foreach($includes as $includePath) {
			IncludeParser::parseInclude($result,$data,$includePath);
		}
	
		while($result->unparsed != "") {
			self::parseText($result,$data);

			if(BlockParser::parseBlockEnd($result,$data)) {
				break;
			}

			if(SightCommandParser::parse($result,$data)) {
				continue;
			}				
				
			if(BlockParser::parse($result,$data)) {
				continue;	
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
			
			if(self::parseEscapedSymbol($result)) {
				continue;
			}
			
		}
		
		if(!is_null($result->defaultDocumentTemplatePath) && $result->documentTemplateParsed == false) {
			TemplateParser::parseTemplate($result,$data,$result->defaultDocumentTemplatePath);
		}
	}

	function parseEscapedSymbol($result) {
		if(preg_match("/^@(.)" . self::$anythingRegExp . "$/",$result->unparsed,$matches) > 0) {
			$result->contents .= $matches[1];
			$result->unparsed = $matches[2];
			return true;
		}
		return false;
	}
	
	function parseText($result) {
		preg_match(self::$textRegExp,$result->unparsed,$matches);
		$result->contents .= $matches[1];
		$result->unparsed = $matches[2];
	}
	
}

Parser::init();


?>