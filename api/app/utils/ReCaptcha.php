<?php

class ReCaptcha {
    public static function validate($captcha_response) {
        $post_data = http_build_query(
            array(
                'secret' => CAPTCHA_SECRET,
                'response' => $captcha_response,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        );
        $opts = array(
            'http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            )
        );
        $context  = stream_context_create($opts);
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $result = json_decode($response);
        if (!$result->success) {
            throw new Exception('CAPTCHA verification failed.', 1);
        }

        return $result;
    }
}