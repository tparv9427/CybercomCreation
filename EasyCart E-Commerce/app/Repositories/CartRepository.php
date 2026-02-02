<?php

namespace EasyCart\Repositories;

use EasyCart\Core\Database;

class CartRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function get()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null) {
            // Logged in user
            return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        } else {
            // Guest
            return isset($_SESSION['guest_cart']) ? $_SESSION['guest_cart'] : [];
        }
    }

    public function save($cartData)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null) {
            $_SESSION['cart'] = $cartData;
            $this->persist($_SESSION['user_id'], $cartData);
        } else {
            $_SESSION['guest_cart'] = $cartData;
        }
    }

    public function persist($userId, $cartData)
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Get or Create Cart
            $stmt = $this->pdo->prepare("SELECT id FROM carts WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $userId]);
            $cartId = $stmt->fetchColumn();

            if (!$cartId) {
                $stmt = $this->pdo->prepare("INSERT INTO carts (user_id) VALUES (:user_id) RETURNING id");
                $stmt->execute([':user_id' => $userId]);
                $cartId = $stmt->fetchColumn();
            }

            // 2. Clear existing items (Full Sync strategy)
            $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE cart_id = :cart_id");
            $stmt->execute([':cart_id' => $cartId]);

            // 3. Insert new items
            if (!empty($cartData)) {
                $itemStmt = $this->pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (:cart_id, :product_id, :quantity)");
                foreach ($cartData as $productId => $quantity) {
                    $itemStmt->execute([
                        ':cart_id' => $cartId,
                        ':product_id' => $productId,
                        ':quantity' => $quantity
                    ]);
                }
            }

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            // Log error silently or throw
            error_log("Cart persistence failed: " . $e->getMessage());
        }
    }

    public function loadFromDisk($userId)
    {
        // Renamed internally but keeping method name compatible if used elsewhere
        // Though ideally should be loadFromDb
        $stmt = $this->pdo->prepare("
            SELECT product_id, quantity 
            FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.id
            WHERE c.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        $items = $stmt->fetchAll();

        $cart = [];
        foreach ($items as $item) {
            $cart[$item['product_id']] = $item['quantity'];
        }

        return $cart;
    }
}
