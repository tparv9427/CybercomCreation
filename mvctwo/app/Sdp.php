<?php
class Sdp{
    static public function run(){
        $front = new Core_Controllers_Front();
        $url =  $_SERVER['REQUEST_URI'];
        $func = str_replace( '/' , '_' ,$url);
        $func = substr($func,1);
        $func = implode('_', array_map('ucfirst', explode('_', $func)));
        echo $func."()";
        
    } 
}
?>