<?php

namespace Sight\DocumentParser;

class VariableParser {

	public static $varSetterRegExp;
	public static $varEchoRegExp;
	public static $siteRootEchoRegExp;
	public static $defaultDocumentTemplateSetterRegExp;
	public static $varEqualityCompareRegExp;

	public static function init() {
		self::$varSetterRegExp = Parser::$varRegExp . " *= *";
		self::$varEchoRegExp = Parser::$varRegExp;
		self::$siteRootEchoRegExp = "@site\.root[\/\\\]?";
		self::$defaultDocumentTemplateSetterRegExp = "@defaultDocumentTemplate +([^\s]*)";
		self::$varEqualityCompareRegExp = Parser::$varRegExp . " *== *";
	}
	
	public static function parse($result,$data) {

		if($matches = $result->stripOff(self::$defaultDocumentTemplateSetterRegExp)) {
			$result->defaultDocumentTemplatePath = $matches[1];
			return true;
		}

		if($matches = $result->stripOff(self::$varEqualityCompareRegExp)) {
			$subResult = $result->pullSubParse();
			BlockParser::parseRightSide($subResult,$data);

			if(self::toString($data->get($matches[1])) == self::toString($subResult->contents)) {
				$result->contents .= "true";
			} else {
				$result->contents .= "false";
			}
			return true;
		}
		
		if($matches = $result->stripOff(self::$varSetterRegExp)) {
			$value = new SubResult($result->unparsed);
			BlockParser::parseRightSide($value,$data);
			$data->set($matches[1],$value->contents);
			$result->unparsed = $value->unparsed;
			return true;
		}
		
		if($matches = $result->stripOff(self::$varEchoRegExp)) {
			$value = $data->get($matches[1]);
			if (is_callable($value)) //is_object($value) && ($value instanceof Closure))
			{
				$result->contents .= $value();
			}
			else
			{
				$result->contents .= strval($value);
			}
			return true;
		}
		
		return false;
	}
	
	public static function skip($result) {

		if($matches = $result->stripOff(self::$defaultDocumentTemplateSetterRegExp)) {
			return true;
		}
		
		if($matches = $result->stripOff(self::$varEqualityCompareRegExp)) {
			BlockParser::skipRightSide($result);
			return true;
		}
		
		if($matches = $result->stripOff(self::$varSetterRegExp)) {
			BlockParser::skipRightSide($result);
			return true;
		}
		
		if($matches = $result->stripOff(self::$varEchoRegExp)) {
			return true;
		}
		
		return false;
	}

	private static function toString($input) {
		if(is_bool($input)) {
			if($input === true)
				return "true";
			if($input === false)
				return "false";
		} else {
			return (string)$input;
		}
	}

}

VariableParser::init();






class ExpressionEvaluator {

	public static function evaluate($expression,$data) {
		$result = false;

		$subResult = new SubResult($expression);

		VariableParser::parse($subResult,$data);

		return $subResult->contents === "true";
	}

}