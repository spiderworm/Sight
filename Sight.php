<?php

require_once("Sight.Routes.php");
require_once("Sight.Response.php");
require_once("Sight.DocumentParser.php");
require_once("Sight.HtmlDocument.php");

class Sight {

	private $root;
	private $defaultTemplatePath;
	private $includes;
	
	function __construct($title) {
		$this->includes = array();
		$this->title = $title;
		preg_match("/^(.*\/)index\.php$/",$_SERVER['SCRIPT_NAME'],$matches);
		$this->root = $matches[1];
		$this->routes = new Sight\Routes();
	}
	
	function respond() {

		$response = new Sight\Response();

		$request = array_key_exists('url',$_GET) ? $_GET['url'] : "";

		$routes = $this->routes->findRoutes($request);
		
		$route = NULL;
		$path = "";
		
		foreach($routes as $route) {
			if($request === "" || $request === "/") {

				$path = $route->getPath("index");
				if($path !== NULL && file_exists($path)) {
					$response->httpCode = $route->httpCode;
					break;
				}

			} else {

				$path = $route->getPath($request);
				if($path !== NULL && file_exists($path)) {
					$response->httpCode = $route->httpCode;
					break;
				}

				if($request[strlen($request)-1] === "/") {

					$path = $route->getPath($request . "index");
					if($path !== NULL && file_exists($path)) {
						$response->httpCode = $route->httpCode;
						break;
					}

				} else {

					$path = $route->getPath($request . "/index");
					if($path !== NULL && file_exists($path)) {
						$response->httpCode = "301 Moved Permanently"; 
						$response->headers[] = "Location: " . $this->root . $request . "/";
						break;
					}

				}

			}

		}

		if($route === NULL) {
			$response->httpCode = "500 Internal Service Error";
			$response->headers[] = "Status: 500 Internal Service Error";
			$response->send("no route found and no proper 404");
			exit('no route found and no proper 404');
		} else {

			$data = new Sight\ResponseData();		
			$data->set("site.title",$this->title);
			$data->set("site.root",$this->root);
			$data->set("defaultTemplate",$this->defaultTemplatePath);
			$data->set("document.path",$path);

			$doc = new Sight\HtmlDocument($path);
			$doc->setIncludes($this->includes);

			$response->data = $data;
			$response->document = $doc;

			$route->runController($request,$response);
			$response->send();

		}
	}
	
	function route($url,$docPath,$controller=null) {
		if(is_null($controller))
			$controller = function($o,$urlMatches) { };
		$this->routes->add($url,$docPath,$controller);
	}
	
	function routeError($errorCode,$docPath) {
		$this->routes->errorAdd($errorCode,$docPath);
	}
	
	function getUrl($path) {
		preg_match("/^(\\/|\\\)?(.*)$/",$path,$matches);
		if($matches[1] == "\\" || $matches[1] == "/") {
			return $this->root . $matches[2];
		}
	}
	
	function setDefaultTemplate($templateDocPath) {
		$this->defaultTemplatePath = $templateDocPath;
	}
	
	function addInclude($path) {
		$this->includes[] = $path;
	}
	
}
