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
	function errorAdd($errorCode, $docPath) {
		$this->errorRoutes[$errorCode] = new Route("//",$docPath,function(){});
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
	function __construct($url,$path,$controller) {
		$this->url = $url;
		$this->path = $path;
		$this->controller = $controller;
		$this->httpCode = "200";
	}
	function matchesUrl($url) {
		return preg_match($this->url,$url) > 0;
	}
	function getPath($url) {
		if(!$this->matchesUrl($url))
			return null;

		preg_match($this->url,$url,$matches);
		$needles = array();
		for($j=0; $j<count($matches); $j++) {
			$needles[] = "$" . $j;
		}

		$result = str_replace($needles,$matches,$this->path);	
		
		return $result;
	}
	function runController($data,$url) {
		if(!$this->matchesUrl($url))
			return null;
		preg_match($this->url,$url,$urlMatches);

		$controller = $this->controller;
		$controller($data,$urlMatches);
	}
}
