<?php
require_once 'app/Core/Database.php';

try {
    $db = EasyCart\Core\Database::getInstance()->getConnection();
    $cols = $db->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'coupons'")->fetchAll(PDO::FETCH_ASSOC);
    echo "Coupons Table Structure:\n";
    print_r($cols);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
