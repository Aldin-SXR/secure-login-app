<?php

/**
 * Login system routes.
 */

Flight::route("POST /register", function() {
    $raw_data = Flight::request()->data->getData();
    /* Check for password */
    if (PasswordChecker::password_is_breached($raw_data["password"])) {
        Flight::json([ "message" => "This password has been breached. Please choose a different password." ], 400);
        die;
    }
    /* Send a welcome response */
    Flight::json([ "message" => "Successful registration. Welcome, ".$raw_data["name"] ]);
});

Flight::route("POST /login", function() {
    $raw_data = Flight::request()->data->getData();
    Flight::json([ "message" => "Welcome to the system, ".$raw_data["email"] ]);
});