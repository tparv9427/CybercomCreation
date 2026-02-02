<?php
// Script to create the database if it doesn't exist
// Connects to default 'postgres' database to issue CREATE DATABASE command

$config = require __DIR__ . '/../config/database.php';

$dsn = "pgsql:host={$config['host']};port={$config['port']};dbname=postgres;";

try {
    echo "Connecting to default 'postgres' database...\n";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Check if database exists
    $stmt = $pdo->query("SELECT 1 FROM pg_database WHERE datname = '{$config['dbname']}'");
    if ($stmt->fetchColumn()) {
        echo "Database '{$config['dbname']}' already exists.\n";
    } else {
        echo "Creating database '{$config['dbname']}'...\n";
        $pdo->exec("CREATE DATABASE \"{$config['dbname']}\"");
        echo "Database created successfully.\n";
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
