<?php

namespace EasyCart\Repositories;

use EasyCart\Core\Database;
use PDO;
use Exception;

class OrderRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function save($orderData, $items)
    {
        try {
            $this->pdo->beginTransaction();

            // Insert Order
            $stmt = $this->pdo->prepare("
                INSERT INTO orders 
                (order_number, user_id, subtotal, shipping_cost, tax, total, status, created_at) 
                VALUES 
                (:order_number, :user_id, :subtotal, :shipping_cost, :tax, :total, :status, NOW())
                RETURNING id
            ");

            $stmt->execute([
                ':order_number' => $orderData['order_number'],
                ':user_id' => $orderData['user_id'],
                ':subtotal' => $orderData['subtotal'],
                ':shipping_cost' => $orderData['shipping_cost'],
                ':tax' => $orderData['tax'],
                ':total' => $orderData['total'],
                ':status' => 'Processing' // Default
            ]);

            $orderId = $stmt->fetchColumn();

            // Insert Items
            $itemStmt = $this->pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES (:order_id, :product_id, :quantity, :price)
            ");

            foreach ($items as $item) {
                $itemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['id'], // Assuming item is product array
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price']
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

    public function findByUserId($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM orders 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        $orders = $stmt->fetchAll();

        foreach ($orders as &$order) {
            // Fetch items for each order
            $itemStmt = $this->pdo->prepare("
                SELECT oi.*, p.name as product_name, p.image as product_image 
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id
            ");
            $itemStmt->execute([':order_id' => $order['id']]);
            $order['items'] = $itemStmt->fetchAll();
        }

        return $orders;
    }
}
