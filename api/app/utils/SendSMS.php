<?php

class SendSms {
    public static function send_message($text, $to = '387603383856', $from = 'SSSD') {
        $url = "https://ibu-sms.adnan.dev/api/sms";
        $postData = array(
            "access_token" => hash('sha256', DB_USER),
            "to" => $to,
            "from" => $from,
            "text" => $text
        );
        
        $handler = curl_init();
        
        curl_setopt($handler, CURLOPT_URL, $url);
        curl_setopt($handler, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($handler, CURLOPT_POST, true);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
    
        $response = curl_exec($handler);
        return json_decode($response, true);
    }
}