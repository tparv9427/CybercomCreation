<?php
// Icon sets from original config
$icons = [
    1 => ['📱', '💻', '⌚', '🎧', '📷', '🖥️', '⌨️', '🖱️', '🔊', '📡'],
    2 => ['👕', '👔', '👗', '👞', '👟', '👠', '🧢', '🕶️', '👜', '🎒'],
    3 => ['🛋️', '🛏️', '🪑', '🍽️', '☕', '🍴', '🏠', '💡', '🖼️', '🕐'],
    4 => ['⚽', '🏀', '🎾', '🏈', '⛳', '🏋️', '🚴', '🏊', '🧘', '🥊'],
    5 => ['📚', '📖', '📕', '📗', '📘', '📙', '📓', '📔', '📒', '📰']
];

$file = __DIR__ . '/products.json';
if (!file_exists($file)) {
    die("products.json not found");
}

$json = file_get_contents($file);
$products = json_decode($json, true);

foreach ($products as &$product) {
    $cat_id = $product['category_id'];
    if (isset($icons[$cat_id])) {
        // Assign random icon from the category set
        $product['icon'] = $icons[$cat_id][array_rand($icons[$cat_id])];
    }
}

file_put_contents($file, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Updated icons for " . count($products) . " products.\n";
?>
