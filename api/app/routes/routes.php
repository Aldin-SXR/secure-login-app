<?php

/**
 * Login system routes.
 */

/**
 * @OA\Post(
 *     path="/register",
 *     tags={"auth"},
 *     summary="Register",
 *     description="Register for the system",
 *     operationId="register",
 *     @OA\Response(
 *         response="200",
 *         description="Successful registration."
 *     ),
 *     @OA\Response(
 *         response="400",
 *         description="Insecure password (breached)."
 *     ),
 *     @OA\RequestBody(
 *         description="Registration model",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/RegisterModel")
 *     )
 * )
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

/**
 * @OA\Post(
 *     path="/login",
 *     tags={"auth"},
 *     summary="Log in",
 *     description="Log in to the system",
 *     operationId="login",
 *     @OA\Response(
 *         response=200,
 *         description="Successful login."
 *     ),
 *     @OA\RequestBody(
 *         description="Login model",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/LoginModel")
 *     )
 * )
 */
Flight::route("POST /login", function() {
    $user_service = new UserService();
    $data = Flight::request()->data->getData();
    $user_service->log_in($data);

    if (array_key_exists('captcha_response', $data)) {
        $response = ReCaptcha::validate($data['captcha_response']);
        Flight::json([ "response" => $response ]);
    }
    // Flight::json([ "message" => "Welcome to the system, ".$raw_data["username"] ]);
});


Flight::route("POST /validate", function() {
    $otp = OTPGenerator::generate_otp();
    $text = 'Your one-time password is: '.$otp."\n".'It will expire in 30 seconds.'."\n";
    $response = SendSms::send_message($text);
    Flight::json($response);
});


Flight::route("GET /", function() {
    $base_url = "";
    if ((empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off") && defined('ENV') && ENV == 'PROD') {
        $base_url = "https://" . $_SERVER['HTTP_HOST'] . "/";
    } else {
        $base_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    Flight::set('flight.views.path', __DIR__.'/../../docs');
    Flight::render('index', array(
        'api_swagger_url' => $base_url
    ));
});