<?php
/**
 * @OA\Schema(
 *      title="Registration model",
 *     description="Data used for system registration",
 * )
 */
class RegisterModel {
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
     *     description="E-mail",
     *     title="E-mail address",
     * )
     *
     * @var string
     */
    private $email;
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