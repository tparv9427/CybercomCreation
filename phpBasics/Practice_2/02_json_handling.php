<?php
$product = [
    "id" => 101,
    "name" => "Smartphone",
    "price" => 599.99,
    "in_stock" => true
];

$jsonString = json_encode($product);
echo "JSON Encoded: " . $jsonString . "<br><br>";

$decodedArray = json_decode($jsonString, true);
echo "JSON Decoded (back to array): <br>";
print_r($decodedArray);
echo "<br>";
?>
