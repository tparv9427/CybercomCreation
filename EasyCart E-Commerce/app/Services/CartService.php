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
    private $saveRepo;

    public function __construct()
    {
        $this->cartRepo = new CartRepository();
        $this->productRepo = new ProductRepository();
        $this->saveRepo = new \EasyCart\Repositories\SaveForLaterRepository();
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

    /**
     * Save item for later
     * 
     * @param int $productId
     * @return bool
     */
    public function saveForLater($productId)
    {
        $cart = $this->cartRepo->get();
        // Item doesn't have to be in cart to be saved for later
        
        $saved = $this->saveRepo->get();
        // If in cart, move quantity. If not, default to 1 or add 1? 
        // Logic: specific request "add this save for later button...". Usually implies "add this item to saved list".
        // If it's already in saved, we can just ensure it stays there.
        // If it was in cart, we should remove it from cart.
        
        $quantity = isset($cart[$productId]) ? $cart[$productId] : 1;
        
        // If already in saved, we might want to just keep it or add quantity?
        // Simple implementation: Overwrite or set.
        $saved[$productId] = $quantity; // Store with quantity
        
        $this->saveRepo->save($saved);
        
        // If it was in cart, remove it
        if (isset($cart[$productId])) {
            $this->remove($productId);
        }
        
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
        $saved = $this->saveRepo->get();
        if(!isset($saved[$productId])) {
            return false;
        }

        $this->add($productId, $saved[$productId]);
        
        unset($saved[$productId]);
        $this->saveRepo->save($saved);
        
        return true;
    }
    
    /**
     * Get saved items with product details
     * 
     * @return array
     */
    public function getSavedItems()
    {
        $saved = $this->saveRepo->get();
        $items = [];
        
        foreach ($saved as $productId => $quantity) {
             $product = $this->productRepo->find($productId);
             if ($product) {
                 $items[] = [
                     'product' => $product,
                     'quantity' => $quantity,
                     'total' => $product['price'] * $quantity
                 ];
             }
        }
        
        return $items;
    }
}
