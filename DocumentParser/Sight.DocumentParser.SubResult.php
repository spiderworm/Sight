<?

namespace Sight\DocumentParser;

class SubResult {
	var $before = "";
	var $after = "";
	var $contents = "";
	var $unparsed = "";
	var $defaultDocumentTemplatePath = null;
	var $documentTemplateParsed = false;
	
	function __construct($text) {
		$this->unparsed = $text;
	}
	function add($parseSubResult) {
		$this->before = $parseSubResult->before . $this->before;
		$this->after = $this->after . $parseSubResult->after;
		$this->contents = $this->contents . $parseSubResult->contents;
	}
	function toString() {
		return $this->before . $this->contents . $this->after;
	}
}

?>