<?php
/**
 * List all tables in the database
 */

require_once __DIR__ . '/../app/Core/Database.php';

use EasyCart\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();

    echo "=================================================\n";
    echo "Database Tables\n";
    echo "=================================================\n\n";

    $stmt = $pdo->query("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        ORDER BY table_name
    ");

    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Total tables: " . count($tables) . "\n\n";

    // Categorize tables
    $oldTables = [];
    $newTables = [];
    $otherTables = [];

    foreach ($tables as $table) {
        if (strpos($table, 'catalog_') === 0 || strpos($table, 'sales_') === 0) {
            $newTables[] = $table;
        } elseif (in_array($table, ['products', 'categories', 'brands', 'carts', 'cart_items', 'orders', 'order_items'])) {
            $oldTables[] = $table;
        } else {
            $otherTables[] = $table;
        }
    }

    if (!empty($newTables)) {
        echo "NEW TABLES (Migrated Schema):\n";
        echo "-----------------------------\n";
        foreach ($newTables as $table) {
            echo "  ✓ $table\n";
        }
        echo "\n";
    }

    if (!empty($oldTables)) {
        echo "OLD TABLES (Original Schema):\n";
        echo "------------------------------\n";
        foreach ($oldTables as $table) {
            echo "  • $table\n";
        }
        echo "\n";
    }

    if (!empty($otherTables)) {
        echo "OTHER TABLES:\n";
        echo "-------------\n";
        foreach ($otherTables as $table) {
            echo "  • $table\n";
        }
        echo "\n";
    }

    echo "=================================================\n";
    echo "Complete Table List:\n";
    echo "=================================================\n";
    foreach ($tables as $i => $table) {
        echo ($i + 1) . ". $table\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
