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

Flight::start();
