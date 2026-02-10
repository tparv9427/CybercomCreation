<?php

namespace EasyCart\Services;

use EasyCart\Resource\Resource_Product;

/**
 * PricingService
 * 
 * Extracted from: checkout.php, ajax_cart.php
 */
class PricingService
{
    private $productResource;

    public function __construct()
    {
        $this->productResource = new Resource_Product();
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
            $product = $this->productResource->load($productId);
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
    public function calculateTotal($subtotal, $shipping, $tax, $paymentFee = 0.0, $discount = 0.0)
    {
        return max(0, $subtotal + $shipping + $tax + $paymentFee - $discount);
    }

    /**
     * Calculate all pricing for cart
     * 
     * @param array $cart
     * @param string $shippingMethod
     * @param string $paymentMethod
     * @return array ['subtotal', 'shipping', 'tax', 'total', 'item_count', 'payment_fee']
     */
    public function calculateAll($cart, $shippingMethod = 'standard', $paymentMethod = 'card', $coupon = null)
    {
        $itemCount = array_sum($cart);
        $subtotal = $this->calculateSubtotal($cart);
        $shipping = $this->calculateShipping($subtotal, $shippingMethod);
        $paymentFee = $this->calculatePaymentFee($paymentMethod);
        $tax = $this->calculateTax($subtotal, $shipping);

        $discount = 0;
        if ($coupon && isset($coupon['percent'])) {
            $discount = ($subtotal * $coupon['percent']) / 100;
        }

        $total = $this->calculateTotal($subtotal, $shipping, $tax, $paymentFee, $discount);

        return [
            'item_count' => $itemCount,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'payment_fee' => $paymentFee,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total
        ];
    }

    /**
     * Determine shipping category based on cart contents (Cart Value >= 300)
     * 
     * @param array $cart Array of product_id => quantity
     * @return string 'express' or 'freight'
     */
    public function determineShippingCategory($cart)
    {
        // Check if any product is freight category (price >= 300)
        foreach ($cart as $productId => $quantity) {
            $product = $this->productResource->load($productId);
            if ($product && $product['price'] >= 300) {
                return 'freight';
            }
        }

        // Check cart value (Subtotal only)
        $subtotal = $this->calculateSubtotal($cart);
        $cartValue = $subtotal;

        if ($cartValue >= 300) {
            return 'freight';
        }

        return 'express';
    }

    /**
     * Calculate estimated total range
     * 
     * @param array $cart
     * @return array ['min' => float, 'max' => float, 'cart_value' => float]
     */
    public function calculateEstimatedTotalRange($cart)
    {
        $subtotal = $this->calculateSubtotal($cart);
        $category = $this->determineShippingCategory($cart);
        $methods = $this->getAllowedShippingMethods($category);

        $totals = [];
        foreach ($methods as $method) {
            $shipping = $this->calculateShipping($subtotal, $method);
            // Tax removed from estimated calculation on cart page
            $totals[] = $subtotal + $shipping;
        }

        $taxOnItems = $subtotal * 0.18;
        $cartValue = $subtotal;

        return [
            'min' => !empty($totals) ? min($totals) : 0,
            'max' => !empty($totals) ? max($totals) : 0,
            'cart_value' => $cartValue,
            'subtotal' => $subtotal,
            'tax_on_items' => $taxOnItems
        ];
    }

    /**
     * Get allowed shipping methods for a category
     * 
     * @param string $category 'express' or 'freight'
     * @return array List of allowed shipping method values
     */
    public function getAllowedShippingMethods($category)
    {
        if ($category === 'freight') {
            return ['white_glove', 'freight'];
        }
        return ['standard', 'express'];
    }
}
