<?php

namespace Sight;

class Document {
	private $path;
	
	function __construct($path) {
		$this->path = $path;
	}

	function send($data) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimetype = finfo_file($finfo, $path);

		header('Content-type: ' . $mimetype);
		//header('Content-Disposition: attachment; filename='.basename($this->path));
		readfile($this->path);
	}
}