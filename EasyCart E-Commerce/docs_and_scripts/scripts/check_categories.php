<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EasyCart\Core\Database;

$pdo = Database::getInstance()->getConnection();

$stmt = $pdo->query("SELECT * FROM catalog_category_entity ORDER BY entity_id");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total Categories Found: " . count($categories) . "\n\n";

foreach ($categories as $cat) {
    echo "ID: " . $cat['entity_id'] . "\n";
    echo "Name: " . $cat['name'] . "\n";
    echo "Status: " . ($cat['is_active'] ? 'Active' : 'Inactive') . "\n";
    echo "-------------------\n";
}
