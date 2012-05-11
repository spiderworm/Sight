<?

namespace Sight\DocumentParser;

class TemplateParser {

	public static $templateRegex;

	public static function init() {
		self::$templateRegex = "/^\s*@template *" . Parser::$anythingRegExp . "$/";
	}
	
	public static function parse($result,$data) {
		if(preg_match(self::$templateRegex,$result->unparsed,$matches) > 0) {

			$rightSide = new SubResult($matches[1]);
			
			Parser::parseRightSide($rightSide,$data);
		
			$path = $rightSide->contents;
			
			//echo "<p>found template, parsing template file " . $path . "</p>";
			
			self::parseTemplate($result,$data,$path);
			
			$result->unparsed = $rightSide->unparsed;
			
			return true;
		}
		return false;
	}
	
	public static function parseTemplate($result,$data,$templatePath) {
		if(file_exists($templatePath)) {
	
			$templateResult = new SubResult("");
			$templateResult->contents = "@contents";
		
			$data->set("contents","@contents");
		
			$templateResult->contents = "";
			$templateResult->unparsed = file_get_contents($templatePath);
			
		
			Parser::parse($templateResult,$data);
			$split = preg_split("/@contents/",$templateResult->contents);
			$templateResult->before = $templateResult->before . $split[0];
			$templateResult->after = (isset($split[1]) ? $split[1] : "") . $templateResult->after;
			$templateResult->contents = "";
		
			$result->add($templateResult);
			
		}
		
	}
	
}

TemplateParser::init();

?>