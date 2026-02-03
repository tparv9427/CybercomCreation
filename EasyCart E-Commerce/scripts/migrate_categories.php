<?php
/**
 * Category Data Migration Script
 * Migrates categories from old schema to new EAV structure
 */

require_once __DIR__ . '/../app/Core/Database.php';

use EasyCart\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();

    echo "=================================================\n";
    echo "Category Data Migration\n";
    echo "=================================================\n\n";

    $pdo->beginTransaction();

    // Step 1: Migrate core category data
    echo "Step 1: Migrating core category data...\n";

    $stmt = $pdo->query("
        INSERT INTO catalog_category_entity (entity_id, name, slug, is_active)
        SELECT id, name, slug, TRUE
        FROM categories
        ON CONFLICT (entity_id) DO NOTHING
    ");

    $categoryCount = $stmt->rowCount();
    echo "✓ Migrated $categoryCount categories\n\n";

    // Update sequence to match max ID
    echo "Step 2: Updating sequences...\n";

    $pdo->exec("
        SELECT setval('catalog_category_entity_entity_id_seq', 
            (SELECT MAX(entity_id) FROM catalog_category_entity), true)
    ");

    echo "✓ Sequences updated\n\n";

    $pdo->commit();

    // Verification
    echo "=================================================\n";
    echo "Verification\n";
    echo "=================================================\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    $oldCount = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM catalog_category_entity");
    $newCount = $stmt->fetchColumn();

    echo "Old categories table: $oldCount records\n";
    echo "New catalog_category_entity: $newCount records\n";

    if ($oldCount == $newCount) {
        echo "✓ Category counts match!\n";
    } else {
        echo "✗ WARNING: Category counts don't match!\n";
    }

    echo "\n=================================================\n";
    echo "Category migration completed successfully!\n";
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
