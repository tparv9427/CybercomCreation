<?php

namespace EasyCart\Repositories;

/**
 * WishlistRepository
 * 
 * Migrated from: includes/session-manager.php (lines 56-96)
 */
class WishlistRepository
{
    private $wishlistFile;

    public function __construct()
    {
        $this->wishlistFile = __DIR__ . '/../../data/user_wishlists.json';
    }

    public function get()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null) {
            return isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : [];
        } else {
            return isset($_SESSION['guest_wishlist']) ? $_SESSION['guest_wishlist'] : [];
        }
    }

    public function save($wishlistData)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null) {
            $_SESSION['wishlist'] = $wishlistData;
            $this->saveToDisk($_SESSION['user_id'], $wishlistData);
        } else {
            $_SESSION['guest_wishlist'] = $wishlistData;
        }
    }

    public function saveToDisk($userId, $wishlistData)
    {
        $data = file_exists($this->wishlistFile) ? json_decode(file_get_contents($this->wishlistFile), true) : [];
        $data[$userId] = $wishlistData;
        
        $dir = dirname($this->wishlistFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        file_put_contents($this->wishlistFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function loadFromDisk($userId)
    {
        if (file_exists($this->wishlistFile)) {
            $wishlists = json_decode(file_get_contents($this->wishlistFile), true);
            return isset($wishlists[$userId]) ? $wishlists[$userId] : [];
        }
        return [];
    }
}
