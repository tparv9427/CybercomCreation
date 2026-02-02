<?php
require_once __DIR__ . '/../vendor/autoload.php';

use EasyCart\Core\Database;

echo "Setting up Database Schema...\n";

try {
    $pdo = Database::getInstance()->getConnection();

    $sql = file_get_contents(__DIR__ . '/../schema.sql');

    $pdo->exec($sql);

    echo "Schema imported successfully.\n";
} catch (Exception $e) {
    die("Error importing schema: " . $e->getMessage() . "\n");
}
