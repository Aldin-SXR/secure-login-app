<?php

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