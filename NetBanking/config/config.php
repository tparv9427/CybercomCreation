<?php

define('APP_NAME', 'NetBanking');
define('APP_ROOT', dirname(__DIR__));
// Dynamic URL Root
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('URL_ROOT', $protocol . "://" . $host);

// DB Layout
define('DB_PATH', APP_ROOT . '/data');
