<?php
use Yubikey\Validate;

class U2FAuthentication {
    /** @var Validate Yubikey validator. */
    private $validator; 
    
    public function __construct() {
        $this->validator = new \Yubikey\Validate(YUBIKEY_SECRET, YUBIKEY_CLIENT_ID);
    }

    public function validate_otp($otp) {
        $response = $this->validator->check($otp);
        if ($response->success() === true) {
            return true;
        }
        return false;
    }
}