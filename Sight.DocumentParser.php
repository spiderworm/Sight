<?php

namespace Sight;

require_once("DocumentParser/Sight.DocumentParser.SubResult.php");
require_once("DocumentParser/Sight.DocumentParser.Parser.php");
require_once("DocumentParser/Sight.DocumentParser.TemplateParser.php");

class DocumentParser {

	function parse($text,$data,$includes,$defaultTemplatePath = null) {
		$result = new DocumentParser\SubResult($text);
		$result->defaultDocumentTemplatePath = $defaultTemplatePath;
		DocumentParser\Parser::parse($result,$data,$includes);

		return $result->toString();
	}

}
