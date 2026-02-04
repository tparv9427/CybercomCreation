<?php

namespace EasyCart\Repositories;

use EasyCart\Core\Database;
use EasyCart\Database\Queries;
use PDO;
use Exception;

/**
 * OrderRepository
 * 
 * Updated to use new schema: sales_order, sales_order_product
 */
class OrderRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Create order from cart (cart-to-order conversion)
     */
    public function createFromCart($cartId, $userId, $totals)
    {
        try {
            $this->pdo->beginTransaction();

            // Generate order number
            $orderNumber = 'ORD-' . strtoupper(uniqid());

            // 1. Create order header
            $stmt = $this->pdo->prepare(Queries::ORDER_CREATE);
            $stmt->execute([
                ':order_number' => $orderNumber,
                ':user_id' => $userId,
                ':cart_id' => $cartId,
                ':subtotal' => $totals['subtotal'],
                ':shipping_cost' => $totals['shipping_cost'],
                ':tax' => $totals['tax'],
                ':discount' => $totals['discount'] ?? 0,
                ':total' => $totals['total']
            ]);

            $orderId = $stmt->fetchColumn();

            // 2. Copy cart products to order products (with snapshots)
            $stmt = $this->pdo->prepare(Queries::ORDER_ADD_PRODUCTS_FROM_CART);
            $stmt->execute([
                ':order_id' => $orderId,
                ':cart_id' => $cartId
            ]);

            // 3. Inactivate the cart (DO NOT DELETE)
            $stmt = $this->pdo->prepare(Queries::CART_INACTIVATE);
            $stmt->execute([':cart_id' => $cartId]);

            $this->pdo->commit();

            return $orderId;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Order creation failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Legacy save method (for backward compatibility)
     * Now uses createFromCart internally
     */
    public function save($orderData, $items)
    {
        try {
            $this->pdo->beginTransaction();

            // Insert Order
            $stmt = $this->pdo->prepare("
                INSERT INTO sales_order 
                (order_number, user_id, subtotal, shipping_cost, tax, total, status, created_at) 
                VALUES 
                (:order_number, :user_id, :subtotal, :shipping_cost, :tax, :total, :status, NOW())
                RETURNING order_id
            ");

            $stmt->execute([
                ':order_number' => $orderData['order_number'],
                ':user_id' => $orderData['user_id'],
                ':subtotal' => $orderData['subtotal'],
                ':shipping_cost' => $orderData['shipping_cost'],
                ':tax' => $orderData['tax'],
                ':total' => $orderData['total'],
                ':status' => 'processing'
            ]);

            $orderId = $stmt->fetchColumn();

            // Insert Items with snapshots
            $itemStmt = $this->pdo->prepare("
                INSERT INTO sales_order_product 
                (order_id, product_entity_id, product_name, product_sku, product_price, quantity, row_total) 
                VALUES (:order_id, :product_id, :product_name, :product_sku, :price, :quantity, :row_total)
            ");

            foreach ($items as $item) {
                $itemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['id'],
                    ':product_name' => $item['name'] ?? 'Unknown Product',
                    ':product_sku' => $item['sku'] ?? 'unknown',
                    ':price' => $item['price'],
                    ':quantity' => $item['quantity'],
                    ':row_total' => $item['price'] * $item['quantity']
                ]);
            }

            $this->pdo->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Order Save Failed: " . $e->getMessage());
            return false;
        }
    }

    public function findByUserId($userId, $isArchived = false)
    {
        $stmt = $this->pdo->prepare(Queries::ORDER_FIND_BY_USER);
        $stmt->execute([
            ':user_id' => $userId,
            ':is_archived' => $isArchived ? 1 : 0
        ]);
        $orders = $stmt->fetchAll();

        foreach ($orders as &$order) {
            // Map entity_id to id for backward compatibility
            $order['id'] = $order['order_id'];

            // Fetch items for each order
            $itemStmt = $this->pdo->prepare(Queries::ORDER_GET_PRODUCTS);
            $itemStmt->execute([':order_id' => $order['order_id']]);
            $items = $itemStmt->fetchAll();

            // Map for backward compatibility
            foreach ($items as &$item) {
                $item['id'] = $item['item_id'];
                $item['name'] = $item['product_name'];
                $item['price'] = $item['product_price'];
                $item['image'] = $item['product_image'];
            }

            $order['items'] = $items;
        }

        return $orders;
    }

    public function findById($orderId)
    {
        $stmt = $this->pdo->prepare(Queries::ORDER_FIND_BY_ID);
        $stmt->execute([':order_id' => $orderId]);
        $order = $stmt->fetch();

        if ($order) {
            $order['id'] = $order['order_id']; // Backward compatibility

            // Get order items
            $itemStmt = $this->pdo->prepare(Queries::ORDER_GET_PRODUCTS);
            $itemStmt->execute([':order_id' => $order['order_id']]);
            $order['items'] = $itemStmt->fetchAll();
        }

        return $order;
    }

    public function findByOrderNumber($orderNumber)
    {
        $stmt = $this->pdo->prepare(Queries::ORDER_FIND_BY_NUMBER);
        $stmt->execute([':order_number' => $orderNumber]);
        $order = $stmt->fetch();

        if ($order) {
            $order['id'] = $order['order_id'];

            // Get order items
            $itemStmt = $this->pdo->prepare(Queries::ORDER_GET_PRODUCTS);
            $itemStmt->execute([':order_id' => $order['order_id']]);
            $order['items'] = $itemStmt->fetchAll();
        }

        return $order;
    }
    /**
     * Add address to order
     */
    public function addAddress($orderId, $type, $data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO sales_order_address 
            (order_id, address_type, first_name, last_name, email, phone, address_line_one, city, state, postal_code, country)
            VALUES 
            (:order_id, :type, :first_name, :last_name, :email, :phone, :address_line_one, :city, :state, :postal_code, :country)
        ");

        // Simple name split for now if full name provided
        $parts = explode(' ', $data['name'] ?? '', 2);
        $firstName = $parts[0] ?? '';
        $lastName = $parts[1] ?? '';

        return $stmt->execute([
            ':order_id' => $orderId,
            ':type' => $type,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $data['email'] ?? '',
            ':phone' => $data['phone'] ?? '',
            ':address_line_one' => $data['address'] ?? '',
            ':city' => $data['city'] ?? '',
            ':state' => '', // Not in form yet
            ':postal_code' => $data['zip'] ?? '',
            ':country' => 'India' // Default
        ]);
    }

    /**
     * Add payment and shipping method info to order
     */
    public function addPaymentInfo($orderId, $shippingMethod, $paymentMethod)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO sales_order_payment (order_id, shipping_method, payment_method)
            VALUES (:order_id, :shipping, :payment)
            ON CONFLICT (order_id) DO UPDATE SET
                shipping_method = EXCLUDED.shipping_method,
                payment_method = EXCLUDED.payment_method
        ");

        return $stmt->execute([
            ':order_id' => $orderId,
            ':shipping' => $shippingMethod,
            ':payment' => $paymentMethod
        ]);
    }

    /**
     * Get user dashboard statistics
     */
    public function getDashboardStats($userId)
    {
        $stmt = $this->pdo->prepare(Queries::DASHBOARD_STATS);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Get chart data for user
     */
    public function getChartData($userId)
    {
        $stmt = $this->pdo->prepare(Queries::DASHBOARD_CHART_DATA);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Archive or unarchive an order
     */
    public function archive($orderId, $status = true)
    {
        $stmt = $this->pdo->prepare(Queries::ORDER_ARCHIVE_UPDATE);
        return $stmt->execute([
            ':order_id' => $orderId,
            ':status' => $status ? 1 : 0
        ]);
    }
}
