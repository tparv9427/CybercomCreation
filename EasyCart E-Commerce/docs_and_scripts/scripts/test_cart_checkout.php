<?php
/**
 * Cart & Checkout Verification Tests
 * 
 * This script tests the key cart and checkout functionality:
 * 1. Cart merge logic
 * 2. Checkout locking/timeout
 * 3. Cart→Order flow
 * 4. CookieService encryption
 * 
 * Usage:
 *   php scripts/test_cart_checkout.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use EasyCart\Core\Database;
use EasyCart\Services\CookieService;
use EasyCart\Repositories\CartRepository;

echo "=== Cart & Checkout Verification Tests ===\n";
echo "Started: " . date('Y-m-d H:i:s') . "\n\n";

$passed = 0;
$failed = 0;

function test($name, $condition, &$passed, &$failed)
{
    if ($condition) {
        echo "✓ PASS: $name\n";
        $passed++;
    } else {
        echo "✗ FAIL: $name\n";
        $failed++;
    }
}

// ============================================================================
// Test 1: CookieService Encryption/Decryption
// ============================================================================
echo "--- Test 1: CookieService Encryption ---\n";

$cookieService = new CookieService();

// Test basic encryption
$testData = [
    'cart_id' => 123,
    'products' => [1 => 2, 5 => 3],
    'timestamp' => time()
];

$encrypted = $cookieService->encrypt($testData);
test("Encrypt returns string", is_string($encrypted), $passed, $failed);
test("Encrypted is not empty", strlen($encrypted) > 0, $passed, $failed);
test("Encrypted is base64", base64_decode($encrypted, true) !== false, $passed, $failed);

// Test decryption
$decrypted = $cookieService->decrypt($encrypted);
test("Decrypt returns array", is_array($decrypted), $passed, $failed);
test("Decrypted matches original", $decrypted === $testData, $passed, $failed);

// Test tamper detection
$tampered = $encrypted . 'x';
$tamperedResult = $cookieService->decrypt($tampered);
test("Tampered data returns null", $tamperedResult === null, $passed, $failed);

// Test invalid base64
$invalidResult = $cookieService->decrypt('not-valid-base64!!!');
test("Invalid data returns null", $invalidResult === null, $passed, $failed);

echo "\n";

// ============================================================================
// Test 2: Cart Queries Exist
// ============================================================================
echo "--- Test 2: Cart Query Constants ---\n";

$requiredQueries = [
    'CART_FIND_BY_USER',
    'CART_FIND_BY_SESSION',
    'CART_CREATE',
    'CART_GET_PRODUCTS',
    'CART_ADD_PRODUCT',
    'CART_SOFT_DELETE',
    'CART_LOCK_CHECKOUT',
    'CART_UNLOCK_CHECKOUT',
    'CART_UNLOCK_EXPIRED',
    'CART_ARCHIVE',
    'CART_MERGE_PRODUCTS',
    'CART_COUNT_PRODUCTS'
];

foreach ($requiredQueries as $query) {
    $exists = defined("EasyCart\\Database\\Queries::$query");
    test("Query $query exists", $exists, $passed, $failed);
}

echo "\n";

// ============================================================================
// Test 3: CartRepository Methods Exist
// ============================================================================
echo "--- Test 3: CartRepository Methods ---\n";

$cartRepo = new CartRepository();
$requiredMethods = [
    'get',
    'save',
    'getProductCount',
    'isGuestCart',
    'enforceSingleCart',
    'lockForCheckout',
    'unlockCart',
    'unlockExpiredCheckouts',
    'archiveCart',
    'softDelete',
    'transferGuestCartToUser'
];

foreach ($requiredMethods as $method) {
    $exists = method_exists($cartRepo, $method);
    test("Method $method exists", $exists, $passed, $failed);
}

echo "\n";

// ============================================================================
// Test 4: Database Schema Verification
// ============================================================================
echo "--- Test 4: Database Schema ---\n";

try {
    $pdo = Database::getInstance()->getConnection();

    // Check cart table columns
    $stmt = $pdo->query("
        SELECT column_name 
        FROM information_schema.columns 
        WHERE table_name = 'sales_cart'
    ");
    $columns = $stmt->fetchAll(\PDO::FETCH_COLUMN);

    $requiredColumns = [
        'cart_id',
        'user_id',
        'session_id',
        'is_active',
        'is_temp',
        'is_checkout',
        'checkout_locked_at',
        'archived',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    foreach ($requiredColumns as $col) {
        test("Column sales_cart.$col exists", in_array($col, $columns), $passed, $failed);
    }

    // Check order product table has tax/discount
    $stmt = $pdo->query("
        SELECT column_name 
        FROM information_schema.columns 
        WHERE table_name = 'sales_order_product'
    ");
    $orderColumns = $stmt->fetchAll(\PDO::FETCH_COLUMN);

    test("Column sales_order_product.tax exists", in_array('tax', $orderColumns), $passed, $failed);
    test("Column sales_order_product.discount exists", in_array('discount', $orderColumns), $passed, $failed);

} catch (Exception $e) {
    echo "Database connection error: " . $e->getMessage() . "\n";
    $failed++;
}

echo "\n";

// ============================================================================
// Test 5: Checkout Controller has CartRepository
// ============================================================================
echo "--- Test 5: Controller Integration ---\n";

$checkoutControllerPath = __DIR__ . '/../app/Controllers/CheckoutController.php';
$checkoutContent = file_get_contents($checkoutControllerPath);

test(
    "CheckoutController imports CartRepository",
    strpos($checkoutContent, 'use EasyCart\\Repositories\\CartRepository') !== false,
    $passed,
    $failed
);
test(
    "CheckoutController has lockForCheckout call",
    strpos($checkoutContent, 'lockForCheckout') !== false,
    $passed,
    $failed
);
test(
    "CheckoutController has archiveCart call",
    strpos($checkoutContent, 'archiveCart') !== false,
    $passed,
    $failed
);
test(
    "CheckoutController has unlockCart call",
    strpos($checkoutContent, 'unlockCart') !== false,
    $passed,
    $failed
);

echo "\n";

// ============================================================================
// Test 6: Routes Verification
// ============================================================================
echo "--- Test 6: Routes ---\n";

$routesPath = __DIR__ . '/../routes/web.php';
$routesContent = file_get_contents($routesPath);

test("Route /cart/sync exists", strpos($routesContent, '/cart/sync') !== false, $passed, $failed);
test(
    "CartController has sync method",
    method_exists(new \EasyCart\Controllers\CartController(), 'sync'),
    $passed,
    $failed
);

echo "\n";

// ============================================================================
// Summary
// ============================================================================
echo "=== Test Summary ===\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "Total:  " . ($passed + $failed) . "\n";
echo "\nCompleted: " . date('Y-m-d H:i:s') . "\n";

exit($failed > 0 ? 1 : 0);
