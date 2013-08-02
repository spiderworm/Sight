<?php

require_once("../Sight.php");

$sight = new Sight("Template Project");

$sight->routeError(404,"pages/404.html");

$sight->setDefaultTemplate("includes/template.html");

$sight->route(
	"/^.*$/",
	"pages/$0.html"
);

$sight->respond();
