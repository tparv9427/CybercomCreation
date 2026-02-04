<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\JsonDB;

class AuthController extends Controller
{

    public function login()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        $this->view('auth/login', ['title' => 'Login']);
    }

    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $db = new JsonDB();
            $users = $db->custom_query('users');

            foreach ($users as $user) {
                if ($user['username'] === $username) {
                    // In a real app, use password_verify($password, $user['password'])
                    // For demo with the hash we provided:
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        header('Location: /');
                        exit;
                    }
                }
            }

            // Failed
            $this->view('auth/login', ['error' => 'Invalid credentials', 'title' => 'Login']);
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}
