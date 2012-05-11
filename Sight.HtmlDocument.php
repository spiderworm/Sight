<?

namespace Sight;

class HtmlDocument {
	function __construct() {
		$this->text = "";
		$this->parser = new DocumentParser();
		$this->includes = array();
	}
	function setIncludes($includes) {
		$this->includes = $includes;
	}
	function setContents($contents,$data,$defaultTemplatePath) {
		$this->text = $this->parser->parse(
			$contents,
			$data,
			$this->includes,
			$defaultTemplatePath
		);
	}
	function send() {
		echo $this->text;
	}
}

?>
