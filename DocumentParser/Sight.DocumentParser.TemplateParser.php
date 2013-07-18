<?php

namespace Sight\DocumentParser;

class TemplateParser {

	public static $templateRegex;

	public static function init() {
		self::$templateRegex = "\s*@template *";
	}
	
	public static function parse($result,$data) {
		if($matches = $result->stripOff(self::$templateRegex)) {

			$rightSide = new SubResult($result->unparsed);

			BlockParser::parseRightSide($rightSide,$data);
		
			$path = $rightSide->contents;

			self::parseTemplate($result,$data,$path);

			$result->unparsed = $rightSide->unparsed;
			
			return true;
		}
		return false;
	}
	
	public static function skip($result) {
		if($matches = $result->stripOff(self::$templateRegex)) {
			BlockParser::skipRightSide($result);
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
