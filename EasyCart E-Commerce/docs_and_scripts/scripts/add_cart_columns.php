<?php
require_once __DIR__ . '/../app/Core/Database.php';

use EasyCart\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();

    echo "Adding columns to carts table...\n";

    // Add shipping_method if not exists
    $pdo->exec("ALTER TABLE carts ADD COLUMN IF NOT EXISTS shipping_method VARCHAR(50)");

    // Add payment_method if not exists
    $pdo->exec("ALTER TABLE carts ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50)");

    echo "Columns added successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
