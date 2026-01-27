<?php

namespace EasyCart\Services;

use EasyCart\Repositories\ProductRepository;

/**
 * PricingService
 * 
 * Extracted from: checkout.php, ajax_cart.php
 */
class PricingService
{
    private $productRepo;

    public function __construct()
    {
        $this->productRepo = new ProductRepository();
    }

    /**
     * Calculate subtotal from cart
     * 
     * @param array $cart Array of product_id => quantity
     * @return float
     */
    public function calculateSubtotal($cart)
    {
        $subtotal = 0;
        
        foreach ($cart as $productId => $quantity) {
            $product = $this->productRepo->find($productId);
            if ($product) {
                $subtotal += $product['price'] * $quantity;
            }
        }

        return $subtotal;
    }

    /**
     * Calculate shipping cost
     * 
     * @param int $totalItems Total number of items in cart
     * @return float
     */
    public function calculateShipping($totalItems)
    {
        // $10 per item
        return $totalItems * 10;
    }

    /**
     * Calculate tax
     * 
     * @param float $subtotal
     * @return float
     */
    public function calculateTax($subtotal)
    {
        // 8% tax on subtotal
        return $subtotal * 0.08;
    }

    /**
     * Calculate total
     * 
     * @param float $subtotal
     * @param float $shipping
     * @param float $tax
     * @return float
     */
    public function calculateTotal($subtotal, $shipping, $tax)
    {
        return $subtotal + $shipping + $tax;
    }

    /**
     * Calculate all pricing for cart
     * 
     * @param array $cart
     * @return array ['subtotal', 'shipping', 'tax', 'total', 'item_count']
     */
    public function calculateAll($cart)
    {
        $itemCount = array_sum($cart);
        $subtotal = $this->calculateSubtotal($cart);
        $shipping = $this->calculateShipping($itemCount);
        $tax = $this->calculateTax($subtotal);
        $total = $this->calculateTotal($subtotal, $shipping, $tax);

        return [
            'item_count' => $itemCount,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total
        ];
    }
}
