<?php
$marks = 85;

if ($marks >= 90) {
    echo "A";
} elseif ($marks >= 75) {
    echo "B";
} elseif ($marks >= 50) {
    echo "C";
} else {
    echo "Fail";
}

echo "<br>";

$day = "Sat";

switch ($day) {
    case "Sat":
    case "Sun":
        echo "Weekend";
        break;
    default:
        echo "Weekday";
}
?>
