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