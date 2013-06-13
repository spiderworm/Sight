<?php

namespace Sight;

class Response {

	public $data = NULL;
	public $httpCode = "200 OK";
	public $document = NULL;

	function __construct() {
		$this->headers = array();
	}
	function send($text=NULL) {
		header("HTTP/1.1 " . $this->httpCode);
		foreach($this->headers as $header)
			header($header);
		if($text !== NULL)
			echo $text;
		if($this->document !== NULL)
			$this->document->send($this->data);	
	}	
}

class EnhancedStdClass {
	function __toString() {
		$result = "";
		foreach($this as $key=>$value) {
			try {
				$result .= (string)$key;
			} catch(Exception $e) {
				$result .= "{unknown key}";	
			}
			$result .= " => ";
			try {
				$result .= (string)$value;
			} catch(Exception $e) {
				$result .= "{unknown value}";	
			}
			$result .= "\n";
		}
		return $result;
	}	
}

class ResponseData {
	function __construct() {
		$this->data = new \stdClass();
	}
	function set($path,$value) {
		$dataPath = explode(".",$path);

		$current = $this->data;

		for($i=0; $i<count($dataPath)-1; $i++) {
			$path = $dataPath[$i];
			if(!isset($current->$path) || !is_object($current->$path)) {
				$current->$path = new EnhancedStdClass();
			}
			$current = $current->$path;
		}

		$path = $dataPath[$i];
		$current->$path = $value;
	}
	function get($path) {
		$dataPath = explode(".",$path);

		$current = $this->data;

		for($i=0; $i<count($dataPath)-1; $i++) {
			$path = $dataPath[$i];
			$current = $this->getValueAt($current,$path);
			if(is_null($current))
				return null;
		}

		$path = $dataPath[$i];

		return $this->getValueAt($current,$path);
	}
	private function getValueAt($obj,$path) {
		if(is_object($obj)) {
			if(isset($obj->$path))
				return $obj->$path;
		} elseif(is_array($obj)) {
			if(isset($obj[$path]))
				return $obj[$path];
		}
		return null;
	}
	
}
