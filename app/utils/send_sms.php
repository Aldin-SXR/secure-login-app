<?php

require_once __DIR__."/../../config/Config.php";

class SendSms {
    public static function send_message() {
        $url = "https://rest.nexmo.com/sms/json";
        $postData = array(
            "api_key" => API_KEY,
            "api_secret" => API_SECRET,
            "to" => "387603383856",
            "from" => "NEXMO",
            "text" => "Omnia mea mecum porto."
        );
        
        $handler = curl_init();
        
        curl_setopt($handler, CURLOPT_URL, $url);
        curl_setopt($handler, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($handler, CURLOPT_POST, true);
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($handler);
        
        print_r($response);
    }
}