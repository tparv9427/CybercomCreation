<?php
$a = 5;
$b = 10;
echo "Before Swap: a = $a, b = $b <br>";
$a = $a + $b;
$b = $a - $b;
$a = $a - $b;
echo "After Swap: a = $a, b = $b <br>";
?>
