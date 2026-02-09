<?php
require_once 'app/Core/Database.php';
require_once 'app/Database/Queries.php';
require_once 'app/Repositories/OrderRepository.php';

// Mock session/auth for this test
define('SITE_NAME', 'EasyCart');

try {
    $repo = new EasyCart\Repositories\OrderRepository();

    // Check for a user with orders
    $db = EasyCart\Core\Database::getInstance()->getConnection();
    $user = $db->query("SELECT id FROM users LIMIT 1")->fetch();

    if (!$user) {
        die("No users found in database to test with.\n");
    }

    $userId = $user['id'];
    echo "Testing with User ID: $userId\n";

    $stats = $repo->getDashboardStats($userId);
    echo "Stats:\n";
    print_r($stats);

    $chart = $repo->getChartData($userId);
    echo "Chart Data (count: " . count($chart) . "):\n";
    print_r(array_slice($chart, 0, 5)); // Show first 5 entries

    echo "\nPhase 9 Backend Check: SUCCESS\n";
} catch (Exception $e) {
    echo "Check Failed: " . $e->getMessage() . "\n";
}
