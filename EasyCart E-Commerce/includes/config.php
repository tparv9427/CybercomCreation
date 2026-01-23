<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include server simulator (v4.0-fake-server)
require_once __DIR__ . '/server-simulator.php';

// Site configuration
define('SITE_NAME', 'EasyCart');
define('CURRENCY', '$');

// Version
define('EASYCART_VERSION', '4.0-fake-server');

// Initialize session variables if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

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

// Product name templates
$electronics_names = [
    'Smartphone', 'Tablet', 'Laptop', 'Desktop', 'Monitor', 'Keyboard', 'Mouse', 'Headphones', 
    'Earbuds', 'Speaker', 'Smartwatch', 'Fitness Tracker', 'Camera', 'Webcam', 'Microphone',
    'Router', 'Power Bank', 'Charger', 'USB Cable', 'Hard Drive', 'SSD', 'RAM', 'Graphics Card',
    'Processor', 'Motherboard', 'Gaming Console', 'Controller', 'VR Headset', 'Drone', 'Projector'
];

$fashion_names = [
    'T-Shirt', 'Shirt', 'Jeans', 'Pants', 'Shorts', 'Dress', 'Skirt', 'Jacket', 'Coat',
    'Sweater', 'Hoodie', 'Sneakers', 'Boots', 'Sandals', 'Heels', 'Flats', 'Watch', 'Sunglasses',
    'Hat', 'Cap', 'Scarf', 'Belt', 'Wallet', 'Handbag', 'Backpack', 'Socks', 'Tie', 'Suit',
    'Blazer', 'Cardigan'
];

$home_names = [
    'Sofa', 'Chair', 'Table', 'Bed', 'Mattress', 'Pillow', 'Blanket', 'Curtains', 'Rug',
    'Lamp', 'Mirror', 'Clock', 'Vase', 'Picture Frame', 'Bookshelf', 'Cabinet', 'Drawer',
    'Dining Set', 'Coffee Maker', 'Blender', 'Toaster', 'Microwave', 'Air Purifier', 'Fan',
    'Heater', 'Vacuum Cleaner', 'Iron', 'Washing Machine', 'Refrigerator', 'Cookware Set'
];

$sports_names = [
    'Running Shoes', 'Yoga Mat', 'Dumbbells', 'Resistance Bands', 'Jump Rope', 'Gym Bag',
    'Water Bottle', 'Protein Shaker', 'Fitness Tracker', 'Bicycle', 'Helmet', 'Tennis Racket',
    'Basketball', 'Football', 'Soccer Ball', 'Baseball Bat', 'Golf Clubs', 'Swimming Goggles',
    'Yoga Blocks', 'Foam Roller', 'Exercise Ball', 'Pull-up Bar', 'Ankle Weights', 'Wrist Wraps',
    'Knee Sleeves', 'Compression Shorts', 'Sports Bra', 'Athletic Shirt', 'Track Pants', 'Stopwatch'
];

$books_names = [
    'Fiction Novel', 'Mystery Thriller', 'Romance Book', 'Science Fiction', 'Fantasy Epic',
    'Biography', 'Self-Help Guide', 'Business Book', 'Cookbook', 'Travel Guide', 'History Book',
    'Poetry Collection', 'Art Book', 'Photography Book', 'Programming Guide', 'Marketing Book',
    'Psychology Book', 'Philosophy Book', 'Children\'s Book', 'Comic Book', 'Graphic Novel',
    'Dictionary', 'Encyclopedia', 'Textbook', 'Workbook', 'Journal', 'Planner', 'Notebook',
    'Educational Book', 'Motivational Book'
];

$adjectives = ['Premium', 'Professional', 'Advanced', 'Ultra', 'Pro', 'Elite', 'Deluxe', 'Supreme', 'Master', 'Expert'];
$qualities = ['Quality', 'Performance', 'Edition', 'Series', 'Collection', 'Model', 'Version', 'Grade'];

$icons = [
    1 => ['📱', '💻', '⌚', '🎧', '📷', '🖥️', '⌨️', '🖱️', '🔊', '📡'],
    2 => ['👕', '👔', '👗', '👞', '👟', '👠', '🧢', '🕶️', '👜', '🎒'],
    3 => ['🛋️', '🛏️', '🪑', '🍽️', '☕', '🍴', '🏠', '💡', '🖼️', '🕐'],
    4 => ['⚽', '🏀', '🎾', '🏈', '⛳', '🏋️', '🚴', '🏊', '🧘', '🥊'],
    5 => ['📚', '📖', '📕', '📗', '📘', '📙', '📓', '📔', '📒', '📰']
];

// Generate 100 products per category
$products = [];
$product_id = 1;

foreach ($categories as $cat_id => $category) {
    $name_pool = [];
    switch ($cat_id) {
        case 1: $name_pool = $electronics_names; break;
        case 2: $name_pool = $fashion_names; break;
        case 3: $name_pool = $home_names; break;
        case 4: $name_pool = $sports_names; break;
        case 5: $name_pool = $books_names; break;
    }
    
    for ($i = 0; $i < 100; $i++) {
        $base_name = $name_pool[array_rand($name_pool)];
        $adj = $adjectives[array_rand($adjectives)];
        $qual = $qualities[array_rand($qualities)];
        $name = "$adj $base_name $qual";
        
        $base_price = rand(20, 500);
        $discount = rand(0, 40);
        $price = $base_price * (1 - $discount / 100);
        
        $products[$product_id] = [
            'id' => $product_id,
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)) . '-' . $product_id,
            'category_id' => $cat_id,
            'brand_id' => rand(1, 12),
            'price' => round($price, 2),
            'original_price' => (float)$base_price,
            'discount_percent' => $discount,
            'rating' => round(rand(30, 50) / 10, 1),
            'reviews_count' => rand(10, 500),
            'stock' => rand(0, 100),
            'description' => "Premium quality $base_name with excellent features and performance. Perfect for your needs.",
            'long_description' => "Experience the best in class $base_name with advanced technology and superior craftsmanship. This product combines style, functionality, and durability to deliver exceptional value.",
            'features' => [
                'High quality materials',
                'Advanced technology',
                'Durable construction',
                'Excellent performance',
                'Great value for money'
            ],
            'specifications' => [
                'Model' => strtoupper(substr(md5($name), 0, 8)),
                'Brand' => $brands[rand(1, 12)]['name'],
                'Warranty' => rand(1, 3) . ' Year(s)',
                'Made In' => ['USA', 'Germany', 'Japan', 'China', 'India'][array_rand(['USA', 'Germany', 'Japan', 'China', 'India'])]
            ],
            'variants' => [
                'color' => ['Black', 'White', 'Blue', 'Red', 'Gray'],
                'size' => ['S', 'M', 'L', 'XL']
            ],
            'icon' => $icons[$cat_id][array_rand($icons[$cat_id])],
            'new' => rand(0, 10) > 7,
            'featured' => rand(0, 10) > 8
        ];
        
        $product_id++;
    }
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
    return isset($_SESSION['cart'][$productId]);
}

function isInWishlist($productId) {
    return in_array($productId, $_SESSION['wishlist']);
}

function getCartCount() {
    return array_sum($_SESSION['cart']);
}

function getWishlistCount() {
    return count($_SESSION['wishlist']);
}

function getCartTotal() {
    global $products;
    $total = 0;
    foreach ($_SESSION['cart'] as $productId => $quantity) {
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
