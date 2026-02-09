<?php
/**
 * Master Migration Script
 * Runs all migration scripts in the correct order
 */

echo "=================================================\n";
echo "EasyCart Database Migration - Master Script\n";
echo "=================================================\n\n";

echo "This script will:\n";
echo "1. Create new database schema\n";
echo "2. Migrate products data\n";
echo "3. Migrate categories data\n";
echo "4. Migrate carts data\n";
echo "5. Migrate orders data\n";
echo "6. Verify migration integrity\n\n";

echo "⚠ WARNING: This will create new tables in your database.\n";
echo "⚠ Make sure you have a backup before proceeding!\n\n";

// Ask for confirmation
echo "Do you want to proceed? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) !== 'yes') {
    echo "\nMigration cancelled.\n";
    exit(0);
}

echo "\n";

$scripts = [
    'migrate_schema.php' => 'Creating new schema',
    'migrate_categories.php' => 'Migrating categories',
    'migrate_products.php' => 'Migrating products',
    'migrate_carts.php' => 'Migrating carts',
    'migrate_orders.php' => 'Migrating orders',
    'verify_migration.php' => 'Verifying migration'
];

$failed = false;

foreach ($scripts as $script => $description) {
    echo "=================================================\n";
    echo "Running: $description\n";
    echo "=================================================\n\n";

    $scriptPath = __DIR__ . '/' . $script;

    if (!file_exists($scriptPath)) {
        echo "✗ Error: Script not found: $script\n";
        $failed = true;
        break;
    }

    // Execute the script
    $output = [];
    $returnCode = 0;
    exec("php \"$scriptPath\"", $output, $returnCode);

    // Display output
    foreach ($output as $line) {
        echo $line . "\n";
    }

    if ($returnCode !== 0) {
        echo "\n✗ Migration step failed: $description\n";
        $failed = true;
        break;
    }

    echo "\n";
}

echo "=================================================\n";

if ($failed) {
    echo "✗ Migration failed!\n";
    echo "=================================================\n";
    echo "\nPlease review the errors above and fix them before retrying.\n";
    exit(1);
} else {
    echo "✓ All migration steps completed successfully!\n";
    echo "=================================================\n";
    echo "\nNext steps:\n";
    echo "1. Update Repository classes to use new table names\n";
    echo "2. Update Service layer for new schema\n";
    echo "3. Test the application thoroughly\n";
    echo "4. After verification, you can drop old tables\n";
    exit(0);
}
