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
     * )
     *
     * @var string
     */
    public $auth_type;
    /**
     * @OA\Property(
     *     description="Authentication code (6-digit number)",
     *     title="Authentication code",
     * )
     *
     * @var string
     */
    public $auth_code;
}