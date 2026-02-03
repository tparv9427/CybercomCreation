<?php
/**
 * Database Schema Migration Script
 * Creates new tables with EAV pattern for catalog and cart-to-order workflow
 */

require_once __DIR__ . '/../app/Core/Database.php';

use EasyCart\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();

    echo "=================================================\n";
    echo "EasyCart Database Schema Migration\n";
    echo "=================================================\n\n";

    // Read the new schema SQL file
    $schemaFile = __DIR__ . '/../schema_new.sql';

    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: $schemaFile");
    }

    echo "Reading schema file: schema_new.sql\n";
    $sql = file_get_contents($schemaFile);

    if ($sql === false) {
        throw new Exception("Failed to read schema file");
    }

    echo "Executing schema creation...\n\n";

    // Execute the schema
    $pdo->exec($sql);

    echo "✓ Schema created successfully!\n\n";

    // Verify tables were created
    echo "Verifying created tables:\n";
    echo "-------------------------\n";

    $tables = [
        'catalog_product_entity',
        'catalog_product_attribute',
        'catalog_product_image',
        'catalog_category_entity',
        'catalog_category_attribute',
        'catalog_category_product',
        'sales_cart',
        'sales_cart_product',
        'sales_cart_address',
        'sales_cart_payment',
        'sales_order',
        'sales_order_product',
        'sales_order_address',
        'sales_order_payment'
    ];

    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_name = '$table'");
        $exists = $stmt->fetchColumn();

        if ($exists) {
            echo "✓ $table\n";
        } else {
            echo "✗ $table (FAILED)\n";
        }
    }

    echo "\n=================================================\n";
    echo "Migration completed successfully!\n";
    echo "=================================================\n";
    echo "\nNext steps:\n";
    echo "1. Run: php scripts/migrate_products.php\n";
    echo "2. Run: php scripts/migrate_categories.php\n";
    echo "3. Run: php scripts/migrate_carts.php\n";
    echo "4. Run: php scripts/migrate_orders.php\n";
    echo "5. Run: php scripts/verify_migration.php\n";

} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
