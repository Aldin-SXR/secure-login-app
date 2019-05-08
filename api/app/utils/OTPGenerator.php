<?php
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

/**
 * Generate a one-time password.
 */

class OTPGenerator {
    private static $otp;

    public static function generate_otp() {
        self::$otp = TOTP::create();
        self::$otp->setLabel('aldin@tribeos.io');
        $secret = Base32::encode(self::random_str(16));
        return self::$otp->now($secret);
    }

    public static function get_provisioning_link() {
        return self::$otp->getProvisioningUri();
    }

    private static function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    public static function get_qr_code($provisioning_link) {
        // NOT FOR PRODUCTION USE
        exec('qrencode -o - -s8 '.$provisioning_link.' | display');
    }
}