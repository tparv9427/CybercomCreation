<?php
/**
 * Cookie Encryption Secret Key
 * 
 * IMPORTANT: In production, replace this with a proper secret key
 * stored in environment variables.
 * 
 * Generate a secure key using: php -r "echo bin2hex(random_bytes(32));"
 */

return [
    // 256-bit (32-byte) secret key for AES-256-GCM
    // CHANGE THIS IN PRODUCTION!
    'secret_key' => hash('sha256', 'easycart_cookie_secret_change_me_in_production_2026', true),

    // Cookie settings
    'cookie_name' => 'easycart_cart',
    'expiry_days' => 10,
    'secure' => true,  // Set to true in production (requires HTTPS)
    'httponly' => true,
    'samesite' => 'Lax'
];
