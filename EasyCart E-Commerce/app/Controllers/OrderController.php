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
            header('Location: /login');
            exit;
        }

        $page_title = 'My Orders';
        $filter = $_GET['filter'] ?? 'active';
        $isArchived = ($filter === 'archived');

        $categories = (new \EasyCart\Repositories\CategoryRepository())->getAll();

        // Retrieve orders for the current user
        $user_id = $_SESSION['user_id'];

        $orderRepo = new \EasyCart\Repositories\OrderRepository();
        $dbOrders = $orderRepo->findByUserId($user_id, $isArchived);

        $statusService = new \EasyCart\Services\OrderStatusService();

        // Map DB orders to View structure to keep UI intact
        $orders = [];
        foreach ($dbOrders as $o) {
            $resolvedStatus = $statusService::getStatus($o['status'], $o['created_at']);
            $orders[] = [
                'id' => $o['order_number'], // Keep order number as ID for routing
                'order_number' => $o['order_number'],
                'date' => date('F j, Y', strtotime($o['created_at'])),
                'subtotal' => $o['subtotal'],
                'shipping_cost' => $o['shipping_cost'],
                'shipping_method' => $o['shipping_method'] ?? 'Standard',
                'tax' => $o['tax'],
                'discount' => $o['discount'] ?? 0,
                'total' => $o['total'],
                'status_slug' => $resolvedStatus,
                'status_label' => $statusService::getLabel($resolvedStatus),
                'ship_to' => trim(($o['first_name'] ?? '') . ' ' . ($o['last_name'] ?? '')),
                'shipping_address' => trim(($o['address_line_one'] ?? '') . ', ' . ($o['city'] ?? '') . ', ' . ($o['state'] ?? '') . ' ' . ($o['postal_code'] ?? '')),
                'payment_method' => $o['payment_method'] ?? 'COD',
                'items' => array_map(function ($item) {
                    $image = $item['product_image'] ?? null;
                    if ($image && strpos($image, '/assets/') !== 0 && strpos($image, 'assets/') !== 0) {
                        $image = '/assets/images/products/' . $image;
                    } elseif ($image && strpos($image, 'assets/') === 0) {
                        $image = '/' . $image;
                    }
                    return [
                        'product_id' => $item['product_entity_id'] ?? 0,
                        'name' => $item['product_name'],
                        'price' => $item['product_price'],
                        'quantity' => $item['quantity'],
                        'image' => $image,
                        'total' => $item['product_price'] * $item['quantity']
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

    /**
     * Order detail page
     */
    public function show($id)
    {
        $page_title = 'Order Details';
        $orderRepo = new \EasyCart\Repositories\OrderRepository();
        $order = $orderRepo->findByOrderNumber($id);
        $order_id = $id;

        if ($order) {
            $order['items'] = array_map(function ($item) {
                $image = $item['product_image'] ?? null;
                if ($image && strpos($image, '/assets/') !== 0 && strpos($image, 'assets/') !== 0) {
                    $image = '/assets/images/products/' . $image;
                } elseif ($image && strpos($image, 'assets/') === 0) {
                    $image = '/' . $image;
                }
                return [
                    'product_id' => $item['product_entity_id'] ?? 0,
                    'name' => $item['product_name'],
                    'price' => $item['product_price'],
                    'quantity' => $item['quantity'],
                    'image' => $image,
                    'total' => $item['product_price'] * $item['quantity']
                ];
            }, $order['items']);
        }

        $categories = (new \EasyCart\Repositories\CategoryRepository())->getAll();

        include __DIR__ . '/../Views/layouts/header.php';
        echo "<div class='container' style='padding: 4rem 2rem; min-height: 60vh;'>";
        echo "<h1>Order Details</h1>";
        echo "<p style='font-size: 1.2rem; margin-top: 1rem;'>Order ID: <strong>" . htmlspecialchars($order_id) . "</strong></p>";
        echo "<p style='color: var(--secondary); margin-top: 1rem;'>Full order details design will be implemented in the next phase.</p>";
        echo "<a href='/orders' class='btn' style='margin-top: 2rem;'>Back to My Orders</a>";
        echo "</div>";
        include __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * Order invoice page (PDF-like HTML)
     */
    public function invoice($id)
    {
        if (!AuthService::check()) {
            header('Location: /login');
            exit;
        }

        $orderRepo = new \EasyCart\Repositories\OrderRepository();
        $order = $orderRepo->findByOrderNumber($id);

        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            header('Location: /orders');
            exit;
        }

        $page_title = 'Invoice - ' . $order['order_number'];

        // Map items consistently for the view
        $order['items'] = array_map(function ($item) {
            $image = $item['product_image'] ?? null;
            if ($image && strpos($image, '/assets/') !== 0 && strpos($image, 'assets/') !== 0) {
                $image = '/assets/images/products/' . $image;
            } elseif ($image && strpos($image, 'assets/') === 0) {
                $image = '/' . $image;
            }
            return [
                'product_id' => $item['product_entity_id'] ?? 0,
                'name' => $item['product_name'],
                'price' => $item['product_price'],
                'quantity' => $item['quantity'],
                'sku' => $item['sku'] ?? 'N/A',
                'image' => $image,
                'total' => $item['product_price'] * $item['quantity']
            ];
        }, $order['items']);

        // No header/footer for invoice to keep it clean for PDF
        include __DIR__ . '/../Views/orders/invoice.php';
    }

    /**
     * Archive an order
     */
    public function archive($id)
    {
        if (!AuthService::check()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $orderRepo = new \EasyCart\Repositories\OrderRepository();
        $order = $orderRepo->findByOrderNumber($id);

        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }

        $status = isset($_GET['unarchive']) ? false : true;
        $orderRepo->archive($order['order_id'], $status);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}
