<?php

namespace EasyCart\Controllers;

use EasyCart\Services\AuthService;

/**
 * OrderController
 * 
 * Migrated from: orders.php, order-success.php
 */
class OrderController
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * My orders page
     */
    public function index()
    {
        if (!AuthService::check()) {
            header('Location: login.php');
            exit;
        }

        $page_title = 'My Orders';
        $orders = []; // TODO: Implement order repository

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/orders/index.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * Order success page
     */
    public function success()
    {
        $page_title = 'Order Placed Successfully';
        $order_id = $_SESSION['last_order_id'] ?? null;

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/orders/success.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }
}
