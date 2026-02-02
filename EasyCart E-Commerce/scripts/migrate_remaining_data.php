<?php
require_once __DIR__ . '/../app/Core/Database.php';

use EasyCart\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "Starting migration of remaining JSON data...\n";

// --- Migrate Coupons ---
$couponsFile = __DIR__ . '/../data/coupons.json';
if (file_exists($couponsFile)) {
    echo "Migrating Coupons...\n";
    $coupons = json_decode(file_get_contents($couponsFile), true);
    $stmt = $pdo->prepare("INSERT INTO coupons (code, discount_percent) VALUES (:code, :percent) ON CONFLICT (code) DO NOTHING");

    foreach ($coupons as $code => $percent) {
        $stmt->execute([':code' => $code, ':percent' => $percent]);
    }
    echo "Coupons migrated.\n";
}

// --- Migrate Carts ---
$cartsFile = __DIR__ . '/../data/user_carts.json';
if (file_exists($cartsFile)) {
    echo "Migrating Carts...\n";
    $carts = json_decode(file_get_contents($cartsFile), true);

    foreach ($carts as $userId => $items) {
        // Verify user exists first
        $userStmt = $pdo->prepare("SELECT id FROM users WHERE id = :id");
        $userStmt->execute([':id' => $userId]);
        if (!$userStmt->fetch()) {
            echo "Skipping cart for unknown user ID: $userId\n";
            continue;
        }

        // Create Cart
        $cartStmt = $pdo->prepare("INSERT INTO carts (user_id) VALUES (:user_id) RETURNING id");
        $cartStmt->execute([':user_id' => $userId]);
        $cartId = $cartStmt->fetchColumn();

        // Insert Items
        $itemStmt = $pdo->prepare("
            INSERT INTO cart_items (cart_id, product_id, quantity) 
            VALUES (:cart_id, :product_id, :quantity)
            ON CONFLICT (cart_id, product_id) DO UPDATE SET quantity = EXCLUDED.quantity
        ");

        foreach ($items as $productId => $quantity) {
            // Verify product exists
            $prodStmt = $pdo->prepare("SELECT id FROM products WHERE id = :id");
            $prodStmt->execute([':id' => $productId]);
            if (!$prodStmt->fetch()) {
                continue; // Skip unknown products
            }

            $itemStmt->execute([
                ':cart_id' => $cartId,
                ':product_id' => $productId,
                ':quantity' => $quantity
            ]);
        }
    }
    echo "Carts migrated.\n";
}

// --- Migrate Wishlists ---
$wishlistFile = __DIR__ . '/../data/user_wishlists.json';
if (file_exists($wishlistFile)) {
    echo "Migrating Wishlists...\n";
    $wishlists = json_decode(file_get_contents($wishlistFile), true);

    $stmt = $pdo->prepare("
        INSERT INTO wishlists (user_id, product_id) 
        VALUES (:user_id, :product_id)
        ON CONFLICT (user_id, product_id) DO NOTHING
    ");

    foreach ($wishlists as $userId => $productIds) {
        // Verify user exists
        $userStmt = $pdo->prepare("SELECT id FROM users WHERE id = :id");
        $userStmt->execute([':id' => $userId]);
        if (!$userStmt->fetch())
            continue;

        foreach ($productIds as $productId) {
            // Verify product exists
            $prodStmt = $pdo->prepare("SELECT id FROM products WHERE id = :id");
            $prodStmt->execute([':id' => $productId]);
            if (!$prodStmt->fetch())
                continue;

            $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
        }
    }
    echo "Wishlists migrated.\n";
}

// --- Migrate Saved Items ---
$savedItemsFile = __DIR__ . '/../data/user_saved_items.json';
if (file_exists($savedItemsFile)) {
    echo "Migrating Saved Items...\n";
    $savedItems = json_decode(file_get_contents($savedItemsFile), true);

    $stmt = $pdo->prepare("
        INSERT INTO saved_items (user_id, product_id) 
        VALUES (:user_id, :product_id)
        ON CONFLICT (user_id, product_id) DO NOTHING
    ");

    foreach ($savedItems as $userId => $productIds) {
        // Verify user exists
        $userStmt = $pdo->prepare("SELECT id FROM users WHERE id = :id");
        $userStmt->execute([':id' => $userId]);
        if (!$userStmt->fetch())
            continue;

        foreach ($productIds as $productId) {
            // Verify product exists
            $prodStmt = $pdo->prepare("SELECT id FROM products WHERE id = :id");
            $prodStmt->execute([':id' => $productId]);
            if (!$prodStmt->fetch())
                continue;

            $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
        }
    }
    echo "Saved Items migrated.\n";
}

echo "Migration complete!\n";
