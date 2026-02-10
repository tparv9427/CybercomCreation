<?php

namespace EasyCart\Services;

use EasyCart\Resource\Resource_Cart;
use EasyCart\Resource\Resource_Product;
use EasyCart\Resource\Resource_Saved;

/**
 * CartService
 * 
 * Handles cart business logic using new MVC Resources.
 * Replaces legacy Repository-based implementation.
 */
class CartService
{
    private $cartResource;
    private $productResource;
    private $savedResource;

    public const MAX_QUANTITY_PER_ITEM = 6;

    public function __construct()
    {
        $this->cartResource = new Resource_Cart();
        $this->productResource = new Resource_Product();
        $this->savedResource = new Resource_Saved();
    }

    /**
     * Get current Cart ID, creating one if necessary
     * @return int
     */
    public function getCartId(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = session_id();

        $cart = $this->cartResource->findActive($userId, $sessionId);
        if ($cart) {
            return (int) $cart['cart_id'];
        }

        // Create new cart
        return (int) $this->cartResource->save([
            'customer_id' => $userId,
            'session_id' => $sessionId,
            'is_active' => true,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function getUserId()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Add product to cart
     */
    public function add($productId, $quantity = 1)
    {
        $product = $this->productResource->load($productId);
        if (!$product) {
            return ['success' => false, 'max_stock_reached' => false, 'current_quantity' => 0, 'max_stock' => 0];
        }

        $cartId = $this->getCartId();
        $currentItems = $this->cartResource->getItems($cartId);
        $oldQuantity = $currentItems[$productId] ?? 0;

        $currentStock = isset($product['stock']) ? (int) $product['stock'] : 999;
        $maxAllowed = min($currentStock, self::MAX_QUANTITY_PER_ITEM);

        $newQuantity = $oldQuantity + $quantity;
        $limitReached = false;
        $stockReached = false;

        if ($newQuantity > $maxAllowed) {
            $newQuantity = $maxAllowed;
            if ($maxAllowed === self::MAX_QUANTITY_PER_ITEM) {
                $limitReached = true;
            } else {
                $stockReached = true;
            }
        }

        $this->cartResource->saveItem($cartId, $productId, $newQuantity);

        return [
            'success' => true,
            'max_stock_reached' => $stockReached,
            'limit_reached' => $limitReached,
            'current_quantity' => $newQuantity,
            'max_stock' => $maxAllowed,
            'was_capped' => $limitReached || $stockReached
        ];
    }

    /**
     * Update cart item quantity
     */
    public function update($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->remove($productId);
            return ['success' => true, 'actual_quantity' => 0, 'limit_reached' => false, 'max_stock_reached' => false, 'max_stock' => 0];
        }

        $product = $this->productResource->load($productId);
        if (!$product) {
            return ['success' => false, 'actual_quantity' => 0, 'limit_reached' => false, 'max_stock_reached' => false, 'max_stock' => 0];
        }

        $currentStock = isset($product['stock']) ? (int) $product['stock'] : 999;
        $maxAllowed = min($currentStock, self::MAX_QUANTITY_PER_ITEM);

        $limitReached = false;
        $stockReached = false;

        if ($quantity > $maxAllowed) {
            $quantity = $maxAllowed;
            if ($maxAllowed === self::MAX_QUANTITY_PER_ITEM) {
                $limitReached = true;
            } else {
                $stockReached = true;
            }
        }

        $cartId = $this->getCartId();
        $this->cartResource->saveItem($cartId, $productId, $quantity);

        return [
            'success' => true,
            'actual_quantity' => $quantity,
            'limit_reached' => $limitReached,
            'max_stock_reached' => $stockReached,
            'max_stock' => $maxAllowed
        ];
    }

    /**
     * Remove product from cart
     */
    public function remove($productId)
    {
        $cartId = $this->getCartId();
        $this->cartResource->removeItem($cartId, $productId);
        return true;
    }

    /**
     * Empty the cart
     */
    public function empty()
    {
        $cartId = $this->getCartId();
        $this->cartResource->clearItems($cartId);
        return true;
    }

    /**
     * Get cart count (total items)
     */
    public function getCount()
    {
        $items = $this->get();
        return array_sum($items);
    }

    /**
     * Check if product is in cart
     */
    public function has($productId)
    {
        $items = $this->get();
        return isset($items[$productId]);
    }

    /**
     * Get cart contents [product_id => quantity]
     */
    public function get()
    {
        $cartId = $this->getCartId();
        return $this->cartResource->getItems($cartId);
    }

    /**
     * Save item for later
     */
    public function saveForLater($productId)
    {
        $userId = $this->getUserId();
        if (!$userId)
            return false;

        $this->savedResource->add($userId, $productId);
        $this->remove($productId);
        return true;
    }

    /**
     * Move saved item back to cart
     */
    public function moveToCartFromSaved($productId)
    {
        $userId = $this->getUserId();
        if (!$userId)
            return false;

        $saved = $this->savedResource->getByUserId($userId);
        if (!in_array($productId, $saved)) {
            return false;
        }

        $this->add($productId);
        $this->savedResource->remove($userId, $productId);
        return true;
    }

    /**
     * Get saved items with product details
     */
    public function getSavedItems()
    {
        $userId = $this->getUserId();
        if (!$userId)
            return [];

        $savedProductIds = $this->savedResource->getByUserId($userId);
        $items = [];

        foreach ($savedProductIds as $productId) {
            $product = $this->productResource->load($productId);
            if ($product) {
                // Formatting for view consistency
                $items[] = [
                    'product' => $product,
                    // 'quantity' => 1 // implied
                ];
            }
        }

        return $items;
    }
}
