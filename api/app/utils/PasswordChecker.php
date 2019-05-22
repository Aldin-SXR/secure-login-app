<?php

class PasswordChecker {
    /* Send a GET request via CURL */
    private static function send_get_request($url) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url
        ]);
        $result = curl_exec($ch);
        return $result;
    }

    /* Check for password breaches via "Have I been pwned?" API */
    public static function password_is_breached($password) {
        $hashed_password = sha1($password);
        $response = self::send_get_request(HIBP_URL.substr($hashed_password, 0, 5));
        /* Try to match the password hash with the received response */
        if (strpos($response, strtoupper(substr($hashed_password, 5))) !== FALSE) {
            return true;
        }
        return false;
    }
}