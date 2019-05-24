<?php
/**
 * Various validation utilities.
 */

class Validator {

    public static function validate_data($data, $allowed_fields, $required_fields) {
        $parsed_data = [ ];
        /* Take in only white-listed fields */
        foreach ($allowed_fields as $field) {
            if (array_key_exists($field, $data) && isset($data[$field])) {
                /* Sanitize data, just in case */
                $parsed_data[$field] = filter_var($data[$field], FILTER_SANITIZE_STRING);
            }
        }
        /* Check for required fields */
        foreach ($required_fields as $field) {
            if (!array_key_exists($field, $parsed_data)) {
                JsonResponse::error('The field \''.$field.'\' is required.');
            }

            if (empty($parsed_data[$field]) || $parsed_data[$field] === '') {
                JsonResponse::error('The field \''.$field.'\' cannot be empty.');
            }
        }
        return $parsed_data;
    }

    public static function validate_email($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            JsonResponse::error('The provided e-mail address is invalid.');
        }
    }

    public static function validate_username($username) {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            JsonResponse::error('Only alphanumeric characters, dash (-) and underscore (_) are allowed in the username.');
        }
    }

    public static function validate_password($password) {
        if (strlen($password) < 6) {
            JsonResponse::error('The password must be at least 6 characters long.');
        }
        if (!preg_match('/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[-+_!@#$%^&*.,?])/', $password)) {
            JsonResponse::error('The password should contain at least one lowercase letter, one uppercase letter, one special character and one number.');
        }
        if (PasswordChecker::password_is_breached($password)) {
            JsonResponse::error('This password has been exposed in a data breach.');
        }
    }
}