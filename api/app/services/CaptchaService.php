<?php

class CaptchaService {
    
    public function return_site_key() {
        JsonResponse::output([
            'site_key' => SITE_KEY
        ]);
    }
}