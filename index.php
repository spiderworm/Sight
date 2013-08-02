<?php

require_once("Sight.php");

$sight = new Sight("Sight!");

$sight->routeError(404,"pages/404.html");

$sight->setDefaultTemplate("includes/template.html");

$sight->addInclude("includes/defaults.html");

$sight->route(
	"/^.*$/",
	"pages/$0.html"
);

$sight->respond();
