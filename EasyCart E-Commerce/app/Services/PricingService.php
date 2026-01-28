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
     * Calculate shipping cost based on selected method and subtotal
     * 
     * @param float $subtotal
     * @param string $method (standard|express|white_glove|freight)
     * @return float
     */
    public function calculateShipping($subtotal, $method = 'standard')
    {
        switch ($method) {
            case 'express':
                // Flat $80 OR 10% of subtotal (whichever is lower)
                $percentCost = $subtotal * 0.10;
                return min(80, $percentCost);
                
            case 'white_glove':
                // Flat $150 OR 5% of subtotal (whichever is lower)
                $percentCost = $subtotal * 0.05;
                return min(150, $percentCost);
                
            case 'freight':
                // 3% of subtotal, Minimum $200
                $percentCost = $subtotal * 0.03;
                return max(200, $percentCost);
                
            case 'standard':
            default:
                // Flat $40
                return 40;
        }
    }

    /**
     * Calculate tax (18% on Subtotal + Shipping)
     * 
     * @param float $subtotal
     * @param float $shipping
     * @return float
     */
    public function calculateTax($subtotal, $shipping = 0)
    {
        // 18% tax on (Subtotal + Shipping)
        return ($subtotal + $shipping) * 0.18;
    }

    /**
     * Calculate payment fee
     * 
     * @param string $method
     * @return float
     */
    public function calculatePaymentFee($method)
    {
        if ($method === 'cod') {
            return 5.00;
        }
        return 0.00;
    }

    /**
     * Calculate total
     * 
     * @param float $subtotal
     * @param float $shipping
     * @param float $tax
     * @param float $paymentFee
     * @return float
     */
    public function calculateTotal($subtotal, $shipping, $tax, $paymentFee = 0.0)
    {
        return $subtotal + $shipping + $tax + $paymentFee;
    }

    /**
     * Calculate all pricing for cart
     * 
     * @param array $cart
     * @param string $shippingMethod
     * @param string $paymentMethod
     * @return array ['subtotal', 'shipping', 'tax', 'total', 'item_count', 'payment_fee']
     */
    public function calculateAll($cart, $shippingMethod = 'standard', $paymentMethod = 'card')
    {
        $itemCount = array_sum($cart);
        $subtotal = $this->calculateSubtotal($cart);
        $shipping = $this->calculateShipping($subtotal, $shippingMethod);
        $paymentFee = $this->calculatePaymentFee($paymentMethod);
        $tax = $this->calculateTax($subtotal, $shipping);
        $total = $this->calculateTotal($subtotal, $shipping, $tax, $paymentFee);

        return [
            'item_count' => $itemCount,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'payment_fee' => $paymentFee,
            'tax' => $tax,
            'total' => $total
        ];
    }
}
