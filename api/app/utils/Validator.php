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
        }
        return $parsed_data;
    }
}