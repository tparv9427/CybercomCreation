<?php
// Start session


// Include server simulator (v4.0-fake-server)
require_once __DIR__ . '/server-simulator.php';

// Site configuration
define('SITE_NAME', 'EasyCart');
define('CURRENCY', '$');

// Version
define('EASYCART_VERSION', '4.0-fake-server');

// Include session manager
require_once __DIR__ . '/session-manager.php';

// Initialize session
initSession();


if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = null;
}

// Sample Categories Data
$categories = [
    1 => ['id' => 1, 'name' => 'Electronics', 'slug' => 'electronics'],
    2 => ['id' => 2, 'name' => 'Fashion', 'slug' => 'fashion'],
    3 => ['id' => 3, 'name' => 'Home & Living', 'slug' => 'home-living'],
    4 => ['id' => 4, 'name' => 'Sports', 'slug' => 'sports'],
    5 => ['id' => 5, 'name' => 'Books', 'slug' => 'books']
];

// Sample Brands Data
$brands = [
    1 => ['id' => 1, 'name' => 'TechPro'],
    2 => ['id' => 2, 'name' => 'StyleMax'],
    3 => ['id' => 3, 'name' => 'HomeComfort'],
    4 => ['id' => 4, 'name' => 'SportFit'],
    5 => ['id' => 5, 'name' => 'ReadMore'],
    6 => ['id' => 6, 'name' => 'ElectroPlus'],
    7 => ['id' => 7, 'name' => 'FashionHub'],
    8 => ['id' => 8, 'name' => 'GadgetWorld'],
    9 => ['id' => 9, 'name' => 'UrbanStyle'],
    10 => ['id' => 10, 'name' => 'CozyHome'],
    11 => ['id' => 11, 'name' => 'ActiveLife'],
    12 => ['id' => 12, 'name' => 'BookNest']
];

// Load products from JSON file
$products_file = __DIR__ . '/../data/products.json';
$products = [];

if (file_exists($products_file)) {
    $json = file_get_contents($products_file);
    $products = json_decode($json, true);
} else {
    // Fallback: empty products array if file doesn't exist
    error_log("Products file not found: $products_file");
    $products = [];
}

// Load users from file or initialize with defaults
function loadUsers() {
    $users_file = __DIR__ . '/../data/users.json';
    
    if (file_exists($users_file)) {
        $json = file_get_contents($users_file);
        return json_decode($json, true);
    }
    
    // Default 5 users
    return [
        1 => [
            'id' => 1,
            'email' => 'demo@easycart.com',
            'password' => password_hash('demo123', PASSWORD_DEFAULT),
            'name' => 'Demo User',
            'created_at' => '2026-01-01'
        ],
        2 => [
            'id' => 2,
            'email' => 'john.doe@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'name' => 'John Doe',
            'created_at' => '2026-01-05'
        ],
        3 => [
            'id' => 3,
            'email' => 'jane.smith@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'name' => 'Jane Smith',
            'created_at' => '2026-01-10'
        ],
        4 => [
            'id' => 4,
            'email' => 'mike.wilson@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'name' => 'Mike Wilson',
            'created_at' => '2026-01-12'
        ],
        5 => [
            'id' => 5,
            'email' => 'sarah.jones@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'name' => 'Sarah Jones',
            'created_at' => '2026-01-15'
        ]
    ];
}

// Save users to file
function saveUsers($users) {
    $data_dir = __DIR__ . '/../data';
    if (!is_dir($data_dir)) {
        mkdir($data_dir, 0777, true);
    }
    
    $users_file = $data_dir . '/users.json';
    file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
}

// Get next user ID
function getNextUserId($users) {
    if (empty($users)) return 1;
    return max(array_keys($users)) + 1;
}

// Add new user
function addUser($email, $password, $name) {
    $users = loadUsers();
    
    // Check if email exists
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return false;
        }
    }
    
    $user_id = getNextUserId($users);
    $users[$user_id] = [
        'id' => $user_id,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'name' => $name,
        'created_at' => date('Y-m-d')
    ];
    
    saveUsers($users);
    return $user_id;
}

$users = loadUsers();
$orders = [];

// Helper Functions
function getProduct($productId) {
    global $products;
    return isset($products[$productId]) ? $products[$productId] : null;
}

function getCategory($categoryId) {
    global $categories;
    return isset($categories[$categoryId]) ? $categories[$categoryId] : null;
}

function getBrand($brandId) {
    global $brands;
    return isset($brands[$brandId]) ? $brands[$brandId] : null;
}

function getProductsByCategory($categoryId) {
    global $products;
    return array_filter($products, function($product) use ($categoryId) {
        return $product['category_id'] == $categoryId;
    });
}

function getProductsByBrand($brandId) {
    global $products;
    return array_filter($products, function($product) use ($brandId) {
        return $product['brand_id'] == $brandId;
    });
}

function getFeaturedProducts() {
    global $products;
    $featured = array_filter($products, function($product) {
        return $product['featured'] === true;
    });
    return array_slice($featured, 0, 6);
}

function getNewProducts() {
    global $products;
    $new = array_filter($products, function($product) {
        return $product['new'] === true;
    });
    return array_slice($new, 0, 6);
}

function formatPrice($price) {
    return CURRENCY . number_format($price, 2);
}

function isInCart($productId) {
    $cart = getCart();
    return isset($cart[$productId]);
}

function isInWishlist($productId) {
    $wishlist = getWishlist();
    // Wishlist is a simple array of IDs? No, looking at ajax_wishlist.php it seems to be keys or values?
    // ajax_wishlist.php: $_SESSION['wishlist'][] = $product_id; (indexed array of IDs)
    return in_array($productId, $wishlist);
}

function getCartCount() {
    return array_sum(getCart());
}

function getWishlistCount() {
    return count(getWishlist());
}

function getCartTotal() {
    global $products;
    $total = 0;
    $cart = getCart();
    foreach ($cart as $productId => $quantity) {
        if (isset($products[$productId])) {
            $total += $products[$productId]['price'] * $quantity;
        }
    }
    return $total;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;
}

function getUserById($userId) {
    global $users;
    return isset($users[$userId]) ? $users[$userId] : null;
}
?>
