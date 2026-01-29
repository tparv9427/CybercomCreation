<?php
$colors = ["Red", "Green", "Blue", "Yellow"];
echo "Original Array: ";
print_r($colors);
echo "<br><br>";

// Remove element at index 1 (Green)
unset($colors[1]);
echo "After Removing Index 1: ";
print_r($colors);
echo "<br><br>";

// Add new element
$colors[] = "Purple";
echo "After Adding Purple: ";
print_r($colors);
echo "<br><br>";

// Reindex
$colors = array_values($colors);
echo "After Reindexing: ";
print_r($colors);
echo "<br>";
?>
