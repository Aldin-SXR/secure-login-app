<?php
/**
 * Custom JSON responses.
 */

class JsonResponse {
    
    public static function output($data, $message = 'Successfully executed.', $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        /* Check for provided data */
        if (!is_null($data) && !empty($data)) {
            $response['data'] = $data;
        }
        $response['message'] = $message;
        echo json_encode($response);
        die();
    }

    public static function error($message, $code = 400) {
        http_response_code($code);
        header('Content-Type: application/json');
        $response['message'] = $message;
        echo json_encode($response);
        die();
    }
}