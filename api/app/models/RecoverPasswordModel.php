<?php

/**
 * @OA\Schema(
 *      title="Recover password model",
 *     description="Data used to reset the password.",
 * )
 */
class RecoverPasswordModel {
    /**
     * @OA\Property(
     *     description="Recovery token used to issue new password.",
     *     title="Recovery token",
     *     required=true
     * )
     *
     * @var string
     */
    public $recovery_token;
    /**
     * @OA\Property(
     *     description="New password",
     *     title="Password",
     *     required=true
     * )
     *
     * @var string
     */
    public $password;
}