<?php

namespace EasyCart\Core;

/**
 * Validation
 * 
 * Centralized validation class for common validation rules.
 */
class Validation
{
    /**
     * Validate email format
     * @param string $email
     * @return bool
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password strength
     * @param string $password
     * @param int $minLength
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validatePassword(string $password, int $minLength = 8): array
    {
        $errors = [];

        if (strlen($password) < $minLength) {
            $errors[] = "Password must be at least {$minLength} characters";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Validate quantity
     * @param mixed $quantity
     * @param int $max
     * @return array ['valid' => bool, 'value' => int, 'error' => string|null]
     */
    public static function validateQuantity($quantity, int $max = 999): array
    {
        $value = (int) $quantity;

        if ($value < 1) {
            return ['valid' => false, 'value' => 1, 'error' => 'Quantity must be at least 1'];
        }

        if ($value > $max) {
            return ['valid' => false, 'value' => $max, 'error' => "Maximum quantity is {$max}"];
        }

        return ['valid' => true, 'value' => $value, 'error' => null];
    }

    /**
     * Sanitize string input
     * @param string $input
     * @return string
     */
    public static function sanitizeString(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate required fields
     * @param array $data
     * @param array $requiredFields
     * @return array ['valid' => bool, 'missing' => array]
     */
    public static function validateRequired(array $data, array $requiredFields): array
    {
        $missing = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $missing[] = $field;
            }
        }

        return [
            'valid' => empty($missing),
            'missing' => $missing
        ];
    }
}
