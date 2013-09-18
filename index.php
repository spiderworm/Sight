<?php

error_reporting(E_ALL);
ini_set('display_errors','1');

require_once("Sight.php");

$sight = new Sight("Sight!");

$sight->routeError(404,"pages/404.html");

$sight->setDefaultTemplate("includes/template.html");

$sight->data->set("true",true);
$sight->data->set("false",false);

$sight->route(
	"/^.*$/",
	"pages/$0.html"
);

$sight->respond();
