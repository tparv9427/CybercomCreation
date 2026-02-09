<?php
/**
 * Cart Cleanup Cron Job
 * 
 * This script should be run periodically (daily recommended) to:
 * 1. Expire abandoned guest carts (10+ days old)
 * 2. Hard delete soft-deleted carts (30+ days old)
 * 3. Unlock expired checkout locks (10+ minutes old)
 * 
 * Usage:
 *   php scripts/cart_cleanup.php
 * 
 * Cron example (run daily at 3 AM):
 *   0 3 * * * /usr/bin/php /path/to/scripts/cart_cleanup.php >> /var/log/cart_cleanup.log 2>&1
 * 
 * Windows Task Scheduler:
 *   php D:\Cybercom Creation\EasyCart E-Commerce\scripts\cart_cleanup.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use EasyCart\Core\Database;

// Configuration
$config = [
    'guest_cart_expiry_days' => 10,      // Expire guest carts after 10 days
    'soft_delete_retention_days' => 30,   // Hard delete after 30 days
    'checkout_timeout_minutes' => 10,     // Unlock checkout after 10 minutes
    'dry_run' => false,                   // Set to true to preview without making changes
];

// Allow dry-run from command line
if (in_array('--dry-run', $argv ?? [])) {
    $config['dry_run'] = true;
}

echo "=== Cart Cleanup Job ===\n";
echo "Started: " . date('Y-m-d H:i:s') . "\n";
echo "Mode: " . ($config['dry_run'] ? "DRY RUN (no changes)" : "LIVE") . "\n\n";

try {
    $pdo = Database::getInstance()->getConnection();

    // ============================================================================
    // 1. Unlock Expired Checkout Locks
    // ============================================================================
    echo "1. Unlocking expired checkout locks...\n";

    $sql = "
        UPDATE sales_cart 
        SET is_checkout = FALSE, 
            is_active = TRUE,
            checkout_locked_at = NULL
        WHERE is_checkout = TRUE 
          AND checkout_locked_at < NOW() - INTERVAL ':minutes minutes'
          AND deleted_at IS NULL
    ";

    if (!$config['dry_run']) {
        $stmt = $pdo->prepare(str_replace(':minutes', $config['checkout_timeout_minutes'], $sql));
        $stmt->execute();
        $unlockedCount = $stmt->rowCount();
    } else {
        // Count only
        $countSql = "
            SELECT COUNT(*) FROM sales_cart 
            WHERE is_checkout = TRUE 
              AND checkout_locked_at < NOW() - INTERVAL '{$config['checkout_timeout_minutes']} minutes'
              AND deleted_at IS NULL
        ";
        $unlockedCount = $pdo->query($countSql)->fetchColumn();
    }
    echo "   Unlocked: $unlockedCount carts\n\n";

    // ============================================================================
    // 2. Expire Abandoned Guest Carts (soft delete)
    // ============================================================================
    echo "2. Expiring abandoned guest carts ({$config['guest_cart_expiry_days']}+ days)...\n";

    $sql = "
        UPDATE sales_cart 
        SET deleted_at = NOW(),
            is_active = FALSE
        WHERE is_temp = TRUE 
          AND user_id IS NULL
          AND updated_at < NOW() - INTERVAL ':days days'
          AND deleted_at IS NULL
          AND archived = FALSE
    ";

    if (!$config['dry_run']) {
        $stmt = $pdo->prepare(str_replace(':days', $config['guest_cart_expiry_days'], $sql));
        $stmt->execute();
        $expiredCount = $stmt->rowCount();
    } else {
        $countSql = "
            SELECT COUNT(*) FROM sales_cart 
            WHERE is_temp = TRUE 
              AND user_id IS NULL
              AND updated_at < NOW() - INTERVAL '{$config['guest_cart_expiry_days']} days'
              AND deleted_at IS NULL
              AND archived = FALSE
        ";
        $expiredCount = $pdo->query($countSql)->fetchColumn();
    }
    echo "   Expired: $expiredCount guest carts\n\n";

    // ============================================================================
    // 3. Hard Delete Old Soft-Deleted Carts
    // ============================================================================
    echo "3. Hard deleting old soft-deleted carts ({$config['soft_delete_retention_days']}+ days)...\n";

    // First, delete cart products
    $sqlProducts = "
        DELETE FROM sales_cart_product 
        WHERE cart_id IN (
            SELECT cart_id FROM sales_cart 
            WHERE deleted_at IS NOT NULL 
              AND deleted_at < NOW() - INTERVAL ':days days'
        )
    ";

    // Then, delete carts
    $sqlCarts = "
        DELETE FROM sales_cart 
        WHERE deleted_at IS NOT NULL 
          AND deleted_at < NOW() - INTERVAL ':days days'
    ";

    if (!$config['dry_run']) {
        // Delete products first (FK constraint)
        $stmt = $pdo->prepare(str_replace(':days', $config['soft_delete_retention_days'], $sqlProducts));
        $stmt->execute();
        $deletedProducts = $stmt->rowCount();

        // Then delete carts
        $stmt = $pdo->prepare(str_replace(':days', $config['soft_delete_retention_days'], $sqlCarts));
        $stmt->execute();
        $deletedCarts = $stmt->rowCount();
    } else {
        $countSql = "
            SELECT COUNT(*) FROM sales_cart 
            WHERE deleted_at IS NOT NULL 
              AND deleted_at < NOW() - INTERVAL '{$config['soft_delete_retention_days']} days'
        ";
        $deletedCarts = $pdo->query($countSql)->fetchColumn();
        $deletedProducts = 0; // Not calculated in dry run
    }
    echo "   Deleted: $deletedCarts carts, $deletedProducts cart items\n\n";

    // ============================================================================
    // 4. Clean up archived carts older than 90 days (optional - can be adjusted)
    // ============================================================================
    echo "4. Hard deleting old archived carts (90+ days)...\n";

    $archivedRetentionDays = 90;

    // Delete archived cart products
    $sqlArchivedProducts = "
        DELETE FROM sales_cart_product 
        WHERE cart_id IN (
            SELECT cart_id FROM sales_cart 
            WHERE archived = TRUE 
              AND updated_at < NOW() - INTERVAL '$archivedRetentionDays days'
        )
    ";

    // Delete archived carts
    $sqlArchivedCarts = "
        DELETE FROM sales_cart 
        WHERE archived = TRUE 
          AND updated_at < NOW() - INTERVAL '$archivedRetentionDays days'
    ";

    if (!$config['dry_run']) {
        $stmt = $pdo->query($sqlArchivedProducts);
        $archivedProductsDeleted = $stmt->rowCount();

        $stmt = $pdo->query($sqlArchivedCarts);
        $archivedCartsDeleted = $stmt->rowCount();
    } else {
        $countSql = "
            SELECT COUNT(*) FROM sales_cart 
            WHERE archived = TRUE 
              AND updated_at < NOW() - INTERVAL '$archivedRetentionDays days'
        ";
        $archivedCartsDeleted = $pdo->query($countSql)->fetchColumn();
        $archivedProductsDeleted = 0;
    }
    echo "   Deleted: $archivedCartsDeleted archived carts, $archivedProductsDeleted items\n\n";

    // ============================================================================
    // Summary
    // ============================================================================
    echo "=== Summary ===\n";
    echo "Checkout locks unlocked: $unlockedCount\n";
    echo "Guest carts expired: $expiredCount\n";
    echo "Soft-deleted carts removed: $deletedCarts\n";
    echo "Archived carts removed: $archivedCartsDeleted\n";
    echo "\nCompleted: " . date('Y-m-d H:i:s') . "\n";

    if ($config['dry_run']) {
        echo "\n*** DRY RUN - No changes were made ***\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

exit(0);
