<?php
require_once __DIR__ . '/../app/Services/WishlistService.php';
require_once __DIR__ . '/../app/Services/AuthService.php';
require_once __DIR__ . '/../app/Repositories/WishlistRepository.php';
require_once __DIR__ . '/../app/Core/Database.php';

// Mock session
session_start();
$_SESSION['user_id'] = 19;

use EasyCart\Services\WishlistService;

echo "Instantiating WishlistService...\n";
$service = new WishlistService();

echo "Calling getCount()...\n";
$count = $service->getCount();
echo "Count: $count\n";

echo "Calling get()...\n";
$items = $service->get();
echo "Items: " . count($items) . "\n";

echo "WishlistService Verify Complete.\n";
