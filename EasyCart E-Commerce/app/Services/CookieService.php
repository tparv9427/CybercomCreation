<?php

namespace EasyCart\Services;

/**
 * CookieService
 * 
 * Production-grade encrypted cookie management for cart state.
 * Uses AES-256-GCM for tamper-proof encryption.
 * 
 * Cookie stores CLIENT INTENT only - DB remains source of truth for:
 * - Stock availability
 * - Product prices
 * - Cart limits
 * - Checkout eligibility
 */
class CookieService
{
    // Encryption settings
    private const CIPHER = 'aes-256-gcm';
    private const TAG_LENGTH = 16;

    // Cookie settings
    private const COOKIE_NAME = 'easycart_cart';
    private const EXPIRY_DAYS = 10;

    // Secret key (should be in environment variable in production)
    private string $secretKey;

    public function __construct()
    {
        // In production, use: getenv('CART_COOKIE_SECRET') or similar
        // For now, derive from a config or use a fallback
        $this->secretKey = $this->getSecretKey();
    }

    /**
     * Get or generate secret key
     * In production, this should come from environment variables
     */
    private function getSecretKey(): string
    {
        // Check for config-based key first
        $configPath = __DIR__ . '/../../config/cookie_secret.php';
        if (file_exists($configPath)) {
            $config = require $configPath;
            if (isset($config['secret_key'])) {
                return $config['secret_key'];
            }
        }

        // Fallback: generate from database config (deterministic but not ideal for production)
        $dbConfig = require __DIR__ . '/../../config/database.php';
        return hash('sha256', $dbConfig['dbname'] . $dbConfig['host'] . 'easycart_secret_2026', true);
    }

    // ============================================================================
    // ENCRYPTION / DECRYPTION
    // ============================================================================

    /**
     * Encrypt data using AES-256-GCM
     * 
     * @param array $data Data to encrypt
     * @return string Base64-encoded encrypted string
     */
    public function encrypt(array $data): string
    {
        $json = json_encode($data);

        // Generate random IV
        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER));

        // Encrypt with authentication tag
        $tag = '';
        $encrypted = openssl_encrypt(
            $json,
            self::CIPHER,
            $this->secretKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::TAG_LENGTH
        );

        if ($encrypted === false) {
            throw new \RuntimeException('Encryption failed');
        }

        // Combine IV + tag + ciphertext and base64 encode
        return base64_encode($iv . $tag . $encrypted);
    }

    /**
     * Decrypt data using AES-256-GCM
     * 
     * @param string $encrypted Base64-encoded encrypted string
     * @return array|null Decrypted data or null if tampered/invalid
     */
    public function decrypt(string $encrypted): ?array
    {
        try {
            $data = base64_decode($encrypted, true);
            if ($data === false) {
                return null;
            }

            $ivLength = openssl_cipher_iv_length(self::CIPHER);

            // Extract IV, tag, and ciphertext
            $iv = substr($data, 0, $ivLength);
            $tag = substr($data, $ivLength, self::TAG_LENGTH);
            $ciphertext = substr($data, $ivLength + self::TAG_LENGTH);

            if (strlen($iv) !== $ivLength || strlen($tag) !== self::TAG_LENGTH) {
                return null;
            }

            // Decrypt and verify authentication
            $decrypted = openssl_decrypt(
                $ciphertext,
                self::CIPHER,
                $this->secretKey,
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );

            if ($decrypted === false) {
                // Tampered or invalid
                return null;
            }

            $result = json_decode($decrypted, true);
            return is_array($result) ? $result : null;

        } catch (\Throwable $e) {
            error_log("Cookie decryption failed: " . $e->getMessage());
            return null;
        }
    }

    // ============================================================================
    // CART COOKIE MANAGEMENT
    // ============================================================================

    /**
     * Set encrypted cart cookie
     * 
     * Cookie schema:
     * - cart_id: int
     * - session_id: string
     * - products: array of {product_id, quantity}
     * - is_edited: bool (client made changes)
     * - last_updated_at: int (timestamp)
     * - expiry: int (timestamp)
     * 
     * @param int $cartId
     * @param string $sessionId
     * @param array $products Array of [product_id => quantity]
     * @param bool $isEdited Whether client has pending edits
     */
    public function setCartCookie(int $cartId, string $sessionId, array $products, bool $isEdited = false): void
    {
        $expiryTimestamp = time() + (self::EXPIRY_DAYS * 24 * 60 * 60);

        // Convert products to compact format
        $productList = [];
        foreach ($products as $productId => $quantity) {
            $productList[] = [
                'pid' => (int) $productId,
                'qty' => (int) $quantity
            ];
        }

        $data = [
            'cid' => $cartId,
            'sid' => $sessionId,
            'p' => $productList,
            'ed' => $isEdited,
            'ts' => time(),
            'exp' => $expiryTimestamp
        ];

        $encrypted = $this->encrypt($data);

        // Set secure cookie
        setcookie(
            self::COOKIE_NAME,
            $encrypted,
            [
                'expires' => $expiryTimestamp,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );

        // Also set in superglobal for immediate access in same request
        $_COOKIE[self::COOKIE_NAME] = $encrypted;
    }

    /**
     * Get decrypted cart cookie data
     * 
     * @return array|null Cookie data or null if not set/invalid/expired
     */
    public function getCartCookie(): ?array
    {
        if (!isset($_COOKIE[self::COOKIE_NAME])) {
            return null;
        }

        $data = $this->decrypt($_COOKIE[self::COOKIE_NAME]);

        if ($data === null) {
            // Cookie tampered or invalid - clear it
            $this->clearCartCookie();
            return null;
        }

        // Check expiry
        if (isset($data['exp']) && $data['exp'] < time()) {
            $this->clearCartCookie();
            return null;
        }

        // Convert compact format back to full format
        return [
            'cart_id' => $data['cid'] ?? null,
            'session_id' => $data['sid'] ?? null,
            'products' => $this->expandProducts($data['p'] ?? []),
            'is_edited' => $data['ed'] ?? false,
            'last_updated_at' => $data['ts'] ?? null,
            'expiry' => $data['exp'] ?? null
        ];
    }

    /**
     * Get cart products from cookie as [product_id => quantity]
     * 
     * @return array
     */
    public function getCartProductsFromCookie(): array
    {
        $cookie = $this->getCartCookie();
        return $cookie['products'] ?? [];
    }

    /**
     * Get cart ID from cookie
     * 
     * @return int|null
     */
    public function getCartIdFromCookie(): ?int
    {
        $cookie = $this->getCartCookie();
        return $cookie['cart_id'] ?? null;
    }

    /**
     * Check if cookie exists and is valid
     * 
     * @return bool
     */
    public function hasValidCookie(): bool
    {
        return $this->getCartCookie() !== null;
    }

    /**
     * Clear cart cookie
     */
    public function clearCartCookie(): void
    {
        setcookie(
            self::COOKIE_NAME,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );

        unset($_COOKIE[self::COOKIE_NAME]);
    }

    /**
     * Update cookie with new products (sync after DB update)
     * 
     * @param array $products [product_id => quantity]
     */
    public function syncCookie(array $products): void
    {
        $existing = $this->getCartCookie();

        if ($existing && $existing['cart_id']) {
            $this->setCartCookie(
                $existing['cart_id'],
                $existing['session_id'] ?? session_id(),
                $products,
                false // No longer edited after sync
            );
        }
    }

    // ============================================================================
    // HELPERS
    // ============================================================================

    /**
     * Convert compact product format to [product_id => quantity]
     */
    private function expandProducts(array $compactProducts): array
    {
        $products = [];
        foreach ($compactProducts as $item) {
            if (isset($item['pid']) && isset($item['qty'])) {
                $products[$item['pid']] = $item['qty'];
            }
        }
        return $products;
    }

    /**
     * Get cookie name (for debugging/testing)
     */
    public function getCookieName(): string
    {
        return self::COOKIE_NAME;
    }
}
