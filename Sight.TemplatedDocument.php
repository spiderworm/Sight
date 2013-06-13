<?php

namespace Sight;

require_once('Sight.Document.php');

class TemplatedDocument extends Document {

	private $parser;
	private $includes;

	function __construct($path) {
		$this->template = file_get_contents($path);
		$this->parser = new DocumentParser();
		$this->includes = array();
	}

	function setIncludes($includes) {
		$this->includes = $includes;
	}

	function send($data) {
		if($data === NULL) {
			echo $this->template;
		} else {
			echo $this->parser->parse(
				$this->template,
				$data,
				$this->includes
			);
		}
	}
}
