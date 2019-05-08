<?php
/**
 * @OA\Schema(
 *      title="Login model",
 *     description="Data used for system login",
 * )
 */
class LoginModel {
    /**
     * @OA\Property(
     *     description="Username",
     *     title="Username",
     * )
     *
     * @var string
     */
    public $username;
    /**
     * @OA\Property(
     *     description="Password",
     *     title="Password",
     * )
     *
     * @var string
     */
    public $password;
    /**
     * @OA\Property(
     *     description="Google ReCaptcha response",
     *     title="Captcha response",
     * )
     *
     * @var string
     */
    public $captcha_response;
}