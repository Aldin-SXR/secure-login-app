<?php

/* Require constants and libraries */
require_once __DIR__."/vendor/autoload.php";
require_once __DIR__."/config/Config.php";

/* Require files */
foreach (glob(__DIR__."/app/utils/*.php") as $util) {
    require_once $util;
}

foreach (glob(__DIR__."/app/routes/*.php") as $route) {
    require_once $route;
}

/**
 * Set required headers and handle unwanted method types.
 */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: authorization, Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS, PATCH');
header('Access-Control-Expose-Headers: Content-Range');
/* Handle the OPTIONS request */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    /* A 200 OK response to preflight with empty body */
    header('HTTP/1.0 200');
    die();
}


Flight::start();