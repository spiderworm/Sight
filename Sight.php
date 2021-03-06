<?php

require_once("Sight.Routes.php");
require_once("Sight.Response.php");
require_once("Sight.DocumentParser.php");
require_once("Sight.HtmlDocument.php");

class Sight {

	private $root;
	private $origin;
	private $defaultTemplatePath;
	private $includes;
	
	function __construct($title) {
		$this->includes = array();
		$this->title = $title;

		$https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != false;

		$this->origin = 
			($https ? "https" : "http") . "://" .
			$_SERVER['HTTP_HOST'] .
			($https ?
				($_SERVER['SERVER_PORT'] !== "443" ? ":" . $_SERVER['SERVER_PORT'] : "") :
				($_SERVER['SERVER_PORT'] !== "80" ? ":" . $_SERVER['SERVER_PORT'] : "")
			)
		;

		preg_match("/^(.*)\/index\.php$/",$_SERVER['SCRIPT_NAME'],$matches);
		$this->root = $matches[1];

		$this->routes = new Sight\Routes();

		$this->data = new Sight\ResponseData();
		$this->data->set("site.title",$this->title);
		$this->data->set("site.root",$this->root);
		$this->data->set("site.origin",$this->origin);
		$this->data->set("defaultTemplate","");
	}
	
	function respond() {

		$request = array_key_exists('url',$_GET) ? $_GET['url'] : "";

		$routes = $this->routes->findRoutes($request);

		$route = NULL;
		$path = "";

		foreach($routes as $route) {

			$response = $route->getResponse($request,$this->root);

			if($response !== NULL) {
				$response->data = $this->data->copy();

				$result = $route->runController($request,$response);
				if($result === false) {
					$response = NULL;
				} else {
					break;
				}
			}

		}

		if($response === NULL) {
			$response = new Sight\Response();
			$response->httpCode = "500 Internal Service Error";
			$response->headers[] = "Status: 500 Internal Service Error";
			$response->send();
			exit('Sight: no route found and no proper 404');
		} else {
			//$data->set("document.path",$path);
			if($response->document)
				$response->document->setIncludes($this->includes);
			$response->send();
		}
	}
	
	function route($url,$docPath,$controller=null) {
		if(is_null($controller))
			$controller = function($o,$urlMatches) { };
		$this->routes->add($url,$docPath,$controller);
	}
	
	function routeError($errorCode,$docPath,$controller=null) {
		if(is_null($controller))
			$controller = function($o,$urlMatches) { };
		$this->routes->errorAdd($errorCode,$docPath,$controller);
	}
	
	function getUrl($path) {
		preg_match("/^(\\/|\\\)?(.*)$/",$path,$matches);
		if($matches[1] == "\\" || $matches[1] == "/") {
			return $this->root . $matches[2];
		}
	}
	
	function setDefaultTemplate($templateDocPath) {
		$this->data->set('defaultTemplate',$templateDocPath);
	}
	
	function addInclude($path) {
		$this->includes[] = $path;
	}
	
}
