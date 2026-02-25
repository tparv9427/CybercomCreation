<?php 
spl_autoload_register(function ($class){
    $base = __DIR__ . "\\";
    $file = str_replace("_", "/",$class);
    sprintf("%s.php",$base.$file);
});


?>