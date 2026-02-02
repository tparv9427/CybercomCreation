<?php

namespace EasyCart\Core;

class Session
{
    /**
     * Initialize session
     */
    public static function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Initialize user_id if not set
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = null;
        }
    }

    /**
     * Get session value
     */
    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set session value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Check if session key exists
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session key
     */
    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy session
     */
    public static function destroy()
    {
        session_destroy();
    }
}
