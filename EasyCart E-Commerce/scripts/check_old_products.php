<?php
require_once __DIR__ . '/../config/autoload.php';
use EasyCart\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->query("SELECT id, name, image FROM products LIMIT 10");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Old Products table sample:\n";
    foreach ($products as $p) {
        echo "ID: {$p['id']}, Name: {$p['name']}, Image: {$p['image']}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
