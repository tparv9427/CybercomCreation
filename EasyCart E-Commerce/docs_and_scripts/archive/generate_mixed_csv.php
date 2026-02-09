<?php

$host = '127.0.0.1';
$db = 'easycart';
$user = 'postgres';
$pass = 'root';
$port = "5432";

$dsn = "pgsql:host=$host;port=$port;dbname=$db;";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

$filename = 'mixed_products_150.csv';
$fp = fopen($filename, 'w');

// Headers
$headers = ['sku', 'name', 'price', 'stock', 'description', 'is_active', 'brand', 'color', 'image_url'];
fputcsv($fp, $headers);

// 1. Fetch 50 Existing Products from DB
echo "Fetching 50 existing products...\n";
$stmt = $pdo->query("
    SELECT e.entity_id, e.sku, e.name, e.price, e.stock, e.description, e.is_active 
    FROM catalog_product_entity e 
    LIMIT 50
");
$existingProducts = $stmt->fetchAll();

foreach ($existingProducts as $product) {
    // Fetch attributes for this product
    $attrStmt = $pdo->prepare("
        SELECT attribute_code, attribute_value 
        FROM catalog_product_attribute 
        WHERE product_entity_id = :id
    ");
    $attrStmt->execute([':id' => $product['entity_id']]);
    $attributes = $attrStmt->fetchAll(PDO::FETCH_KEY_PAIR); // ['code' => 'value']

    $row = [
        $product['sku'],
        $product['name'],
        $product['price'],
        $product['stock'],
        $product['description'],
        $product['is_active'] ? 1 : 0,
        $attributes['brand'] ?? 'Generic',
        $attributes['color'] ?? 'Black',
        $attributes['image_url'] ?? 'placeholder.jpg'
    ];
    fputcsv($fp, $row);
}
echo "Added " . count($existingProducts) . " existing products.\n";

// 2. Generate 100 New Products
echo "Generating 100 new products...\n";
$brands = ['NovaTech', 'CyberLine', 'FutureWear', 'QuantumGear', 'StellarGoods'];
$colors = ['Red', 'Blue', 'Green', 'Black', 'White', 'Silver', 'Gold', 'Purple', 'Orange'];

for ($i = 1; $i <= 100; $i++) {
    $brand = $brands[array_rand($brands)];
    $color = $colors[array_rand($colors)];

    $sku = 'MIX-' . strtoupper(substr($brand, 0, 3)) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
    $name = "$brand $color Series $i";
    $price = rand(1000, 30000) / 100;

    $row = [
        $sku,
        $name,
        $price,
        rand(10, 100),
        "A brand new product from $brand. Experience the future of shopping.",
        1,
        $brand,
        $color,
        'placeholder.jpg'
    ];
    fputcsv($fp, $row);
}
echo "Added 100 new products.\n";

fclose($fp);
echo "Successfully created $filename with 150 rows.\n";
