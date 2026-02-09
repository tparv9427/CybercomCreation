<?php
/**
 * Cart Data Migration Script
 * Migrates carts from old schema to new structure
 */

require_once __DIR__ . '/../app/Core/Database.php';

use EasyCart\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();

    echo "=================================================\n";
    echo "Cart Data Migration\n";
    echo "=================================================\n\n";

    $pdo->beginTransaction();

    // Step 1: Migrate cart headers
    echo "Step 1: Migrating cart headers...\n";

    $stmt = $pdo->query("
        INSERT INTO sales_cart (cart_id, user_id, session_id, is_active, created_at, updated_at)
        SELECT id, user_id, session_id, TRUE, created_at, updated_at
        FROM carts
        ON CONFLICT (cart_id) DO NOTHING
    ");

    $cartCount = $stmt->rowCount();
    echo "✓ Migrated $cartCount carts\n\n";

    // Step 2: Migrate cart items
    echo "Step 2: Migrating cart items...\n";

    $stmt = $pdo->query("
        INSERT INTO sales_cart_product (cart_id, product_entity_id, quantity, created_at, updated_at)
        SELECT cart_id, product_id, quantity, created_at, NOW()
        FROM cart_items
        ON CONFLICT (cart_id, product_entity_id) DO NOTHING
    ");

    $itemCount = $stmt->rowCount();
    echo "✓ Migrated $itemCount cart items\n\n";

    // Update sequences
    echo "Step 3: Updating sequences...\n";

    $pdo->exec("
        SELECT setval('sales_cart_cart_id_seq', 
            (SELECT MAX(cart_id) FROM sales_cart), true)
    ");

    echo "✓ Sequences updated\n\n";

    $pdo->commit();

    // Verification
    echo "=================================================\n";
    echo "Verification\n";
    echo "=================================================\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM carts");
    $oldCartCount = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM sales_cart");
    $newCartCount = $stmt->fetchColumn();

    echo "Old carts table: $oldCartCount records\n";
    echo "New sales_cart: $newCartCount records\n";

    if ($oldCartCount == $newCartCount) {
        echo "✓ Cart counts match!\n";
    } else {
        echo "✗ WARNING: Cart counts don't match!\n";
    }

    $stmt = $pdo->query("SELECT COUNT(*) FROM cart_items");
    $oldItemCount = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM sales_cart_product");
    $newItemCount = $stmt->fetchColumn();

    echo "\nOld cart_items table: $oldItemCount records\n";
    echo "New sales_cart_product: $newItemCount records\n";

    if ($oldItemCount == $newItemCount) {
        echo "✓ Cart item counts match!\n";
    } else {
        echo "✗ WARNING: Cart item counts don't match!\n";
    }

    echo "\n=================================================\n";
    echo "Cart migration completed successfully!\n";
    echo "=================================================\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
