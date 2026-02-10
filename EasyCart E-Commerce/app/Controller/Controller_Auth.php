<?php

namespace EasyCart\Controller;

use EasyCart\Services\AuthService;
use EasyCart\Collection\Collection_Category;
use EasyCart\View\View_Auth;

/**
 * Controller_Auth — Login / Signup / Logout
 * 
 * No SQL, no HTML.
 */
class Controller_Auth extends Controller_Abstract
{
    private $authService;
    private $categoryCollection;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->categoryCollection = new Collection_Category();
    }

    public function showLogin(): void
    {
        $this->renderAuthView('login');
    }

    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->authService->login($email, $password);

        if ($user) {
            if (isset($_SESSION['checkout_redirect'])) {
                unset($_SESSION['checkout_redirect']);
                $this->redirect('/checkout');
            }
            $this->redirect('/');
        } else {
            $this->renderAuthView('login', 'Invalid email or password');
        }
    }

    public function showSignup(): void
    {
        $this->renderAuthView('signup');
    }

    public function signup(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $this->renderAuthView('signup', 'All fields are required');
            return;
        }
        if ($password !== $confirm) {
            $this->renderAuthView('signup', 'Passwords do not match');
            return;
        }

        $userId = $this->authService->register($email, $password, $name);
        if ($userId) {
            $this->redirect('/');
        } else {
            $this->renderAuthView('signup', 'Email already exists');
        }
    }

    public function logout(): void
    {
        $this->authService->logout();
        $this->redirect('/');
    }

    private function renderAuthView(string $mode, string $error = '', string $success = ''): void
    {
        $categories = $this->categoryCollection->getAll();

        $contentView = new View_Auth([
            'mode' => $mode,
            'error' => $error,
            'success' => $success,
        ]);

        $this->renderWithLayout($contentView, [
            'page_title' => $mode === 'login' ? 'Login' : 'Sign Up',
            'categories' => $categories,
        ]);
    }
}
