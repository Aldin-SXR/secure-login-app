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
    private $username;
    /**
     * @OA\Property(
     *     description="Password",
     *     title="Password",
     * )
     *
     * @var string
     */
    private $password;
}