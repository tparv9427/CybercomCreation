<?php
// includes/session-manager.php

/**
 * Initializes the session safely.
 */
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Gets the current cart data.
 * If user is logged in, load from data/user_carts.json (or session cache).
 * If guest, use $_SESSION['guest_cart'].
 */
function getCart() {
    initSession();
    if (isset($_SESSION['user_id'])) {
        // Logged in user
        // We can cache in session for performance, but for this task let's just use session as cache
        // and sync to file on logout/changes if needed.
        // Actually, to ensure persistence across devices/sessions, we should ideally load from file
        // on login, and save to file on every change.
        // For simplicity:
        // 1. On Login: Load from file -> Session
        // 2. On Change: Update Session -> Save to File
        // 3. On Logout: Save Session -> File (redundant but safe) -> Clear Session
        
        // This function simply returns the session variable which should be kept in sync.
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    } else {
        // Guest
        return isset($_SESSION['guest_cart']) ? $_SESSION['guest_cart'] : [];
    }
}

/**
 * Sets the cart data.
 * Updates session and, if logged in, saves to JSON file.
 */
function setCart($cartData) {
    initSession();
    if (isset($_SESSION['user_id'])) {
        $_SESSION['cart'] = $cartData;
        saveUserCartToDisk($_SESSION['user_id'], $cartData);
    } else {
        $_SESSION['guest_cart'] = $cartData;
    }
}

/**
 * Gets the current wishlist data.
 */
function getWishlist() {
    initSession();
    if (isset($_SESSION['user_id'])) {
        return isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : [];
    } else {
        return isset($_SESSION['guest_wishlist']) ? $_SESSION['guest_wishlist'] : [];
    }
}

/**
 * Sets the wishlist data.
 */
function setWishlist($wishlistData) {
    initSession();
    if (isset($_SESSION['user_id'])) {
        $_SESSION['wishlist'] = $wishlistData;
        saveUserWishlistToDisk($_SESSION['user_id'], $wishlistData);
    } else {
        $_SESSION['guest_wishlist'] = $wishlistData;
    }
}

/**
 * Helper to save user cart to JSON file.
 */
function saveUserCartToDisk($userId, $cartData) {
    $file = __DIR__ . '/../data/user_carts.json';
    $data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    $data[$userId] = $cartData;
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

/**
 * Helper to save user wishlist to JSON file.
 */
function saveUserWishlistToDisk($userId, $wishlistData) {
    $file = __DIR__ . '/../data/user_wishlists.json';
    $data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    $data[$userId] = $wishlistData;
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

/**
 * Helper to load user data from disk into session (called on login).
 */
function loadUserDataToSession($userId) {
    // Load Cart
    $cartFile = __DIR__ . '/../data/user_carts.json';
    if (file_exists($cartFile)) {
        $carts = json_decode(file_get_contents($cartFile), true);
        $_SESSION['cart'] = isset($carts[$userId]) ? $carts[$userId] : [];
    } else {
        $_SESSION['cart'] = [];
    }

    // Load Wishlist
    $wishlistFile = __DIR__ . '/../data/user_wishlists.json';
    if (file_exists($wishlistFile)) {
        $wishlists = json_decode(file_get_contents($wishlistFile), true);
        $_SESSION['wishlist'] = isset($wishlists[$userId]) ? $wishlists[$userId] : [];
    } else {
        $_SESSION['wishlist'] = [];
    }
}

/**
 * Merges guest data into user data (called on login).
 */
function mergeGuestToUser($userId) {
    // Load existing user data from disk first
    loadUserDataToSession($userId);

    // Merge Cart
    if (isset($_SESSION['guest_cart']) && !empty($_SESSION['guest_cart'])) {
        foreach ($_SESSION['guest_cart'] as $pid => $qty) {
            if (isset($_SESSION['cart'][$pid])) {
                $_SESSION['cart'][$pid] += $qty;
            } else {
                $_SESSION['cart'][$pid] = $qty;
            }
        }
        // Clear guest cart
        unset($_SESSION['guest_cart']);
        // Save merged cart to disk
        saveUserCartToDisk($userId, $_SESSION['cart']);
    }

    // Merge Wishlist
    if (isset($_SESSION['guest_wishlist']) && !empty($_SESSION['guest_wishlist'])) {
        foreach ($_SESSION['guest_wishlist'] as $pid => $val) {
            $_SESSION['wishlist'][$pid] = $val;
        }
        // Clear guest wishlist
        unset($_SESSION['guest_wishlist']);
        // Save merged wishlist to disk
        saveUserWishlistToDisk($userId, $_SESSION['wishlist']);
    }
}
?>
