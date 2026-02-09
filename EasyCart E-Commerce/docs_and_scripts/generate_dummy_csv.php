<?php

$brands = ['NexusGear', 'OptimaStyle', 'UrbanPulse', 'Velociti', 'AetherWorks'];
$colors = ['Red', 'Blue', 'Green', 'Black', 'White', 'Silver', 'Gold'];
$headers = ['sku', 'name', 'price', 'stock', 'description', 'is_active', 'brand', 'color', 'image_url'];

$fp = fopen('dummy_products_250.csv', 'w');
fputcsv($fp, $headers);

foreach ($brands as $brandIndex => $brand) {
    for ($i = 1; $i <= 50; $i++) {
        $sku = strtoupper(substr($brand, 0, 3)) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
        $name = "$brand " . ucfirst($colors[array_rand($colors)]) . " Item " . $i;
        $price = rand(1000, 50000) / 100; // 10.00 to 500.00
        $stock = rand(0, 100);
        $color = $colors[array_rand($colors)];

        $row = [
            $sku,
            $name,
            $price,
            $stock,
            "This is a premium product from $brand. It features high-quality materials and a sleek design.",
            1,
            $brand,
            $color,
            'placeholder.jpg' // Using a placeholder image for now
        ];

        fputcsv($fp, $row);
    }
}

fclose($fp);

echo "Successfully generated dummy_products_250.csv with 250 products.\n";
