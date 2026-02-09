<?php
/**
 * Test script to verify new schema repositories work correctly
 */

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Database/Queries.php';
require_once __DIR__ . '/../app/Repositories/ProductRepository.php';
require_once __DIR__ . '/../app/Repositories/CategoryRepository.php';
require_once __DIR__ . '/../app/Repositories/BrandRepository.php';

use EasyCart\Repositories\ProductRepository;
use EasyCart\Repositories\CategoryRepository;
use EasyCart\Repositories\BrandRepository;

echo "=================================================\n";
echo "Testing New Schema Repositories\n";
echo "=================================================\n\n";

try {
    // Test ProductRepository
    echo "1. Testing ProductRepository...\n";
    $productRepo = new ProductRepository();

    echo "   - Getting all products... ";
    $products = $productRepo->getAll();
    echo "✓ Found " . count($products) . " products\n";

    if (!empty($products)) {
        $firstProduct = $products[0];
        echo "   - First product: {$firstProduct['name']} (ID: {$firstProduct['id']})\n";

        echo "   - Testing find by ID... ";
        $product = $productRepo->find($firstProduct['id']);
        echo ($product ? "✓ Found\n" : "✗ Not found\n");
    }

    echo "   - Getting featured products... ";
    $featured = $productRepo->getFeatured(5);
    echo "✓ Found " . count($featured) . " featured\n";

    echo "   - Getting new products... ";
    $new = $productRepo->getNew(5);
    echo "✓ Found " . count($new) . " new\n";

    echo "\n";

    // Test CategoryRepository
    echo "2. Testing CategoryRepository...\n";
    $categoryRepo = new CategoryRepository();

    echo "   - Getting all categories... ";
    $categories = $categoryRepo->getAll();
    echo "✓ Found " . count($categories) . " categories\n";

    if (!empty($categories)) {
        $firstCat = reset($categories);
        echo "   - First category: {$firstCat['name']} (ID: {$firstCat['id']})\n";

        echo "   - Getting products in category... ";
        $catProducts = $productRepo->findByCategory($firstCat['id']);
        echo "✓ Found " . count($catProducts) . " products\n";
    }

    echo "\n";

    // Test BrandRepository
    echo "3. Testing BrandRepository...\n";
    $brandRepo = new BrandRepository();

    echo "   - Getting all brands... ";
    $brands = $brandRepo->getAll();
    echo "✓ Found " . count($brands) . " brands\n";

    if (!empty($brands)) {
        $firstBrand = reset($brands);
        echo "   - First brand: {$firstBrand['name']}\n";
    }

    echo "\n";

    echo "=================================================\n";
    echo "✓ All repository tests passed!\n";
    echo "=================================================\n";

} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
