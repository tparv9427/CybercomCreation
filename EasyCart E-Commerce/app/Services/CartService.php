<?php

namespace EasyCart\Services;

use EasyCart\Repositories\CartRepository;
use EasyCart\Repositories\ProductRepository;
use EasyCart\Repositories\SaveForLaterRepository;
use EasyCart\Services\AuthService;

/**
 * CartService
 * 
 * Migrated from: ajax_cart.php, config.php (cart functions)
 */
class CartService
{
    private $cartRepo;
    private $productRepo;
    private $saveRepo;

    public function __construct()
    {
        $this->cartRepo = new CartRepository();
        $this->productRepo = new ProductRepository();
        $this->saveRepo = new SaveForLaterRepository();
    }

    private function getUserId()
    {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Add product to cart
     * 
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public function add($productId, $quantity = 1)
    {
        $product = $this->productRepo->find($productId);
        if (!$product) {
            return false;
        }

        $currentStock = isset($product['stock']) ? (int) $product['stock'] : 999;
        $cart = $this->cartRepo->get();

        if (!isset($cart[$productId])) {
            $cart[$productId] = 0;
        }

        $newQuantity = $cart[$productId] + $quantity;

        // Enforce max stock
        if ($newQuantity > $currentStock) {
            $newQuantity = $currentStock;
        }

        $cart[$productId] = $newQuantity;
        $this->cartRepo->save($cart);

        return true;
    }

    /**
     * Update cart item quantity
     * 
     * @param int $productId
     * @param int $quantity
     * @return int The actual updated quantity
     */
    public function update($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->remove($productId);
            return 0;
        }

        $product = $this->productRepo->find($productId);
        if (!$product) {
            return 0;
        }

        $currentStock = isset($product['stock']) ? (int) $product['stock'] : 999;

        // Enforce max stock
        if ($quantity > $currentStock) {
            $quantity = $currentStock;
        }

        $cart = $this->cartRepo->get();
        $cart[$productId] = $quantity;
        $this->cartRepo->save($cart);

        return $quantity;
    }

    /**
     * Remove product from cart
     * 
     * @param int $productId
     * @return bool
     */
    public function remove($productId)
    {
        $cart = $this->cartRepo->get();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->cartRepo->save($cart);
        }

        return true;
    }

    /**
     * Empty the cart (remove all items)
     * 
     * @return bool
     */
    public function empty()
    {
        $this->cartRepo->save([]);
        return true;
    }

    /**
     * Get cart count (total items)
     * 
     * @return int
     */
    public function getCount()
    {
        return array_sum($this->cartRepo->get());
    }

    /**
     * Get cart total price
     * 
     * @return float
     */
    public function getTotal()
    {
        $total = 0;
        $cart = $this->cartRepo->get();

        foreach ($cart as $productId => $quantity) {
            $product = $this->productRepo->find($productId);
            if ($product) {
                // Determine price (use sale price if valid)
                $price = $product['price'];
                $total += $price * $quantity;
            }
        }

        return $total;
    }

    /**
     * Check if product is in cart
     * 
     * @param int $productId
     * @return bool
     */
    public function has($productId)
    {
        $cart = $this->cartRepo->get();
        return isset($cart[$productId]);
    }

    /**
     * Get cart contents
     * 
     * @return array
     */
    public function get()
    {
        return $this->cartRepo->get();
    }

    /**
     * Save item for later
     * 
     * @param int $productId
     * @return bool
     */
    public function saveForLater($productId)
    {
        $userId = $this->getUserId();
        if (!$userId)
            return false;

        // Add to Saved
        $this->saveRepo->add($userId, $productId);

        // Remove from Cart
        $this->remove($productId);

        return true;
    }

    /**
     * Move saved item back to cart
     * 
     * @param int $productId
     * @return bool
     */
    public function moveToCartFromSaved($productId)
    {
        $userId = $this->getUserId();
        if (!$userId)
            return false;

        // Is it actually saved?
        $saved = $this->saveRepo->get($userId);
        // Note: Repository returns array of IDs now, not map like before
        if (!in_array($productId, $saved)) {
            return false;
        }

        // Add to Cart
        $this->add($productId); // Defaults to 1

        // Remove from Saved
        $this->saveRepo->remove($userId, $productId);

        return true;
    }

    /**
     * Get saved items with product details
     * 
     * @return array
     */
    public function getSavedItems()
    {
        $userId = $this->getUserId();
        if (!$userId)
            return [];

        $savedProductIds = $this->saveRepo->get($userId);
        $items = [];

        foreach ($savedProductIds as $productId) {
            $product = $this->productRepo->find($productId);
            if ($product) {
                $items[] = [
                    'product' => $product,
                    // Quantity concept removed for Saved Items in DB refactor (it's just a set of products)
                    // But for UI compatibility, we can imply 1 if needed
                ];
            }
        }

        return $items;
    }
}
