<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EasyCart\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "=== Checking 'catalog_category_entity' (New Schema) ===\n";
$stmt = $pdo->query("SELECT * FROM catalog_category_entity ORDER BY entity_id");
$new_cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Count: " . count($new_cats) . "\n";
foreach ($new_cats as $cat) {
    echo "- " . $cat['name'] . " (Status: " . ($cat['is_active'] ? 'Active' : 'Inactive') . ")\n";
}

echo "\n=== Checking 'categories' (Old Schema) ===\n";
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY id");
    $old_cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Count: " . count($old_cats) . "\n";
    foreach ($old_cats as $cat) {
        echo "- " . ($cat['name'] ?? 'N/A') . "\n";
    }
} catch (Exception $e) {
    echo "Error checking 'categories': " . $e->getMessage() . "\n";
}
