<?php
setcookie("user_preference", "dark_mode", time() + 3600, "/");

if (isset($_COOKIE['user_preference'])) {
    echo $_COOKIE['user_preference'];
}
?>
