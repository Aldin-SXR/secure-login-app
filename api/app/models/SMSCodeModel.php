<?php
/**
 * @OA\Schema(
 *      title="SMS code model",
 *     description="Data used for SMS authentication message.",
 * )
 */
class SMSCodeModel {
    /**
     * @OA\Property(
     *     description="Login hash, which is valid fo 30 seconds.",
     *     title="Login hash",
     *     required=true
     * )
     *
     * @var string
     */
    public $login_hash;
}