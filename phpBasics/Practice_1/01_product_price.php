<?php
$productName = "Laptop";
$price = 1000;
$discount = 10;
$finalPrice = $price - ($price * ($discount / 100));
echo "Product: $productName <br>";
echo "Original Price: $price <br>";
echo "Discount: $discount% <br>";
echo "Final Price: $finalPrice <br>";
?>
