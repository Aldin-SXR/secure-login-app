<?php

/**
 * @OA\Schema(
 *      title="Initiate recovery model",
 *     description="Data used to initiate password recovery procedure.",
 * )
 */
class InitiateRecoveryModel {
    /**
     * @OA\Property(
     *     description="E-mail address used for password validation.",
     *     title="E-mail address",
     *     required=true
     * )
     *
     * @var string
     */
    public $email_address;
}