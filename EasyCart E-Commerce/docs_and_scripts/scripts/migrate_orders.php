<?php
/**
 * Order Data Migration Script
 * Migrates orders from old schema to new structure with snapshots
 */

require_once __DIR__ . '/../app/Core/Database.php';

use EasyCart\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();

    echo "=================================================\n";
    echo "Order Data Migration\n";
    echo "=================================================\n\n";

    $pdo->beginTransaction();

    // Step 1: Migrate order headers
    echo "Step 1: Migrating order headers...\n";

    $stmt = $pdo->query("
        INSERT INTO sales_order (
            order_id, order_number, user_id, subtotal, 
            shipping_cost, tax, total, status, created_at
        )
        SELECT 
            id, 
            order_number, 
            user_id, 
            subtotal, 
            shipping_cost, 
            tax, 
            total, 
            status, 
            created_at
        FROM orders
        ON CONFLICT (order_id) DO NOTHING
    ");

    $orderCount = $stmt->rowCount();
    echo "✓ Migrated $orderCount orders\n\n";

    // Step 2: Migrate order items with product snapshots
    echo "Step 2: Migrating order items with product snapshots...\n";

    $stmt = $pdo->query("
        INSERT INTO sales_order_product (
            order_id, product_entity_id, product_name, product_sku, 
            product_price, quantity, row_total
        )
        SELECT 
            oi.order_id,
            oi.product_id,
            COALESCE(p.name, 'Unknown Product'),
            COALESCE(p.slug, 'unknown'),
            oi.price,
            oi.quantity,
            (oi.price * oi.quantity)
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
    ");

    $itemCount = $stmt->rowCount();
    echo "✓ Migrated $itemCount order items\n\n";

    // Update sequences
    echo "Step 3: Updating sequences...\n";

    $pdo->exec("
        SELECT setval('sales_order_order_id_seq', 
            (SELECT MAX(order_id) FROM sales_order), true)
    ");

    echo "✓ Sequences updated\n\n";

    $pdo->commit();

    // Verification
    echo "=================================================\n";
    echo "Verification\n";
    echo "=================================================\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
    $oldOrderCount = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM sales_order");
    $newOrderCount = $stmt->fetchColumn();

    echo "Old orders table: $oldOrderCount records\n";
    echo "New sales_order: $newOrderCount records\n";

    if ($oldOrderCount == $newOrderCount) {
        echo "✓ Order counts match!\n";
    } else {
        echo "✗ WARNING: Order counts don't match!\n";
    }

    $stmt = $pdo->query("SELECT COUNT(*) FROM order_items");
    $oldItemCount = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM sales_order_product");
    $newItemCount = $stmt->fetchColumn();

    echo "\nOld order_items table: $oldItemCount records\n";
    echo "New sales_order_product: $newItemCount records\n";

    if ($oldItemCount == $newItemCount) {
        echo "✓ Order item counts match!\n";
    } else {
        echo "✗ WARNING: Order item counts don't match!\n";
    }

    echo "\n=================================================\n";
    echo "Order migration completed successfully!\n";
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
