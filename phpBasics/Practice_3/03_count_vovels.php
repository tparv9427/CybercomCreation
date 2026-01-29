<?php
$str = "PARV TALATI";
$count = 0;
echo "String is '" . $str . "'<br>";
for ($i = 0; $i < strlen($str); $i++) {
    if ($str[$i] == "a" || $str[$i] == "A" || $str[$i] == "e" || $str[$i] == "E" || $str[$i] == "i" || $str[$i] == "I" || $str[$i] == "o" || $str[$i] == "O" || $str[$i] == "u" || $str[$i] == "U") {
        $count++;
    }
}
echo "Number of vowels is " . $count;
?>