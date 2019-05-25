<?php
/**
 * @OA\Schema(
 *      title="Verification model",
 *     description="Data used for attempted login verification",
 * )
 */
class VerificationModel {
    /**
     * @OA\Property(
     *     description="Login hash, which is valid fo 30 seconds.",
     *     title="Login hash",
     * )
     *
     * @var string
     */
    public $login_hash;
    /**
     * @OA\Property(
     *     description="Authenticaiton type (SMS or OTP)",
     *     title="Authenticaiton type",
     *     required=true
     * )
     *
     * @var string
     */
    public $auth_type;
    /**
     * @OA\Property(
     *     description="Authentication code (6-digit number)",
     *     title="Authentication code",
     *     required=true
     * )
     *
     * @var string
     */
    public $auth_code;
    /**
     * @OA\Property(
     *     description="Remember this account's login (bypass authorization) for some time",
     *     title="Remember me",
     *     default=false
     * )
     *
     * @var boolean
     */
    public $remember_me;
}