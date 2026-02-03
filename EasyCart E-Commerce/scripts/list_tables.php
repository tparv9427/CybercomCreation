<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EasyCart\Core\Database;

$pdo = Database::getInstance()->getConnection();

$stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Tables found in database:\n";
foreach ($tables as $table) {
    echo "- " . $table . "\n";
}
