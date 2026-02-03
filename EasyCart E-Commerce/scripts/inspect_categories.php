<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EasyCart\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "=== Columns in 'categories' ===\n";
$stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'categories'");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
print_r($columns);

echo "\n=== Data in 'categories' ===\n";
$stmt = $pdo->query("SELECT * FROM categories WHERE name = 'Accessories'");
$cat = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($cat);
