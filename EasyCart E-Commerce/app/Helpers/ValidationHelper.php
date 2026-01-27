<?php

namespace EasyCart\Helpers;

/**
 * ValidationHelper
 * 
 * Input validation utilities
 */
class ValidationHelper
{
    /**
     * Validate email address
     * 
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password strength
     * 
     * @param string $password
     * @param int $minLength
     * @return bool
     */
    public static function validatePassword($password, $minLength = 6)
    {
        return strlen($password) >= $minLength;
    }

    /**
     * Validate phone number (10 digits)
     * 
     * @param string $phone
     * @return bool
     */
    public static function validatePhone($phone)
    {
        return preg_match('/^\d{10}$/', $phone) === 1;
    }

    /**
     * Sanitize input string
     * 
     * @param string $input
     * @return string
     */
    public static function sanitize($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate required fields
     * 
     * @param array $fields Array of field names
     * @param array $data Data array to validate
     * @return array Empty if valid, array of missing fields if invalid
     */
    public static function validateRequired($fields, $data)
    {
        $missing = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $missing[] = $field;
            }
        }
        return $missing;
    }
}
