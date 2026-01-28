<?php
/**
 * Test script for ajax_checkout_pricing.php
 * Run this to verify the endpoint works correctly
 */

// Simulate session with cart data
session_start();
$_SESSION['cart'] = [
    1 => 2,  // Product ID 1, quantity 2
    2 => 1   // Product ID 2, quantity 1
];

// Simulate POST request
$_POST['shipping'] = 'express';
$_SERVER['REQUEST_METHOD'] = 'POST';

// Include the endpoint
require __DIR__ . '/ajax_checkout_pricing.php';
