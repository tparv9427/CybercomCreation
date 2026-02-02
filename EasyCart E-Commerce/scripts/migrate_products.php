<?php
require_once __DIR__ . '/../vendor/autoload.php';

use EasyCart\Core\Database;

$pdo = Database::getInstance()->getConnection();
$productsFile = __DIR__ . '/../legacy/products.json';

if (!file_exists($productsFile)) {
    die("Products file not found.\n");
}

$products = json_decode(file_get_contents($productsFile), true);

echo "Analyzing " . count($products) . " products for Brand Images...\n";

// 1. First Pass: Find one representative image per Brand
$brandImages = [];
foreach ($products as $p) {
    if (!isset($p['brand_id']))
        continue;

    // If we haven't found an image for this brand yet
    if (!isset($brandImages[$p['brand_id']])) {
        // Check if product has an image/icon
        // Note: Logic allows for 'images' array or 'image' string field. 
        // Based on JSON viewed earlier, it has 'images' array.
        if (isset($p['images']) && is_array($p['images']) && !empty($p['images'])) {
            $brandImages[$p['brand_id']] = $p['images'][0]; // Pick first image
        }
    }
}

echo "Identified images for " . count($brandImages) . " brands.\n";

// 2. Second Pass: Insert Products
echo "Migrating Products...\n";

$stmt = $pdo->prepare("
    INSERT INTO products 
    (id, category_id, brand_id, name, slug, price, original_price, stock, description, image, is_featured, is_new, rating, reviews_count) 
    VALUES 
    (:id, :category_id, :brand_id, :name, :slug, :price, :original_price, :stock, :description, :image, :is_featured, :is_new, :rating, :reviews_count)
    ON CONFLICT (id) DO NOTHING
");

foreach ($products as $p) {
    $brandId = $p['brand_id'];

    // Apply the chosen brand image
    $image = isset($brandImages[$brandId]) ? $brandImages[$brandId] : null;

    $stmt->execute([
        ':id' => $p['id'],
        ':category_id' => $p['category_id'],
        ':brand_id' => $brandId,
        ':name' => $p['name'],
        ':slug' => $p['slug'],
        ':price' => $p['price'],
        ':original_price' => isset($p['original_price']) ? $p['original_price'] : $p['price'],
        ':stock' => isset($p['stock']) ? (int) $p['stock'] : 0,
        ':description' => isset($p['description']) ? $p['description'] : '',
        ':image' => $image,
        ':is_featured' => isset($p['featured']) ? ($p['featured'] ? 'true' : 'false') : 'false',
        ':is_new' => isset($p['new']) ? ($p['new'] ? 'true' : 'false') : 'false',
        ':rating' => isset($p['rating']) ? $p['rating'] : 0,
        ':reviews_count' => isset($p['reviews_count']) ? $p['reviews_count'] : 0
    ]);
}

echo "Done migrating products.\n";
