<?php
namespace EasyCart\Core;

use EasyCart\Services\AuthService;

class Middleware
{
    /**
     * Protect against CSRF attacks
     * Should be called for state-changing methods (POST, PUT, DELETE)
     */
    public static function csrfProtection(): void
    {
        // Only check state-changing methods
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Get token from POST data or Headers
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

            if (!CSRF::validateToken($token)) {
                // Log the failure
                error_log("CSRF Validation Failed. IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown'));

                http_response_code(403);

                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    // JSON response for AJAX
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'CSRF token validation failed']);
                } else {
                    // HTML response for Browser
                    echo '<h1>403 Forbidden</h1>';
                    echo '<p>CSRF token validation failed</p>';
                }
                exit;
            }
        }
    }

    /**
     * Require authentication
     * Also checks if user is still active in database
     */
    public static function authRequired(): void
    {
        if (!AuthService::check()) {
            header('Location: /login');
            exit;
        }

        // Check if user is still active in database
        $userRepo = new \EasyCart\Repositories\UserRepository();
        $user = $userRepo->find($_SESSION['user_id']);

        if (!$user || (isset($user['is_active']) && $user['is_active'] === false)) {
            // Auto-logout deactivated user
            $_SESSION['user_id'] = null;
            unset($_SESSION['cart_id']);
            header('Location: /login?deactivated=1');
            exit;
        }
    }

    /**
     * Require guest (not logged in)
     */
    public static function guestOnly(): void
    {
        if (AuthService::check()) {
            header('Location: /');
            exit;
        }
    }
}
