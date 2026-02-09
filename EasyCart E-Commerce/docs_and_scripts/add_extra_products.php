<?php

$host = '127.0.0.1';
$db = 'easycart';
$user = 'postgres';
$pass = 'root';
$port = "5432";
$charset = 'utf8mb4';

$dsn = "pgsql:host=$host;port=$port;dbname=$db;";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected to database successfully.\n";
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

$colors = ['Red', 'Blue', 'Green', 'Black', 'White', 'Silver', 'Gold'];
$brands = ['ExtraBrand'];

// Insert 15 products
for ($i = 1; $i <= 15; $i++) {
    $sku = 'EXTRA-' . str_pad($i, 3, '0', STR_PAD_LEFT);
    $name = "Extra Premium Product " . $i;
    $price = rand(5000, 15000) / 100;
    $stock = rand(10, 50);
    $description = "This is an extra dummy product added for testing pagination.";

    // Insert Entity
    $stmt = $pdo->prepare("
        INSERT INTO catalog_product_entity (sku, name, price, stock, description, is_active, is_featured, is_new, created_at, updated_at)
        VALUES (:sku, :name, :price, :stock, :description, 1, FALSE, TRUE, NOW(), NOW())
        ON CONFLICT ON CONSTRAINT catalog_product_entity_sku_unique DO UPDATE SET updated_at = NOW()
        RETURNING entity_id
    ");

    $stmt->execute([
        ':sku' => $sku,
        ':name' => $name,
        ':price' => $price,
        ':stock' => $stock,
        ':description' => $description
    ]);

    $result = $stmt->fetch();
    $entity_id = $result['entity_id'];

    if ($entity_id) {
        // Insert Attributes
        $attributes = [
            'brand' => 'ExtraBrand',
            'color' => $colors[array_rand($colors)],
            'image_url' => 'placeholder.jpg'
        ];

        foreach ($attributes as $code => $value) {
            $attrStmt = $pdo->prepare("
                INSERT INTO catalog_product_attribute (product_entity_id, attribute_code, attribute_value, created_at)
                VALUES (:entity_id, :code, :value, NOW())
                ON CONFLICT ON CONSTRAINT catalog_product_attribute_composite_unique DO NOTHING
            ");
            $attrStmt->execute([
                ':entity_id' => $entity_id,
                ':code' => $code,
                ':value' => $value
            ]);
        }
        echo "Inserted/Updated Product: $name (ID: $entity_id)\n";
    }
}

echo "Done.";
