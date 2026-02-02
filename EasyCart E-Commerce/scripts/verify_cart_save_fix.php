<?php
require_once __DIR__ . '/../app/Services/CartService.php';
require_once __DIR__ . '/../app/Repositories/CartRepository.php';
require_once __DIR__ . '/../app/Repositories/ProductRepository.php';
require_once __DIR__ . '/../app/Repositories/SaveForLaterRepository.php';
require_once __DIR__ . '/../app/Services/AuthService.php';
require_once __DIR__ . '/../app/Core/Database.php';

session_start();
$_SESSION['user_id'] = 19;

use EasyCart\Services\CartService;

echo "Instantiating CartService...\n";
$service = new CartService();

echo "Calling getSavedItems()...\n";
$items = $service->getSavedItems();
echo "Saved Items Count: " . count($items) . "\n";

// Test adding an item to save for later
echo "Saving item 101 for later...\n";
// Note: This requires item 101 to exist in DB. 
// If it fails, it returns false.
$result = $service->saveForLater(101);
echo "Save result: " . ($result ? 'Success' : 'Failed (Product might not exist)') . "\n";

echo "CartService SaveForLater Verify Complete.\n";
