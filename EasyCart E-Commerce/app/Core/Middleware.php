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
     */
    public static function authRequired(): void
    {
        if (!AuthService::check()) {
            header('Location: /login');
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

    /**
     * Check if current user is still active (not deactivated)
     * Auto-logout and block if deactivated.
     * Add this to routes that require active user status.
     */
    public static function checkActiveUser(): void
    {
        if (!AuthService::check()) {
            return; // Not logged in, skip
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return;
        }

        // Check user activation status from DB
        $resource = new \EasyCart\Resource\Resource_User();
        $user = $resource->load($userId);

        if (!$user || !($user['is_active'] ?? true)) {
            // User is deactivated — auto-logout
            $authService = new AuthService();
            $authService->logout();

            // Respond based on request type
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Your account has been deactivated. You have been logged out.',
                    'redirect' => '/login'
                ]);
            } else {
                $_SESSION['login_error'] = 'Your account has been deactivated. Please contact support.';
                header('Location: /login');
            }
            exit;
        }
    }
}
