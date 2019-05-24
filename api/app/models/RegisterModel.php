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
     *     description="Name and surname",
     *     title="Name",
     * )
     *
     * @var string
     */
    private $name;
    /**
     * @OA\Property(
     *     description="Username",
     *     title="Username",
     * )
     *
     * @var string
     */
    private $user_name;
    /**
     * @OA\Property(
     *     description="E-mail",
     *     title="E-mail address",
     * )
     *
     * @var string
     */
    private $email_address;
    /**
     * @OA\Property(
     *     description="Mobile phone number",
     *     title="Phone number",
     * )
     *
     * @var string
     */
    private $phone_number;
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