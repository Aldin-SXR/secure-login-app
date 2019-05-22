<?php
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

/**
 * Generate a one-time password.
 */

class OTPGenerator {
    /** @var TOTP OTP object. */
    private $otp;

    public function __construct($secret, $email) {
        $secret = Base32::encode($secret);
        $this->otp = TOTP::create($secret);
        $this->otp->setLabel($email);
    }

    public function get_otp() {
        return $this->otp->now();
    } 

    public function get_provisioning_link() {
        return $this->otp->getProvisioningUri();
    }

    public static function get_qr_code($provisioning_link) {
        // NOT FOR PRODUCTION USE
        exec('qrencode -o - -s8 '.$provisioning_link.' | display');
    }
}