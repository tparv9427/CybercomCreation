<?php
namespace EasyCart\Core;

class CSRF
{
    private const TOKEN_NAME = 'csrf_token';
    private const TOKEN_TIME = 'csrf_token_time';
    private const TOKEN_LIFETIME = 3600; // 1 hour

    /**
     * Generate or retrieve existing CSRF token
     */
    public static function generateToken(): string
    {
        // Start session if not started (safety check)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::TOKEN_NAME]) || self::isTokenExpired()) {
            $_SESSION[self::TOKEN_NAME] = bin2hex(random_bytes(32));
            $_SESSION[self::TOKEN_TIME] = time();
        }
        return $_SESSION[self::TOKEN_NAME];
    }

    /**
     * Validate a token against the session
     */
    public static function validateToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::TOKEN_NAME]) || self::isTokenExpired()) {
            return false;
        }
        return hash_equals($_SESSION[self::TOKEN_NAME], $token ?? '');
    }

    /**
     * Get HTML input field with token
     */
    public static function getTokenField(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Check if token is expired
     */
    private static function isTokenExpired(): bool
    {
        if (!isset($_SESSION[self::TOKEN_TIME])) {
            return true;
        }
        return (time() - $_SESSION[self::TOKEN_TIME]) > self::TOKEN_LIFETIME;
    }
}
