<?

namespace Sight\DocumentParser;

class VariableParser {

	public static $varSetterRegExp;
	public static $varEchoRegExp;
	public static $siteRootEchoRegExp;
	public static $defaultDocumentTemplateSetterRegExp;

	public static function init() {
		self::$varSetterRegExp = "/^\s*" . Parser::$varRegExp . " *= *" . Parser::$anythingRegExp . "$/";
		self::$varEchoRegExp = "/^\s*" . Parser::$varRegExp . Parser::$anythingRegExp . "$/";
		self::$siteRootEchoRegExp = "/^@site\.root[\/\\]?" . Parser::$anythingRegExp . "$/";
		self::$defaultDocumentTemplateSetterRegExp = "/^\s*@defaultDocumentTemplate *([^\s]*)" . Parser::$anythingRegExp . "$/";
	}
	
	public static function parse($result,$data) {

		if(preg_match(self::$defaultDocumentTemplateSetterRegExp,$result->unparsed,$matches) > 0) {
			$result->defaultDocumentTemplatePath = $matches[1];
			$result->unparsed = $matches[2];
			return true;
		}
		if(preg_match(self::$varSetterRegExp,$result->unparsed,$matches) > 0) {
			$value = new SubResult($matches[2]);
			BlockParser::parseRightSide($value,$data);
			$data->set($matches[1],$value->contents);
			$result->unparsed = $value->unparsed;
			return true;
		}
		if(preg_match(self::$siteRootEchoRegExp,$result->unparsed,$matches) > 0) {
			$result->contents .= $data->get("site.root");
			$result->unparsed = $matches[1];
			return true;
		}
		if(preg_match(self::$varEchoRegExp,$result->unparsed,$matches) > 0) {
			$result->contents .= $data->get($matches[1]);
			$result->unparsed = $matches[2];
			return true;
		}
		return false;
	}
	
}

VariableParser::init();

?>