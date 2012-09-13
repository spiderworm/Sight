<?php

namespace Sight\DocumentParser;

class VariableParser {

	public static $varSetterRegExp;
	public static $varEchoRegExp;
	public static $siteRootEchoRegExp;
	public static $defaultDocumentTemplateSetterRegExp;

	public static function init() {
		self::$varSetterRegExp = Parser::$varRegExp . " *= *";
		self::$varEchoRegExp = Parser::$varRegExp;
		self::$siteRootEchoRegExp = "@site\.root[\/\\\]?";
		self::$defaultDocumentTemplateSetterRegExp = "@defaultDocumentTemplate +([^\s]*)";
	}
	
	public static function parse($result,$data) {
		
		if($matches = $result->stripOff(self::$defaultDocumentTemplateSetterRegExp)) {
			$result->defaultDocumentTemplatePath = $matches[1];
			return true;
		}
		
		if($matches = $result->stripOff(self::$varSetterRegExp)) {
			$value = new SubResult($result->unparsed);
			BlockParser::parseRightSide($value,$data);
			$data->set($matches[1],$value->contents);
			$result->unparsed = $value->unparsed;
			return true;
		}
		
		if($matches = $result->stripOff(self::$siteRootEchoRegExp)) {
			$result->contents .= $data->get("site.root");
			return true;
		}
		
		if($matches = $result->stripOff(self::$varEchoRegExp)) {
			$result->contents .= strval($data->get($matches[1]));
			return true;
		}
		
		return false;
	}
	
}

VariableParser::init();
