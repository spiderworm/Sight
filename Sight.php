<?

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
	
		$route = $this->routes->findRoute($request);
		$path = $route->getPath($request);
		
		if(!file_exists($path)) {
			$route = $this->routes->get404Route();
			$path = $route->getPath($request);
		}
		
		$data = new Sight\ResponseData();
		$data->set("site.title",$this->title);
		$data->set("site.root",$this->root);
		$data->set("model",$route->getModel($request));

		$response->document->setIncludes($this->includes);
		$response->document->setContents(
			file_get_contents($path),
			$data,
			$this->defaultTemplatePath
		);
		$response->document->send();
	}
	
	function route($url,$docPath,$controller) {
		if(is_null($controller))
			$controller = function() { return new stdClass(); };
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

?>
