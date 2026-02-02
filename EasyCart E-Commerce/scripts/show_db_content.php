<?php
require_once __DIR__ . '/../app/Core/Database.php';

use EasyCart\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();
} catch (\Exception $e) {
    die("Could not connect to database: " . $e->getMessage() . "\n");
}

echo "=== EasyCart Database Content ===\n\n";

// Get list of tables
$tablesStmt = $pdo->query("
    SELECT table_name 
    FROM information_schema.tables 
    WHERE table_schema = 'public' 
    ORDER BY table_name
");
$tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    echo "Table: $table\n";
    echo str_repeat("-", strlen("Table: $table")) . "\n";

    // Get row count
    $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
    echo "Total Rows: $count\n";

    // Get first 5 rows
    $rows = $pdo->query("SELECT * FROM $table LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        echo "(Empty)\n";
    } else {
        // Get headers
        $headers = array_keys($rows[0]);
        echo implode(" | ", $headers) . "\n";

        foreach ($rows as $row) {
            // Truncate long fields for display
            $displayRow = array_map(function ($val) {
                if (is_string($val) && strlen($val) > 20) {
                    return substr($val, 0, 17) . '...';
                }
                return $val;
            }, $row);
            echo implode(" | ", $displayRow) . "\n";
        }
        if ($count > 5) {
            echo "... and " . ($count - 5) . " more rows.\n";
        }
    }
    echo "\n";
}
