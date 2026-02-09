<?php
require_once __DIR__ . '/../config/autoload.php';
use EasyCart\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->query("SELECT entity_id, name, sku FROM catalog_product_entity LIMIT 5");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "New Products table sample:\n";
    foreach ($products as $p) {
        echo "ID: {$p['entity_id']}, Name: {$p['name']}, SKU: {$p['sku']}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
