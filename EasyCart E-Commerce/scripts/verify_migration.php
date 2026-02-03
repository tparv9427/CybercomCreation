<?php
/**
 * Migration Verification Script
 * Validates data integrity after migration
 */

require_once __DIR__ . '/../app/Core/Database.php';

use EasyCart\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();

    echo "=================================================\n";
    echo "Migration Verification\n";
    echo "=================================================\n\n";

    $errors = [];
    $warnings = [];

    // Check 1: Product counts
    echo "Check 1: Product data integrity\n";
    echo "--------------------------------\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $oldProducts = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM catalog_product_entity");
    $newProducts = $stmt->fetchColumn();

    echo "Old products: $oldProducts\n";
    echo "New products: $newProducts\n";

    if ($oldProducts != $newProducts) {
        $errors[] = "Product count mismatch!";
        echo "✗ FAIL: Product counts don't match\n";
    } else {
        echo "✓ PASS: Product counts match\n";
    }

    // Check orphaned product attributes
    $stmt = $pdo->query("
        SELECT COUNT(*) FROM catalog_product_attribute 
        WHERE product_entity_id NOT IN (SELECT entity_id FROM catalog_product_entity)
    ");
    $orphanedAttrs = $stmt->fetchColumn();

    if ($orphanedAttrs > 0) {
        $warnings[] = "$orphanedAttrs orphaned product attributes found";
        echo "⚠ WARNING: $orphanedAttrs orphaned product attributes\n";
    } else {
        echo "✓ PASS: No orphaned product attributes\n";
    }

    echo "\n";

    // Check 2: Category counts
    echo "Check 2: Category data integrity\n";
    echo "--------------------------------\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    $oldCategories = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM catalog_category_entity");
    $newCategories = $stmt->fetchColumn();

    echo "Old categories: $oldCategories\n";
    echo "New categories: $newCategories\n";

    if ($oldCategories != $newCategories) {
        $errors[] = "Category count mismatch!";
        echo "✗ FAIL: Category counts don't match\n";
    } else {
        echo "✓ PASS: Category counts match\n";
    }

    echo "\n";

    // Check 3: Cart counts
    echo "Check 3: Cart data integrity\n";
    echo "----------------------------\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM carts");
    $oldCarts = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM sales_cart");
    $newCarts = $stmt->fetchColumn();

    echo "Old carts: $oldCarts\n";
    echo "New carts: $newCarts\n";

    if ($oldCarts != $newCarts) {
        $errors[] = "Cart count mismatch!";
        echo "✗ FAIL: Cart counts don't match\n";
    } else {
        echo "✓ PASS: Cart counts match\n";
    }

    $stmt = $pdo->query("SELECT COUNT(*) FROM cart_items");
    $oldCartItems = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM sales_cart_product");
    $newCartItems = $stmt->fetchColumn();

    echo "Old cart items: $oldCartItems\n";
    echo "New cart items: $newCartItems\n";

    if ($oldCartItems != $newCartItems) {
        $errors[] = "Cart item count mismatch!";
        echo "✗ FAIL: Cart item counts don't match\n";
    } else {
        echo "✓ PASS: Cart item counts match\n";
    }

    echo "\n";

    // Check 4: Order counts
    echo "Check 4: Order data integrity\n";
    echo "-----------------------------\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
    $oldOrders = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM sales_order");
    $newOrders = $stmt->fetchColumn();

    echo "Old orders: $oldOrders\n";
    echo "New orders: $newOrders\n";

    if ($oldOrders != $newOrders) {
        $errors[] = "Order count mismatch!";
        echo "✗ FAIL: Order counts don't match\n";
    } else {
        echo "✓ PASS: Order counts match\n";
    }

    $stmt = $pdo->query("SELECT COUNT(*) FROM order_items");
    $oldOrderItems = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM sales_order_product");
    $newOrderItems = $stmt->fetchColumn();

    echo "Old order items: $oldOrderItems\n";
    echo "New order items: $newOrderItems\n";

    if ($oldOrderItems != $newOrderItems) {
        $errors[] = "Order item count mismatch!";
        echo "✗ FAIL: Order item counts don't match\n";
    } else {
        echo "✓ PASS: Order item counts match\n";
    }

    echo "\n";

    // Check 5: Foreign key integrity
    echo "Check 5: Foreign key integrity\n";
    echo "------------------------------\n";

    // Check cart products reference valid products
    $stmt = $pdo->query("
        SELECT COUNT(*) FROM sales_cart_product 
        WHERE product_entity_id NOT IN (SELECT entity_id FROM catalog_product_entity)
    ");
    $invalidCartProducts = $stmt->fetchColumn();

    if ($invalidCartProducts > 0) {
        $errors[] = "$invalidCartProducts cart items reference invalid products";
        echo "✗ FAIL: $invalidCartProducts cart items reference invalid products\n";
    } else {
        echo "✓ PASS: All cart items reference valid products\n";
    }

    // Check order products
    $stmt = $pdo->query("
        SELECT COUNT(*) FROM sales_order_product 
        WHERE product_entity_id IS NOT NULL 
        AND product_entity_id NOT IN (SELECT entity_id FROM catalog_product_entity)
    ");
    $invalidOrderProducts = $stmt->fetchColumn();

    if ($invalidOrderProducts > 0) {
        $warnings[] = "$invalidOrderProducts order items reference invalid products (may be deleted products)";
        echo "⚠ WARNING: $invalidOrderProducts order items reference invalid products\n";
    } else {
        echo "✓ PASS: All order items reference valid products\n";
    }

    echo "\n";

    // Summary
    echo "=================================================\n";
    echo "Verification Summary\n";
    echo "=================================================\n\n";

    if (empty($errors)) {
        echo "✓ All critical checks passed!\n";
    } else {
        echo "✗ ERRORS FOUND:\n";
        foreach ($errors as $error) {
            echo "  - $error\n";
        }
    }

    if (!empty($warnings)) {
        echo "\n⚠ WARNINGS:\n";
        foreach ($warnings as $warning) {
            echo "  - $warning\n";
        }
    }

    echo "\n";

    // Statistics
    echo "Migration Statistics:\n";
    echo "--------------------\n";
    echo "Products migrated: $newProducts\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM catalog_product_attribute");
    $attrs = $stmt->fetchColumn();
    echo "Product attributes: $attrs\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM catalog_product_image");
    $images = $stmt->fetchColumn();
    echo "Product images: $images\n";

    echo "Categories migrated: $newCategories\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM catalog_category_product");
    $relations = $stmt->fetchColumn();
    echo "Category-product relations: $relations\n";

    echo "Carts migrated: $newCarts\n";
    echo "Cart items migrated: $newCartItems\n";
    echo "Orders migrated: $newOrders\n";
    echo "Order items migrated: $newOrderItems\n";

    echo "\n=================================================\n";

    if (empty($errors)) {
        echo "✓ Migration verification completed successfully!\n";
        echo "=================================================\n";
        exit(0);
    } else {
        echo "✗ Migration verification failed!\n";
        echo "=================================================\n";
        exit(1);
    }

} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
