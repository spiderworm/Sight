<?php

namespace Sight;

require_once("DocumentParser/Sight.DocumentParser.SubResult.php");
require_once("DocumentParser/Sight.DocumentParser.Parser.php");
require_once("DocumentParser/Sight.DocumentParser.TemplateParser.php");

class DocumentParser {

	function parse($text,$data,$includes) {
		$result = new DocumentParser\SubResult($text);
		DocumentParser\Parser::parse($result,$data,$includes);

		if(!is_null($data->get('defaultTemplate')) && $result->parsedTemplate == false) {		
			DocumentParser\TemplateParser::parseTemplate($result,$data,$data->get('defaultTemplate'));
		}

		return $result->toString();
	}

}
