<?php
class Core_Model_Request{
    protected $_module = "page";
    protected $_controller = "index";
    protected $_action = "index";

    public function __construct(){
        
    }

    public function getRequestUri(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $fullUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $fullUrl;
    }
}
?>