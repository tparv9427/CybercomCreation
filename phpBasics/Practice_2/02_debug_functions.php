<?php
$data = [
    "name" => "Bob",
    "skills" => ["PHP", "JS"],
    "active" => true
];

echo "<h3>print_r():</h3>";
echo "<pre>";
print_r($data);
echo "</pre>";

echo "<h3>var_dump():</h3>";
echo "<pre>";
var_dump($data);
echo "</pre>";

echo "<h3>json_encode():</h3>";
echo json_encode($data);
?>
