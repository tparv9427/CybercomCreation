<?php
require_once __DIR__ . '/../config/autoload.php';
use EasyCart\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->query("SELECT * FROM catalog_product_image LIMIT 10");
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Product Images found: " . count($images) . "\n";
    foreach ($images as $img) {
        echo "Product ID: {$img['product_entity_id']}, Path: {$img['image_path']}, Primary: {$img['is_primary']}\n";
    }

    $stmt = $pdo->query("SELECT COUNT(*) FROM catalog_product_entity");
    echo "Total Products: " . $stmt->fetchColumn() . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
