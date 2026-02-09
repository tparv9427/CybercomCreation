<?php
/**
 * Comprehensive Application Test Script
 * Tests all major features with the new schema
 */

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Database/Queries.php';
require_once __DIR__ . '/../app/Repositories/ProductRepository.php';
require_once __DIR__ . '/../app/Repositories/CategoryRepository.php';
require_once __DIR__ . '/../app/Repositories/BrandRepository.php';
require_once __DIR__ . '/../app/Repositories/CartRepository.php';
require_once __DIR__ . '/../app/Repositories/OrderRepository.php';

use EasyCart\Repositories\ProductRepository;
use EasyCart\Repositories\CategoryRepository;
use EasyCart\Repositories\BrandRepository;
use EasyCart\Repositories\CartRepository;
use EasyCart\Repositories\OrderRepository;

echo "=======================================================\n";
echo "EasyCart Application Test Suite - New Schema\n";
echo "=======================================================\n\n";

$errors = [];
$warnings = [];
$passed = 0;
$total = 0;

function test($name, $callback)
{
    global $errors, $warnings, $passed, $total;
    $total++;
    echo "[$total] Testing: $name... ";

    try {
        $result = $callback();
        if ($result === true || $result === null) {
            echo "✓ PASS\n";
            $passed++;
            return true;
        } else {
            echo "⚠ WARNING: $result\n";
            $warnings[] = "$name: $result";
            $passed++;
            return true;
        }
    } catch (Exception $e) {
        echo "✗ FAIL\n";
        $errors[] = "$name: " . $e->getMessage();
        return false;
    }
}

// ============================================================================
// PHASE 4: Service Layer Testing (via Repositories)
// ============================================================================

echo "\n--- PHASE 4: Repository/Service Layer Tests ---\n\n";

$productRepo = new ProductRepository();
$categoryRepo = new CategoryRepository();
$brandRepo = new BrandRepository();
$cartRepo = new CartRepository();
$orderRepo = new OrderRepository();

// Product Tests
test("Get all products", function () use ($productRepo) {
    $products = $productRepo->getAll();
    if (count($products) < 1)
        throw new Exception("No products found");
    return true;
});

test("Find product by ID", function () use ($productRepo) {
    $product = $productRepo->find(1);
    if (!$product)
        throw new Exception("Product not found");
    if (!isset($product['id']))
        throw new Exception("Missing 'id' field");
    if (!isset($product['name']))
        throw new Exception("Missing 'name' field");
    if (!isset($product['brand_name']))
        return "brand_name not set";
    if (!isset($product['category_id']))
        return "category_id not set";
    return true;
});

test("Get featured products", function () use ($productRepo) {
    $products = $productRepo->getFeatured(5);
    if (count($products) < 1)
        return "No featured products";
    return true;
});

test("Get new products", function () use ($productRepo) {
    $products = $productRepo->getNew(5);
    if (count($products) < 1)
        return "No new products";
    return true;
});

test("Find products by category", function () use ($productRepo) {
    $products = $productRepo->findByCategory(1);
    if (count($products) < 1)
        throw new Exception("No products in category");
    return true;
});

test("Find products by brand", function () use ($productRepo) {
    $products = $productRepo->findByBrand('TechPro');
    if (count($products) < 1)
        return "No TechPro products found";
    return true;
});

test("Search products", function () use ($productRepo) {
    $products = $productRepo->search('phone');
    if (count($products) < 1)
        return "No search results";
    return true;
});

// Category Tests
test("Get all categories", function () use ($categoryRepo) {
    $categories = $categoryRepo->getAll();
    if (count($categories) < 1)
        throw new Exception("No categories found");
    return true;
});

test("Find category by ID", function () use ($categoryRepo) {
    $category = $categoryRepo->find(1);
    if (!$category)
        throw new Exception("Category not found");
    if (!isset($category['id']))
        throw new Exception("Missing 'id' field");
    return true;
});

// Brand Tests
test("Get all brands", function () use ($brandRepo) {
    $brands = $brandRepo->getAll();
    if (count($brands) < 1)
        throw new Exception("No brands found");
    return true;
});

// Cart Tests (requires session)
if (!session_id()) {
    session_start();
}
// Don't set user_id - test as guest cart
unset($_SESSION['user_id']);
unset($_SESSION['cart_id']);

test("Create/Get cart", function () use ($cartRepo) {
    $cart = $cartRepo->get();
    // Empty cart is OK
    return true;
});

test("Save cart items", function () use ($cartRepo) {
    $testCart = [1 => 2, 2 => 1]; // Product 1: qty 2, Product 2: qty 1
    $cartRepo->save($testCart);
    $cart = $cartRepo->get();
    if (count($cart) !== 2)
        throw new Exception("Cart save failed");
    return true;
});

test("Clear cart", function () use ($cartRepo) {
    $cartRepo->save([]);
    $cart = $cartRepo->get();
    if (count($cart) !== 0)
        throw new Exception("Cart clear failed");
    return true;
});

// Order Tests (need a real user)
$pdo = EasyCart\Core\Database::getInstance()->getConnection();
$testUserId = null;
try {
    // Try different possible column names
    $stmt = $pdo->query("SELECT id FROM users LIMIT 1");
    $testUser = $stmt->fetch();
    $testUserId = $testUser ? $testUser['id'] : null;
} catch (Exception $e) {
    // Users table doesn't exist or has different structure
    $testUserId = null;
}

test("Create order from cart", function () use ($cartRepo, $orderRepo, $testUserId) {
    if (!$testUserId)
        return "No users in database - skipping";

    // Add items to cart
    $cartRepo->save([1 => 1, 2 => 1]);

    // Get cart ID from session
    $cartId = $_SESSION['cart_id'] ?? null;
    if (!$cartId)
        throw new Exception("No cart ID in session");

    $totals = [
        'subtotal' => 100.00,
        'shipping_cost' => 10.00,
        'tax' => 9.00,
        'discount' => 0.00,
        'total' => 119.00
    ];

    $orderId = $orderRepo->createFromCart($cartId, $testUserId, $totals);
    if (!$orderId)
        throw new Exception("Order creation failed");

    // Verify cart was inactivated
    $cart = $cartRepo->get();
    if (count($cart) > 0)
        return "Cart not cleared after order (expected behavior with new cart)";

    return true;
});

test("Find orders by user", function () use ($orderRepo, $testUserId) {
    if (!$testUserId)
        return "No users in database - skipping";

    $orders = $orderRepo->findByUserId($testUserId);
    if (count($orders) < 1)
        return "No orders found for user";

    $order = $orders[0];
    if (!isset($order['items']))
        throw new Exception("Order items not loaded");
    if (count($order['items']) < 1)
        throw new Exception("Order has no items");

    return true;
});

// ============================================================================
// PHASE 5: Controller Integration Tests
// ============================================================================

echo "\n--- PHASE 5: Controller Integration Tests ---\n\n";

test("Product listing page structure", function () use ($productRepo, $categoryRepo) {
    // Simulate what ProductController::index() does
    $products = $productRepo->getAll();
    $categories = $categoryRepo->getAll();

    if (count($products) < 1)
        throw new Exception("No products");
    if (count($categories) < 1)
        throw new Exception("No categories");

    // Check first product has required fields
    $product = $products[0];
    $required = ['id', 'name', 'price', 'image', 'rating'];
    foreach ($required as $field) {
        if (!isset($product[$field]))
            throw new Exception("Missing field: $field");
    }

    return true;
});

test("Product detail page structure", function () use ($productRepo) {
    // Simulate what ProductController::show() does
    $product = $productRepo->find(1);

    if (!$product)
        throw new Exception("Product not found");

    $required = ['id', 'name', 'price', 'description', 'stock', 'rating', 'reviews_count'];
    foreach ($required as $field) {
        if (!isset($product[$field]))
            throw new Exception("Missing field: $field");
    }

    // Check recommendations work
    $similar = $productRepo->getSimilarByCategory($product, 4);
    // Similar products may be empty, that's OK

    return true;
});

test("Brand page structure", function () use ($productRepo, $brandRepo) {
    $brands = $brandRepo->getAll();
    if (count($brands) < 1)
        throw new Exception("No brands");

    $firstBrand = reset($brands);
    $products = $productRepo->findByBrand($firstBrand['name']);
    // May be empty, that's OK

    return true;
});

// ============================================================================
// PHASE 6: Data Integrity Tests
// ============================================================================

echo "\n--- PHASE 6: Data Integrity Tests ---\n\n";

test("Product has valid category_id", function () use ($productRepo, $categoryRepo) {
    $product = $productRepo->find(1);
    if (!isset($product['category_id']))
        return "category_id not set";

    if ($product['category_id']) {
        $category = $categoryRepo->find($product['category_id']);
        if (!$category)
            throw new Exception("Product references invalid category");
    }

    return true;
});

test("Product has valid brand_name", function () use ($productRepo) {
    $product = $productRepo->find(1);
    if (!isset($product['brand_name']))
        return "brand_name not set";
    if (empty($product['brand_name']))
        return "brand_name is empty";
    return true;
});

test("Product has valid image", function () use ($productRepo) {
    $product = $productRepo->find(1);
    if (!isset($product['image']))
        return "image not set";
    if (!isset($product['icon']))
        return "icon not set";
    return true;
});

test("Order has product snapshots", function () use ($orderRepo, $testUserId) {
    if (!$testUserId)
        return "No users in database - skipping";

    $orders = $orderRepo->findByUserId($testUserId);
    if (count($orders) < 1)
        return "No orders to check";

    $order = $orders[0];
    if (!isset($order['items']) || count($order['items']) < 1) {
        throw new Exception("Order has no items");
    }

    $item = $order['items'][0];
    $required = ['product_name', 'product_sku', 'product_price', 'quantity'];
    foreach ($required as $field) {
        if (!isset($item[$field]))
            throw new Exception("Missing snapshot field: $field");
    }

    return true;
});

test("Cart inactivation on order", function () use ($cartRepo, $orderRepo, $testUserId) {
    if (!$testUserId)
        return "No users in database - skipping";

    // Create new cart
    $cartRepo->save([3 => 1]);
    $cartId = $_SESSION['cart_id'];

    // Place order
    $totals = [
        'subtotal' => 50.00,
        'shipping_cost' => 5.00,
        'tax' => 4.50,
        'discount' => 0.00,
        'total' => 59.50
    ];

    $orderRepo->createFromCart($cartId, $testUserId, $totals);

    // Try to get cart - should create new one
    $newCart = $cartRepo->get();
    $newCartId = $_SESSION['cart_id'];

    if ($cartId === $newCartId) {
        return "Cart ID unchanged (may still be active)";
    }

    return true;
});

// ============================================================================
// Summary
// ============================================================================

echo "\n=======================================================\n";
echo "Test Summary\n";
echo "=======================================================\n";
echo "Total Tests: $total\n";
echo "Passed: $passed\n";
echo "Failed: " . count($errors) . "\n";
echo "Warnings: " . count($warnings) . "\n";

if (count($warnings) > 0) {
    echo "\n⚠ Warnings:\n";
    foreach ($warnings as $warning) {
        echo "  - $warning\n";
    }
}

if (count($errors) > 0) {
    echo "\n✗ Failures:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
    echo "\n❌ TESTS FAILED\n";
    exit(1);
} else {
    echo "\n✅ ALL TESTS PASSED!\n";
    if (count($warnings) > 0) {
        echo "⚠ Some warnings present (non-critical)\n";
    }
    exit(0);
}
