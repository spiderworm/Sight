<?

require_once("Sight.php");

$sight = new Sight("Sight!");

$defaultController = function() { return new stdClass(); };

$sight->routeError(404,"docs/404.html");

$sight->setDefaultTemplate("docs/includes/template.html");

$sight->route("/^$/","docs/index.html",$defaultController);
$sight->route("/^(.*)\/([^\/]*)$/","docs/$1/$2.html",$defaultController);
$sight->route("/^(.*)\/([^\/]*)\/?$/","docs/$1/$2/index.html",$defaultController);
$sight->route("/^.*$/","docs/$0.html",$defaultController);

$sight->respond();
