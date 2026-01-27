<?php

namespace EasyCart\Controllers;

use EasyCart\Services\AuthService;

/**
 * AuthController
 * 
 * Migrated from: login.php, signup.php, logout.php
 */
class AuthController
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Show login page
     */
    public function showLogin()
    {
        $page_title = 'Login';
        $error = '';

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/auth/login.php';
        include __DIR__ . '/../Views/layouts/footer.php';
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
                header('Location: checkout.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password';
            $page_title = 'Login';
            
            include __DIR__ . '/../Views/layouts/header.php';
            include __DIR__ . '/../Views/auth/login.php';
            include __DIR__ . '/../Views/layouts/footer.php';
        }
    }

    /**
     * Show signup page
     */
    public function showSignup()
    {
        $page_title = 'Sign Up';
        $error = '';

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/auth/signup.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * Process signup (POST)
     */
    public function signup()
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $error = '';

        if (empty($name) || empty($email) || empty($password)) {
            $error = 'All fields are required';
        } else {
            $userId = $this->authService->register($email, $password, $name);
            
            if ($userId) {
                header('Location: index.php');
                exit;
            } else {
                $error = 'Email already exists';
            }
        }

        $page_title = 'Sign Up';
        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/auth/signup.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->authService->logout();
        header('Location: index.php');
        exit;
    }
}
