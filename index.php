<?php

require_once("Sight.php");

$sight = new Sight("Sight!");

$sight->routeError(404,"docs/404.html");

$sight->setDefaultTemplate("docs/includes/template.html");

$sight->route(
	"/^.*$/",
	"docs/$0.html"
);

$sight->respond();
