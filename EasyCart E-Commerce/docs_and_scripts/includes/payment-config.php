<?php
/**
 * Payment Gateway Configuration
 * Change these settings to switch payment providers
 */

// Payment Gateway Settings
define('PAYMENT_GATEWAY', 'internal'); // Options: 'internal', 'razorpay', 'paypal', 'stripe'
define('PAYMENT_MODE', 'test'); // Options: 'test', 'live'

// Razorpay Configuration (for future use)
define('RAZORPAY_KEY_ID', 'your_key_id');
define('RAZORPAY_KEY_SECRET', 'your_key_secret');

// PayPal Configuration (for future use)
define('PAYPAL_CLIENT_ID', 'your_client_id');
define('PAYPAL_SECRET', 'your_secret');

// Stripe Configuration (for future use)
define('STRIPE_PUBLIC_KEY', 'your_public_key');
define('STRIPE_SECRET_KEY', 'your_secret_key');

// Internal Payment Settings (current)
define('ENABLE_CARD_PAYMENT', true);
define('ENABLE_UPI_PAYMENT', true);
define('ENABLE_NETBANKING', true);
define('ENABLE_WALLET', true);
define('ENABLE_COD', true);

// UPI Settings
define('UPI_ID', 'easycart@upi');
define('UPI_NAME', 'EasyCart');

// QR Code API (for UPI QR generation)
define('QR_CODE_API', 'https://api.qrserver.com/v1/create-qr-code/');

/**
 * Get Payment Gateway Instance
 */
function getPaymentGateway() {
    switch (PAYMENT_GATEWAY) {
        case 'razorpay':
            // return new RazorpayGateway();
            break;
        case 'paypal':
            // return new PayPalGateway();
            break;
        case 'stripe':
            // return new StripeGateway();
            break;
        default:
            return 'internal';
    }
}
?>
