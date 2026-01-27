<?php

namespace EasyCart\Repositories;

/**
 * CartRepository
 * 
 * Migrated from: includes/session-manager.php (lines 18-51)
 */
class CartRepository
{
    private $cartFile;

    public function __construct()
    {
        $this->cartFile = __DIR__ . '/../../data/user_carts.json';
    }

    public function get()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null) {
            // Logged in user
            return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        } else {
            // Guest
            return isset($_SESSION['guest_cart']) ? $_SESSION['guest_cart'] : [];
        }
    }

    public function save($cartData)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null) {
            $_SESSION['cart'] = $cartData;
            $this->saveToDisk($_SESSION['user_id'], $cartData);
        } else {
            $_SESSION['guest_cart'] = $cartData;
        }
    }

    public function saveToDisk($userId, $cartData)
    {
        $data = file_exists($this->cartFile) ? json_decode(file_get_contents($this->cartFile), true) : [];
        $data[$userId] = $cartData;
        
        $dir = dirname($this->cartFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        file_put_contents($this->cartFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function loadFromDisk($userId)
    {
        if (file_exists($this->cartFile)) {
            $carts = json_decode(file_get_contents($this->cartFile), true);
            return isset($carts[$userId]) ? $carts[$userId] : [];
        }
        return [];
    }
}
