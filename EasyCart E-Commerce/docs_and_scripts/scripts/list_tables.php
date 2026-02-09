<?php
require_once 'app/Core/Database.php';

try {
    $db = EasyCart\Core\Database::getInstance()->getConnection();
    $tables = $db->query("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public'")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in Public Schema:\n";
    print_r($tables);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
