<?php
require_once __DIR__ . '/../vendor/autoload.php';

use EasyCart\Core\Database;

// Load constants
require_once __DIR__ . '/../config/constants.php';

$pdo = Database::getInstance()->getConnection();

echo "Seeding Categories...\n";
global $categories;
$stmt = $pdo->prepare("INSERT INTO categories (id, name, slug) VALUES (:id, :name, :slug) ON CONFLICT (id) DO NOTHING");

foreach ($categories as $cat) {
    $stmt->execute([
        ':id' => $cat['id'],
        ':name' => $cat['name'],
        ':slug' => $cat['slug']
    ]);
}

echo "Seeding Brands...\n";
global $brands;
$stmt = $pdo->prepare("INSERT INTO brands (id, name) VALUES (:id, :name) ON CONFLICT (id) DO NOTHING");

foreach ($brands as $brand) {
    $stmt->execute([
        ':id' => $brand['id'],
        ':name' => $brand['name']
    ]);
}

echo "Done seeding static data.\n";
