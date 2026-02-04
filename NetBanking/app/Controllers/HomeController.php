<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\JsonDB;

class HomeController extends Controller
{
    public function index()
    {
        // Auth check (basic)
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $db = new JsonDB();
        $user = $db->find('users', $_SESSION['user_id']);
        $transactions = $db->where('transactions', 'user_id', $_SESSION['user_id']);

        // Sort transactions by date desc
        usort($transactions, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        $data = [
            'title' => 'Dashboard',
            'user' => $user,
            'transactions' => $transactions
        ];
        $this->view('home', $data);
    }
}
