<?php

namespace EasyCart\Core;

/**
 * Validation — Centralized Validation Class
 * 
 * Common validation rules used across all controllers:
 * - Required fields, email, length, numeric, in-list
 * - Returns structured error arrays
 * 
 * Usage:
 *   $validator = new Validation($_POST);
 *   $validator->required('name', 'Name is required')
 *             ->email('email', 'Invalid email format')
 *             ->minLength('password', 6, 'Password too short');
 *   if ($validator->fails()) {
 *       $errors = $validator->errors();
 *   }
 */
class Validation
{
    /** @var array Input data to validate */
    private $data = [];

    /** @var array Collected error messages keyed by field */
    private $errors = [];

    /**
     * @param array $data Input data (e.g., $_POST, $_GET)
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // =========================================================================
    // Validation Rules (Fluent / Chainable)
    // =========================================================================

    /**
     * Field must be present and non-empty
     * 
     * @param string $field
     * @param string $message
     * @return self
     */
    public function required(string $field, string $message = ''): self
    {
        $value = $this->getValue($field);
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->addError($field, $message ?: "{$field} is required.");
        }
        return $this;
    }

    /**
     * Field must be a valid email
     * 
     * @param string $field
     * @param string $message
     * @return self
     */
    public function email(string $field, string $message = ''): self
    {
        $value = $this->getValue($field);
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $message ?: "Invalid email address.");
        }
        return $this;
    }

    /**
     * Field must have minimum length
     * 
     * @param string $field
     * @param int $min
     * @param string $message
     * @return self
     */
    public function minLength(string $field, int $min, string $message = ''): self
    {
        $value = $this->getValue($field);
        if ($value !== null && mb_strlen($value) < $min) {
            $this->addError($field, $message ?: "{$field} must be at least {$min} characters.");
        }
        return $this;
    }

    /**
     * Field must have maximum length
     * 
     * @param string $field
     * @param int $max
     * @param string $message
     * @return self
     */
    public function maxLength(string $field, int $max, string $message = ''): self
    {
        $value = $this->getValue($field);
        if ($value !== null && mb_strlen($value) > $max) {
            $this->addError($field, $message ?: "{$field} must not exceed {$max} characters.");
        }
        return $this;
    }

    /**
     * Field must be numeric
     * 
     * @param string $field
     * @param string $message
     * @return self
     */
    public function numeric(string $field, string $message = ''): self
    {
        $value = $this->getValue($field);
        if ($value !== null && $value !== '' && !is_numeric($value)) {
            $this->addError($field, $message ?: "{$field} must be a number.");
        }
        return $this;
    }

    /**
     * Field must be a positive integer
     * 
     * @param string $field
     * @param string $message
     * @return self
     */
    public function integer(string $field, string $message = ''): self
    {
        $value = $this->getValue($field);
        if ($value !== null && $value !== '' && !ctype_digit((string) $value)) {
            $this->addError($field, $message ?: "{$field} must be a whole number.");
        }
        return $this;
    }

    /**
     * Field value must be within allowed list
     * 
     * @param string $field
     * @param array $allowed
     * @param string $message
     * @return self
     */
    public function in(string $field, array $allowed, string $message = ''): self
    {
        $value = $this->getValue($field);
        if ($value !== null && $value !== '' && !in_array($value, $allowed, true)) {
            $this->addError($field, $message ?: "{$field} has an invalid value.");
        }
        return $this;
    }

    /**
     * Field must match another field (e.g., password confirmation)
     * 
     * @param string $field
     * @param string $matchField
     * @param string $message
     * @return self
     */
    public function matches(string $field, string $matchField, string $message = ''): self
    {
        $value1 = $this->getValue($field);
        $value2 = $this->getValue($matchField);
        if ($value1 !== $value2) {
            $this->addError($field, $message ?: "{$field} does not match {$matchField}.");
        }
        return $this;
    }

    /**
     * Field must be between min and max (numeric)
     * 
     * @param string $field
     * @param float $min
     * @param float $max
     * @param string $message
     * @return self
     */
    public function between(string $field, float $min, float $max, string $message = ''): self
    {
        $value = $this->getValue($field);
        if ($value !== null && $value !== '' && is_numeric($value)) {
            if ($value < $min || $value > $max) {
                $this->addError($field, $message ?: "{$field} must be between {$min} and {$max}.");
            }
        }
        return $this;
    }

    // =========================================================================
    // Result Methods
    // =========================================================================

    /**
     * Check if validation failed
     * @return bool
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Check if validation passed
     * @return bool
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Get all errors
     * @return array [field => [messages]]
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error message for a field
     * 
     * @param string $field
     * @return string|null
     */
    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Get first error message (any field)
     * @return string|null
     */
    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }
        return null;
    }

    /**
     * Get a validated/sanitized value
     * 
     * @param string $field
     * @param mixed $default
     * @return mixed
     */
    public function get(string $field, $default = null)
    {
        return $this->getValue($field) ?? $default;
    }

    /**
     * Get all input data
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    // =========================================================================
    // Internal Helpers
    // =========================================================================

    /**
     * Get value from input data
     * @param string $field
     * @return mixed|null
     */
    private function getValue(string $field)
    {
        return $this->data[$field] ?? null;
    }

    /**
     * Add an error message for a field
     * @param string $field
     * @param string $message
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }
}
