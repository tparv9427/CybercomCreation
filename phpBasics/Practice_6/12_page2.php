<?php
session_start();
if (isset($_SESSION[' Talati'])) {
    echo 'Welcome ' . $_SESSION['username'];
}

if (isset($_POST['logout'])) {
    session_destroy();
}
?>
<form method="post">
    <button name="logout">Logout</button>
</form>
