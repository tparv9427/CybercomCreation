<?php

namespace EasyCart\Helpers;

/**
 * Pricing Helper
 * 
 * Handles all pricing calculations including shipping, tax, and totals
 */
class PricingHelper
{
    /**
     * Calculate cart subtotal from cart items
     * 
     * @param array $cartItems Cart items array
     * @return float Subtotal amount
     */
    public static function calculateCartSubtotal($cartItems)
    {
        $subtotal = 0;
        
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        return $subtotal;
    }
    
    /**
     * Calculate shipping cost based on method and subtotal
     * 
     * Shipping Rules:
     * - Standard: Flat $40
     * - Express: $80 OR 10% of subtotal (whichever is lower)
     * - White Glove: $150 OR 5% of subtotal (whichever is lower)
     * - Freight: 3% of subtotal with minimum $200
     * 
     * @param string $method Shipping method (standard, express, white_glove, freight)
     * @param float $subtotal Cart subtotal
     * @return float Shipping cost
     */
    public static function calculateShipping($method, $subtotal)
    {
        switch ($method) {
            case 'standard':
                return 40.00;
                
            case 'express':
                // $80 OR 10% of subtotal (whichever is lower)
                $percentage = $subtotal * 0.10;
                return min(80.00, $percentage);
                
            case 'white_glove':
                // $150 OR 5% of subtotal (whichever is lower)
                $percentage = $subtotal * 0.05;
                return min(150.00, $percentage);
                
            case 'freight':
                // 3% of subtotal with minimum $200
                $percentage = $subtotal * 0.03;
                return max(200.00, $percentage);
                
            default:
                // Default to standard shipping
                return 40.00;
        }
    }
    
    /**
     * Calculate tax (18% on subtotal + shipping)
     * 
     * @param float $subtotal Cart subtotal
     * @param float $shipping Shipping cost
     * @return float Tax amount
     */
    public static function calculateTax($subtotal, $shipping)
    {
        return ($subtotal + $shipping) * 0.18;
    }
    
    /**
     * Calculate final total
     * 
     * @param float $subtotal Cart subtotal
     * @param float $shipping Shipping cost
     * @param float $tax Tax amount
     * @return float Total amount
     */
    public static function calculateTotal($subtotal, $shipping, $tax)
    {
        return $subtotal + $shipping + $tax;
    }
    
    /**
     * Format price with currency symbol
     * 
     * @param float $amount Amount to format
     * @return string Formatted price
     */
    public static function formatPrice($amount)
    {
        return '₹' . number_format($amount, 2);
    }
    
    /**
     * Calculate all pricing details for checkout
     * 
     * @param array $cartItems Cart items
     * @param string $shippingMethod Shipping method
     * @return array Pricing details
     */
    public static function calculateCheckoutPricing($cartItems, $shippingMethod = 'standard')
    {
        $subtotal = self::calculateCartSubtotal($cartItems);
        $shipping = self::calculateShipping($shippingMethod, $subtotal);
        $tax = self::calculateTax($subtotal, $shipping);
        $total = self::calculateTotal($subtotal, $shipping, $tax);
        
        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'subtotal_formatted' => self::formatPrice($subtotal),
            'shipping_formatted' => self::formatPrice($shipping),
            'tax_formatted' => self::formatPrice($tax),
            'total_formatted' => self::formatPrice($total)
        ];
    }
}
