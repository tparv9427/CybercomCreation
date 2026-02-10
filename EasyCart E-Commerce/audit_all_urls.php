<?php
require_once __DIR__ . '/config/autoload.php';
require_once __DIR__ . '/config/app.php';

use EasyCart\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT entity_id, name, url_key FROM catalog_product_entity ORDER BY entity_id DESC LIMIT 100");
    $results = $stmt->fetchAll();

    echo "ID | Name | URL Key\n";
    echo str_repeat("-", 80) . "\n";
    foreach ($results as $row) {
        printf("%-5d | %-40s | %s\n", $row['entity_id'], $row['name'], $row['url_key']);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
