<?php
$userProfile = [
    "name" => "Alice",
    "email" => "alice@example.com",
    "age" => 28,
    "role" => "developer"
];
echo "Profile Keys: " . implode(", ", array_keys($userProfile)) . "<br>";
?>
