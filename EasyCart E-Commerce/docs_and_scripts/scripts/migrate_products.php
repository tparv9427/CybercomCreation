<?php
/**
 * Product Data Migration Script
 * Migrates products from old schema to new EAV structure
 */

require_once __DIR__ . '/../app/Core/Database.php';

use EasyCart\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();

    echo "=================================================\n";
    echo "Product Data Migration\n";
    echo "=================================================\n\n";

    $pdo->beginTransaction();

    // Step 1: Migrate core product data
    echo "Step 1: Migrating core product data...\n";

    $stmt = $pdo->query("
        INSERT INTO catalog_product_entity (
            entity_id, sku, name, price, original_price, stock, 
            description, is_featured, is_new, rating, reviews_count, created_at
        )
        SELECT 
            id, 
            slug, 
            name, 
            price, 
            original_price, 
            stock, 
            description, 
            is_featured, 
            is_new, 
            rating, 
            reviews_count, 
            created_at
        FROM products
        ON CONFLICT (entity_id) DO NOTHING
    ");

    $productCount = $stmt->rowCount();
    echo "✓ Migrated $productCount products\n\n";

    // Step 2: Migrate brand as attribute
    echo "Step 2: Migrating brands as product attributes...\n";

    $stmt = $pdo->query("
        INSERT INTO catalog_product_attribute (product_entity_id, attribute_code, attribute_value)
        SELECT p.id, 'brand', b.name
        FROM products p
        JOIN brands b ON p.brand_id = b.id
        WHERE b.name IS NOT NULL
    ");

    $brandCount = $stmt->rowCount();
    echo "✓ Migrated $brandCount brand attributes\n\n";

    // Step 3: Migrate product images
    echo "Step 3: Migrating product images...\n";

    $stmt = $pdo->query("
        INSERT INTO catalog_product_image (product_entity_id, image_path, is_primary, image_position)
        SELECT id, image, TRUE, 0
        FROM products
        WHERE image IS NOT NULL AND image != ''
    ");

    $imageCount = $stmt->rowCount();
    echo "✓ Migrated $imageCount product images\n\n";

    // Step 4: Migrate category relationships
    echo "Step 4: Migrating category-product relationships...\n";

    $stmt = $pdo->query("
        INSERT INTO catalog_category_product (category_entity_id, product_entity_id, position)
        SELECT category_id, id, 0
        FROM products
        WHERE category_id IS NOT NULL
        ON CONFLICT (category_entity_id, product_entity_id) DO NOTHING
    ");

    $relationCount = $stmt->rowCount();
    echo "✓ Migrated $relationCount category-product relationships\n\n";

    // Update sequence to match max ID
    echo "Step 5: Updating sequences...\n";

    $pdo->exec("
        SELECT setval('catalog_product_entity_entity_id_seq', 
            (SELECT MAX(entity_id) FROM catalog_product_entity), true)
    ");

    echo "✓ Sequences updated\n\n";

    $pdo->commit();

    // Verification
    echo "=================================================\n";
    echo "Verification\n";
    echo "=================================================\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $oldCount = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM catalog_product_entity");
    $newCount = $stmt->fetchColumn();

    echo "Old products table: $oldCount records\n";
    echo "New catalog_product_entity: $newCount records\n";

    if ($oldCount == $newCount) {
        echo "✓ Product counts match!\n";
    } else {
        echo "✗ WARNING: Product counts don't match!\n";
    }

    $stmt = $pdo->query("SELECT COUNT(*) FROM catalog_product_attribute");
    $attrCount = $stmt->fetchColumn();
    echo "Product attributes: $attrCount records\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM catalog_product_image");
    $imgCount = $stmt->fetchColumn();
    echo "Product images: $imgCount records\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM catalog_category_product");
    $relCount = $stmt->fetchColumn();
    echo "Category-product relations: $relCount records\n";

    echo "\n=================================================\n";
    echo "Product migration completed successfully!\n";
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
