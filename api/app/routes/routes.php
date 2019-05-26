<?php

Flight::route("GET /", function() {
    $base_url = "";
    if (isset($_ENV['HEROKU_APP']) && $_ENV['HEROKU_APP']) {
        $base_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    } else if ((empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off") && defined('ENV') && ($_ENV['HEROKU_APP'])) {
        $base_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    } else {
        $base_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    Flight::set('flight.views.path', __DIR__.'/../../docs');
    Flight::render('index', array(
        'api_swagger_url' => $base_url
    ));
});