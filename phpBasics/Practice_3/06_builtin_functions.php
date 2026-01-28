<?php
$str = '  Hello World  ';
$trimmed = trim($str);
echo strtolower($trimmed) . "<br>";
echo str_replace('World', 'PHP', $str);

echo "<br>";

$numbers = [1, 2, 3];
if (in_array(5, $numbers)) {
    echo "Found 5<br>";
} else {
    echo "Not found<br>";
}

array_push($numbers, 5);
print_r($numbers);

$add = [6, 7];
$merged = array_merge($numbers, $add);
print_r($merged);
?>
