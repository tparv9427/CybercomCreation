<?php

declare(strict_types=1);

function calculateTotal(float $price, int $qty): float {
    return $price * $qty;
}

$total = calculateTotal(100.50, 2);
echo "Total: " . $total . "<br>";

$x = 10; 

function testScope() {
    $y = 5; 
    echo "Local: $y <br>";
}

testScope();
echo "Global: $x";
?>
