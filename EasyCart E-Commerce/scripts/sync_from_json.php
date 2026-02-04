<?php
/**
 * Sync Products from JSON to Database
 * Ensures database matches data/products.json and maps images correctly.
 */

require_once __DIR__ . '/../config/autoload.php';
use EasyCart\Core\Database;

try {
    $jsonPath = __DIR__ . '/../data/products.json';
    if (!file_exists($jsonPath)) {
        die("Error: products.json not found at $jsonPath\n");
    }

    $products = json_decode(file_get_contents($jsonPath), true);
    if (!$products) {
        die("Error: Failed to decode products.json\n");
    }

    $pdo = Database::getInstance()->getConnection();
    $pdo->beginTransaction();

    echo "Truncating existing product data for a clean sync...\n";
    $pdo->exec("TRUNCATE TABLE catalog_product_image CASCADE");
    $pdo->exec("TRUNCATE TABLE catalog_product_attribute CASCADE");
    $pdo->exec("TRUNCATE TABLE catalog_category_product CASCADE");
    $pdo->exec("TRUNCATE TABLE catalog_product_entity CASCADE");

    echo "Syncing " . count($products) . " products...\n";

    $stmtInsertProduct = $pdo->prepare("
        INSERT INTO catalog_product_entity (
            entity_id, sku, name, price, original_price, stock, 
            description, is_featured, is_new, rating, reviews_count
        ) VALUES (
            :id, :sku, :name, :price, :original_price, :stock,
            :description, :is_featured, :is_new, :rating, :reviews_count
        )
    ");

    $stmtInsertImage = $pdo->prepare("
        INSERT INTO catalog_product_image (product_entity_id, image_path, is_primary, image_position)
        VALUES (:product_id, :image_path, :is_primary, :position)
    ");

    $stmtInsertAttr = $pdo->prepare("
        INSERT INTO catalog_product_attribute (product_entity_id, attribute_code, attribute_value)
        VALUES (:product_id, :code, :value)
    ");

    $stmtInsertCatProd = $pdo->prepare("
        INSERT INTO catalog_category_product (category_entity_id, product_entity_id, position)
        VALUES (:cat_id, :prod_id, 0)
    ");

    $count = 0;
    foreach ($products as $id => $p) {
        // 1. Insert Entity
        $stmtInsertProduct->execute([
            ':id' => $p['id'],
            ':sku' => $p['slug'] ?? 'sku-' . $p['id'],
            ':name' => $p['name'],
            ':price' => (float) ($p['price'] ?? 0),
            ':original_price' => (float) ($p['original_price'] ?? $p['price'] ?? 0),
            ':stock' => (int) ($p['stock'] ?? 0),
            ':description' => $p['description'] ?? '',
            ':is_featured' => !empty($p['featured']) ? 1 : 0,
            ':is_new' => !empty($p['new']) ? 1 : 0,
            ':rating' => (float) ($p['rating'] ?? 0),
            ':reviews_count' => (int) ($p['reviews_count'] ?? 0)
        ]);

        // 2. Insert Images
        if (isset($p['images']) && is_array($p['images'])) {
            foreach ($p['images'] as $index => $imgPath) {
                // Stripping 'assets/images/products/' if it exists to keep paths clean in DB
                $cleanPath = str_replace('assets/images/products/', '', $imgPath);
                $stmtInsertImage->execute([
                    ':product_id' => $p['id'],
                    ':image_path' => $cleanPath,
                    ':is_primary' => ($index === 0) ? 1 : 0,
                    ':position' => $index
                ]);
            }
        }

        // 3. Insert Brand (as attribute)
        if (isset($p['specifications']['Brand'])) {
            $stmtInsertAttr->execute([
                ':product_id' => $p['id'],
                ':code' => 'brand',
                ':value' => $p['specifications']['Brand']
            ]);
        }

        // 4. Insert Category Relation
        if (isset($p['category_id'])) {
            $stmtInsertCatProd->execute([
                ':cat_id' => $p['category_id'],
                ':prod_id' => $p['id']
            ]);
        }

        $count++;
        if ($count % 50 === 0)
            echo "Processed $count products...\n";
    }

    // Reset sequence
    $pdo->exec("SELECT setval('catalog_product_entity_entity_id_seq', (SELECT MAX(entity_id) FROM catalog_product_entity))");

    $pdo->commit();
    echo "\nSuccessfully synchronized $count products from JSON to Database.\n";

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "\nError: " . $e->getMessage() . "\n";
}
