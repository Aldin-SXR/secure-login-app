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
    $user_service = new UserService();
    $data = Flight::request()->data->getData();
    $user_service->register($data);
});

/**
 * @OA\Post(
 *     path="/login",
 *     tags={"auth"},
 *     summary="Log in (validate user credentials).",
 *     description="Validate the user's credentials in the system.",
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
});

/**
 * @OA\Post(
 *     path="/sms",
 *     tags={"auth"},
 *     summary="Send and SMS code.",
 *     description="Send a 6-digit authentication code via SMS.",
 *     operationId="sms",
 *     @OA\Response(
 *         response=200,
 *         description="Successful SMS sending."
 *     ),
 *     @OA\RequestBody(
 *         description="SMS code model",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/SMSCodeModel")
 *     )
 * )
 */
Flight::route("POST /sms", function() {
    $user_service = new UserService();
    $data  = Flight::request()->data->getData();
    $user_service->send_sms_code($data);
});

/**
 * @OA\Post(
 *     path="/verify",
 *     tags={"auth"},
 *     summary="Verify login attempt.",
 *     description="Verify an attempted login by using an OTP, or SMS-generated code.",
 *     operationId="verify",
 *     @OA\Response(
 *         response=200,
 *         description="Successful verification and login."
 *     ),
 *     @OA\RequestBody(
 *         description="Verification model",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/VerificationModel")
 *     )
 * )
 */
Flight::route("POST /verify", function() {
    $user_service = new UserService();
    $data  = Flight::request()->data->getData();
    $user_service->verify_authentication($data);
});
 
/**
 * @OA\Get(
 *     path="/captcha",
 *     tags={"captcha"},
 *     summary="Return reCaptcha site key.",
 *     description="Send the corresponding reCaptcha site key to the client.",
 *     operationId="captcha",
 *     @OA\Response(
 *         response=200,
 *         description="Site key succesfully sent."
 *     )
 * )
 */
Flight::route("GET /captcha", function() {
    $captcha_service = new CaptchaService();
    $captcha_service->return_site_key();
});

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