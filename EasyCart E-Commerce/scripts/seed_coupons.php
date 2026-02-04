<?php
require_once 'app/Core/Database.php';

try {
    $db = EasyCart\Core\Database::getInstance()->getConnection();
    $stmt = $db->prepare("INSERT INTO coupons (code, discount_percent) VALUES (:code, :percent)");
    $stmt->execute([':code' => 'GET5', ':percent' => 5]);
    $stmt->execute([':code' => 'GET10', ':percent' => 10]);
    $stmt->execute([':code' => 'GET15', ':percent' => 15]);
    $stmt->execute([':code' => 'GET25', ':percent' => 25]);
    echo "Seed coupons successfully added.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
