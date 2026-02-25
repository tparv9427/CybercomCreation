<?php

$data = [
    "name" => "Parv",
    "age"  => 22
];

$json = json_encode($data);

echo $json . "<br><br>";

$decoded = json_decode($json, true);

print_r($decoded);

?>