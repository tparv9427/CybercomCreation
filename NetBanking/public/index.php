<?php

// Front Controller
require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../config/config.php';

use App\Core\Router;

// Simple Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Start Session
session_start();

// Dispatch Request
$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI']);
