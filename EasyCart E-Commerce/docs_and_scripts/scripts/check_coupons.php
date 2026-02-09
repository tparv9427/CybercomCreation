<?php
require_once 'app/Core/Database.php';

try {
    $db = EasyCart\Core\Database::getInstance()->getConnection();
    $coupons = $db->query("SELECT * FROM coupons")->fetchAll(PDO::FETCH_ASSOC);
    echo "Current Coupons in DB:\n";
    print_r($coupons);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
