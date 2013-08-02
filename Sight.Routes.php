<?php

namespace Sight;

class Routes {
	function __construct() {
		$this->routes = array();
		$this->errorRoutes = array();
	}
	function add($url,$docPath,$controller) {
		$route = new Route($url,$docPath,$controller);
		$this->routes[] = $route;
	}
	function errorAdd($errorCode, $docPath, $controller) {
		$this->errorRoutes[$errorCode] = new Route("//",$docPath,$controller);
		$this->errorRoutes[$errorCode]->httpCode = $errorCode;
	}
	function findRoutes($request) {
		$result = array();
		for($i=0; $i<count($this->routes); $i++) {
			if($this->routes[$i]->matchesUrl($request) == true)
				$result[] = $this->routes[$i];
		}
		$result[] = $this->get404Route();
		return $result;
	}
	function get404Route() {
		if($this->errorRoutes[404]) {
			return $this->errorRoutes[404];
		}
		return null;
	}
}

class Route {
	public $httpCode;
	
	function __construct($url,$path,$controller) {
		$this->url = $url;
		$this->path = $path;
		$this->controller = $controller;
		$this->httpCode = "200";
	}
	public function getResponse($url,$webRoot) {
		$response = NULL;

		if(!$this->matchesUrl($url))
			return $response;

		if($url === "" || $url === "/") {

			$path = $this->getFilePath("index");
			if($path !== NULL && file_exists($path)) {
				$response = new Response();
				$response->httpCode = $this->httpCode;
				$response->document = new HtmlDocument($path);
			}

		} else {

			$path = $this->getFilePath($url);
			if($path !== NULL && file_exists($path)) {
				$response = new Response();
				$response->httpCode = $this->httpCode;
				$response->document = new HtmlDocument($path);
			}

			if($url[strlen($url)-1] === "/") {

				$path = $this->getFilePath($url . "index");
				if($path !== NULL && file_exists($path)) {
					$response = new Response();
				$response->httpCode = $this->httpCode;
					$response->document = new HtmlDocument($path);
				}

			} else {

				$path = $this->getFilePath($url . "/index");
				if($path !== NULL && file_exists($path)) {
					$response = new Response();
					$response->httpCode = "301 Moved Permanently"; 
					$response->headers[] = "Location: " . $webRoot . "/" . $url . "/";
				}

			}

		}

		return $response;

	}
	public function matchesUrl($url) {
		return preg_match($this->url,$url) > 0;
	}
	private function getFilePath($url) {
		$result = NULL;
		preg_match($this->url,$url,$matches);
		if($matches) {
			$needles = array();
			for($j=0; $j<count($matches); $j++) {
				$needles[] = "$" . $j;
			}

			$result = str_replace($needles,$matches,$this->path);	
		}
		return $result;
	}
	public function runController($url,$response) {
		if(!$this->matchesUrl($url))
			return null;
		preg_match($this->url,$url,$urlMatches);

		$controller = $this->controller;
		return $controller($response,$urlMatches);
	}
}
