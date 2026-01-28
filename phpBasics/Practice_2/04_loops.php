<?php
for ($i = 1; $i <= 10; $i++) {
    echo "5 x $i = " . (5 * $i) . "<br>";
}

$student = [
    "name" => "Rahul",
    "age" => 23,
    "skills" => "PHP" 
];

foreach ($student as $key => $value) {
    echo "$key: $value <br>";
}
?>
