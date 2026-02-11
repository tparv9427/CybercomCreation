<?php
/**
 * Migration: Add URL keys to categories and brands
 */

// Bootstrap autoloader and config
require_once __DIR__ . '/config/autoload.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/constants.php';

use EasyCart\Core\Database;

try {
    $db = Database::getInstance()->getConnection();

    echo "Starting migration...\n";

    // 1. Add url_key to catalog_category_entity
    echo "Adding url_key to catalog_category_entity...\n";
    $db->exec("ALTER TABLE catalog_category_entity ADD COLUMN IF NOT EXISTS url_key VARCHAR(255) UNIQUE");

    $rewriteService = new \EasyCart\Services\UrlRewriteService();

    // 2. Migrate Products
    echo "Migrating Product URLs...\n";
    $products = $db->query("SELECT entity_id, name FROM catalog_product_entity")->fetchAll();
    foreach ($products as $p) {
        $rewriteService->saveProductRewrite($p['entity_id'], $p['name']);
    }

    // 3. Migrate Categories
    echo "Migrating Category URLs...\n";
    $categories = $db->query("SELECT entity_id, name FROM catalog_category_entity")->fetchAll();
    foreach ($categories as $c) {
        $rewriteService->saveCategoryRewrite($c['entity_id'], $c['name']);
    }

    // 4. Migrate Brands
    echo "Migrating Brand URLs...\n";
    $brandResource = new \EasyCart\Resource\Resource_Brand();
    $brands = $brandResource->getAllBrands();
    foreach ($brands as $b) {
        $rewriteService->saveBrandRewrite($b['id'], $b['name']);
    }

    echo "Migration completed successfully.\n";
} catch (\Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}