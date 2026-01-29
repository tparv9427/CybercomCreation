<?php
$name = "Parv Talati";
$length = strlen($name);
$reversed = '';

for ($i = $length - 1; $i >= 0; $i--) {
    $reversed .= $name[$i];
}

echo "Original String: " . $name . "<br>";
echo "Reversed String: " . $reversed;
?>