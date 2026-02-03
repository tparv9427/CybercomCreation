<?php

namespace EasyCart\Controllers;

use EasyCart\Services\AuthService;
use EasyCart\Repositories\CategoryRepository;

/**
 * AuthController
 * 
 * Migrated from: login.php, signup.php, logout.php
 */
class AuthController
{
    private $authService;
    private $categoryRepo;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->categoryRepo = new CategoryRepository();
    }


    /*
     * Show login page
     */
    public function showLogin()
    {
        $this->renderAuthView('login');
    }

    /**
     * Process login (POST)
     */
    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $error = '';

        $user = $this->authService->login($email, $password);

        if ($user) {
            // Redirect to checkout if that's where they came from
            if (isset($_SESSION['checkout_redirect'])) {
                unset($_SESSION['checkout_redirect']);
                header('Location: /checkout');
            } else {
                header('Location: /');
            }
            exit;
        } else {
            $this->renderAuthView('login', 'Invalid email or password');
        }
    }

    /**
     * Show signup page
     */
    public function showSignup()
    {
        $this->renderAuthView('signup');
    }

    /**
     * Process signup (POST)
     */
    public function signup()
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $error = '';
        $success = '';

        if (empty($name) || empty($email) || empty($password)) {
            $error = 'All fields are required';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match';
        } else {
            // Attempt to register
            $userId = $this->authService->register($email, $password, $name);

            if ($userId) {
                // Session setup and guest data merge are handled inside AuthService::register

                header('Location: /');
                exit;
            } else {
                $error = 'Email already exists';
            }
        }

        $this->renderAuthView('signup', $error, $success);
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->authService->logout();
        header('Location: /');
        exit;
    }

    /**
     * Helper to render auth view
     */
    private function renderAuthView($mode, $error = '', $success = '')
    {
        $page_title = $mode === 'login' ? 'Login' : 'Sign Up';
        $categories = $this->categoryRepo->getAll();

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/auth/auth_combined.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }
}
