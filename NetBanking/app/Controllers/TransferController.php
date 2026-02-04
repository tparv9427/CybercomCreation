<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\JsonDB;

class TransferController extends Controller
{

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $data = [
            'title' => 'Transfer Money',
            'user' => $_SESSION['user_name']
        ];
        $this->view('transfer/index', $data);
    }

    public function process()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recipientName = $_POST['recipient_name'];
            $accountNumber = $_POST['account_number'];
            $amount = floatval($_POST['amount']);

            if ($amount <= 0) {
                $this->view('transfer/index', ['error' => 'Invalid amount', 'title' => 'Transfer Money']);
                return;
            }

            $db = new JsonDB();

            // 1. Check Sender Balance
            $sender = $db->find('users', $_SESSION['user_id']);
            if ($sender['balance'] < $amount) {
                $this->view('transfer/index', ['error' => 'Insufficient funds', 'title' => 'Transfer Money']);
                return;
            }

            // 2. Find Recipient (Simulated lookup by account number)
            $users = $db->custom_query('users');
            $recipient = null;
            foreach ($users as $u) {
                if ($u['account_number'] === $accountNumber) {
                    $recipient = $u;
                    break;
                }
            }

            // For now, if recipient not found, we act like it's an external transfer and just deduct money
            // In a real internal system, we'd error. 
            // Let's implement internal transfer logic primarily.

            // Deduct from Sender
            $sender['balance'] -= $amount;
            $db->update('users', $sender['id'], ['balance' => $sender['balance']]);

            // Add Transaction Record for Sender
            $db->insert('transactions', [
                'user_id' => $sender['id'],
                'description' => "Transfer to " . $recipientName,
                'category' => 'Transfer',
                'amount' => -$amount,
                'date' => date('Y-m-d'),
                'type' => 'debit'
            ]);

            // If Internal Recipient, Add to them
            if ($recipient) {
                $recipient['balance'] += $amount;
                $db->update('users', $recipient['id'], ['balance' => $recipient['balance']]);

                $db->insert('transactions', [
                    'user_id' => $recipient['id'],
                    'description' => "Transfer from " . $sender['name'],
                    'category' => 'Transfer',
                    'amount' => $amount,
                    'date' => date('Y-m-d'),
                    'type' => 'credit'
                ]);
            }

            // Success
            header('Location: /?msg=transfer_success');
            exit;
        }
    }
}
