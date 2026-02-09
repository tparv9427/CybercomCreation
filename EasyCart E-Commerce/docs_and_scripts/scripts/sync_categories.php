<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EasyCart\Core\Database;

$pdo = Database::getInstance()->getConnection();

// Get existing categories in new schema to avoid duplicates
$stmt = $pdo->query("SELECT slug FROM catalog_category_entity");
$existingSlugs = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get all categories from old schema
$stmt = $pdo->query("SELECT * FROM categories");
$oldCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$addedCount = 0;

foreach ($oldCategories as $oldCat) {
    if (!in_array($oldCat['slug'], $existingSlugs)) {
        echo "Migrating: " . $oldCat['name'] . " (" . $oldCat['slug'] . ")\n";

        $insertStmt = $pdo->prepare("
            INSERT INTO catalog_category_entity (name, slug, is_active)
            VALUES (:name, :slug, true)
        ");

        try {
            $insertStmt->execute([
                ':name' => $oldCat['name'],
                ':slug' => $oldCat['slug']
            ]);
            $addedCount++;
            echo " - Success\n";
        } catch (Exception $e) {
            echo " - Failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Skipping: " . $oldCat['name'] . " (already exists)\n";
    }
}

echo "\nMigration existing. Added $addedCount categories.\n";
