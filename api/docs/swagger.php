<?php
header("Access-Control-Allow-Origin: *");

define('SERVER_ROOT', realpath(dirname(__FILE__)));

error_reporting(0);
ini_set('display_errors', 0);

$arr = explode("/", $_GET['send']);
if ($arr[0] == 'swagger.json') {

    require (SERVER_ROOT . "/../../vendor/autoload.php");
    $openapi = \OpenApi\scan([
        SERVER_ROOT . "/../app/models",
        SERVER_ROOT . "/../app/routes",
        SERVER_ROOT . "/../index.php",
        SERVER_ROOT . "/doc_setup.php"
    ]);

    $base_url = "";
    if (($_SERVER['HTTPS'] == 'on' || (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off")) && $_ENV['HEROKU_APP']) {
        $base_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    } else {
        $base_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    $openapi->servers[0]->url = str_replace("docs/swagger.json", "", $base_url);

    header('Content-Type: application/json');
    echo $openapi->toJson();
}
