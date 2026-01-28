<?php
class User {
    public function __toString() {
        return json_encode(["message" => "This is a User Object"]);
    }

    public function __get($prop) {
        return "The property '$prop' does not exist.";
    }
}

$u = new User();
echo $u;
echo "<br>";
echo $u->address;
?>
