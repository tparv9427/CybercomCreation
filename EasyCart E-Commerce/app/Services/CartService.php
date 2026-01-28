<?php

namespace EasyCart\Services;

use EasyCart\Repositories\CartRepository;
use EasyCart\Repositories\ProductRepository;

/**
 * CartService
 * 
 * Migrated from: ajax_cart.php, config.php (cart functions)
 */
class CartService
{
    private $cartRepo;
    private $productRepo;

    public function __construct()
    {
        $this->cartRepo = new CartRepository();
        $this->productRepo = new ProductRepository();
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

        $cart = $this->cartRepo->get();
        
        if (!isset($cart[$productId])) {
            $cart[$productId] = 0;
        }
        
        $cart[$productId] += $quantity;
        $this->cartRepo->save($cart);

        return true;
    }

    /**
     * Update cart item quantity
     * 
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public function update($productId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->remove($productId);
        }

        $cart = $this->cartRepo->get();
        $cart[$productId] = $quantity;
        $this->cartRepo->save($cart);

        return true;
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
                $total += $product['price'] * $quantity;
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
}
