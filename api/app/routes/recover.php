<?php
/**
 *  Recovery endpoints.
 */

/**
 * @OA\Post(
 *     path="/recover",
 *     tags={"recover"},
 *     summary="Initiate recovery procedure.",
 *     description="Start the password recovery procedure, and send a recovery token",
 *     operationId="recover",
 *     @OA\Response(
 *         response=200,
 *         description="Recovery successfully initiated."
 *     ),
 *     @OA\RequestBody(
 *         description="Initiate recovery model",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/InitiateRecoveryModel")
 *     )
 * )
 */
Flight::route("POST /recover", function() {
    $user_service = new UserService();
    $data  = Flight::request()->data->getData();
    $user_service->initiate_recovery($data);
});

/**
 * @OA\Get(
 *     path="/recover/{recovery_token}/validate",
 *     tags={"recover"},
 *     summary="Validate recovery token.",
 *     description="Check whether a supplied recovery token is valid.",
 *     operationId="validateToken",
 *     @OA\Parameter(
 *         name="recovery_token",
 *         in="path",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Token successfully validated."
 *     )
 * )
 */
Flight::route("GET /recover/@recovery_token/validate", function($recovery_token) {
    $user_service = new UserService();
    $data  = [ 'recovery_token' => $recovery_token ];
    $user_service->validate_token($data);
});

/**
 * @OA\Post(
 *     path="/recover/password",
 *     tags={"recover"},
 *     summary="Recover password.",
 *     description="Supply the new password.",
 *     operationId="recoverPassword",
 *     @OA\Response(
 *         response=200,
 *         description="Password successfully reset."
 *     ),
 *     @OA\RequestBody(
 *         description="Password recovery model",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/RecoverPasswordModel")
 *     )
 * )
 */
Flight::route("POST /recover/password", function() {
    $user_service = new UserService();
    $data  = Flight::request()->data->getData();
    $user_service->reset_password($data);
});