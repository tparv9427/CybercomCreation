<?php

namespace EasyCart\Repositories;

use EasyCart\Core\Database;
use EasyCart\Database\Queries;

/**
 * CartRepository
 * 
 * Updated to use new schema: sales_cart, sales_cart_product
 */
class CartRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Get or create cart ID for current session
     */
    private function getCartId()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        // Check if cart_id already in session
        if (isset($_SESSION['cart_id'])) {
            return $_SESSION['cart_id'];
        }

        // Determine user_id or session_id
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = $userId ? null : session_id();

        // Try to find existing cart
        if ($userId) {
            $stmt = $this->pdo->prepare(Queries::CART_FIND_BY_USER);
            $stmt->execute([':user_id' => $userId]);
        } else {
            $stmt = $this->pdo->prepare(Queries::CART_FIND_BY_SESSION);
            $stmt->execute([':session_id' => $sessionId]);
        }

        $cart = $stmt->fetch();
        $cartId = $cart ? $cart['cart_id'] : null;

        // Create new cart if not found
        if (!$cartId) {
            $stmt = $this->pdo->prepare(Queries::CART_CREATE);
            $stmt->execute([
                ':user_id' => $userId,
                ':session_id' => $sessionId
            ]);
            $cartId = $stmt->fetchColumn();
        }

        // Store in session
        $_SESSION['cart_id'] = $cartId;
        return $cartId;
    }

    /**
     * Get cart items as associative array [product_id => quantity]
     */
    public function get()
    {
        $cartId = $this->getCartId();

        $stmt = $this->pdo->prepare(Queries::CART_GET_PRODUCTS);
        $stmt->execute([':cart_id' => $cartId]);
        $items = $stmt->fetchAll();

        $cart = [];
        foreach ($items as $item) {
            $cart[$item['product_entity_id']] = $item['quantity'];
        }

        return $cart;
    }

    /**
     * Save cart items (full sync)
     */
    public function save($cartData)
    {
        $cartId = $this->getCartId();

        try {
            $this->pdo->beginTransaction();

            // Clear existing items
            $stmt = $this->pdo->prepare(Queries::CART_CLEAR_PRODUCTS);
            $stmt->execute([':cart_id' => $cartId]);

            // Insert new items
            if (!empty($cartData)) {
                $itemStmt = $this->pdo->prepare(Queries::CART_ADD_PRODUCT);
                foreach ($cartData as $productId => $quantity) {
                    $itemStmt->execute([
                        ':cart_id' => $cartId,
                        ':product_id' => $productId,
                        ':quantity' => $quantity
                    ]);
                }
            }

            // Update cart timestamp
            $stmt = $this->pdo->prepare("
                UPDATE sales_cart 
                SET updated_at = CURRENT_TIMESTAMP 
                WHERE cart_id = :cart_id
            ");
            $stmt->execute([':cart_id' => $cartId]);

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            error_log("Cart save failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Persist cart (legacy compatibility - now just calls save)
     */
    public function persist($userId, $cartData)
    {
        $this->save($cartData);
    }

    /**
     * Load user cart from database
     */
    public function loadUserCart($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT ci.product_entity_id, ci.quantity 
            FROM sales_cart_product ci
            JOIN sales_cart c ON ci.cart_id = c.cart_id
            WHERE c.user_id = :user_id AND c.is_active = TRUE
        ");
        $stmt->execute([':user_id' => $userId]);
        $items = $stmt->fetchAll();

        $cart = [];
        foreach ($items as $item) {
            $cart[$item['product_entity_id']] = $item['quantity'];
        }

        return $cart;
    }

    /**
     * Transfer guest cart to user account
     */
    public function transferGuestCartToUser($userId)
    {
        if (!isset($_SESSION['cart_id'])) {
            return;
        }

        $guestCartId = $_SESSION['cart_id'];

        try {
            // Check if user already has a cart
            $stmt = $this->pdo->prepare(Queries::CART_FIND_BY_USER);
            $stmt->execute([':user_id' => $userId]);
            $userCart = $stmt->fetch();
            $userCartId = $userCart ? $userCart['cart_id'] : null;

            if ($userCartId && $userCartId != $guestCartId) {
                // Merge guest cart into user cart
                $this->pdo->beginTransaction();

                // Get guest items
                $stmt = $this->pdo->prepare(Queries::CART_GET_PRODUCTS);
                $stmt->execute([':cart_id' => $guestCartId]);
                $guestItems = $stmt->fetchAll();

                // Merge into user cart
                $mergeStmt = $this->pdo->prepare("
                    INSERT INTO sales_cart_product (cart_id, product_entity_id, quantity) 
                    VALUES (:cart_id, :product_id, :quantity)
                    ON CONFLICT (cart_id, product_entity_id) 
                    DO UPDATE SET quantity = LEAST(sales_cart_product.quantity + EXCLUDED.quantity, " . \EasyCart\Services\CartService::MAX_QUANTITY_PER_ITEM . "),
                                  updated_at = CURRENT_TIMESTAMP
                ");

                foreach ($guestItems as $item) {
                    $mergeStmt->execute([
                        ':cart_id' => $userCartId,
                        ':product_id' => $item['product_entity_id'],
                        ':quantity' => $item['quantity']
                    ]);
                }

                // Inactivate guest cart (don't delete)
                $stmt = $this->pdo->prepare(Queries::CART_INACTIVATE);
                $stmt->execute([':cart_id' => $guestCartId]);

                $this->pdo->commit();

                // Update session to use user cart
                $_SESSION['cart_id'] = $userCartId;
            } else {
                // Just update ownership of guest cart
                $stmt = $this->pdo->prepare(Queries::CART_UPDATE_OWNERSHIP);
                $stmt->execute([
                    ':user_id' => $userId,
                    ':cart_id' => $guestCartId
                ]);
            }
        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Cart transfer failed: " . $e->getMessage());
        }
    }
}
