<?php

namespace EasyCart\Controllers;

use EasyCart\Services\AuthService;
use EasyCart\Repositories\CategoryRepository;

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
        $categories = (new \EasyCart\Repositories\CategoryRepository())->getAll();

        // Retrieve orders for the current user
        $user_id = $_SESSION['user_id'];

        $orderRepo = new \EasyCart\Repositories\OrderRepository();
        $dbOrders = $orderRepo->findByUserId($user_id);

        // Map DB orders to View structure to keep UI intact
        $orders = [];
        foreach ($dbOrders as $o) {
            $orders[] = [
                'id' => $o['order_number'], // Display order number as ID
                'date' => date('F j, Y', strtotime($o['created_at'])),
                'total' => $o['total'],
                'status' => $o['status'],
                'items' => array_map(function ($item) {
                    return [
                        'name' => $item['product_name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'image' => $item['product_image'] ?? null,
                        'total' => $item['price'] * $item['quantity']
                    ];
                }, $o['items'])
            ];
        }

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
        $categories = (new \EasyCart\Repositories\CategoryRepository())->getAll();

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/orders/success.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }
}
