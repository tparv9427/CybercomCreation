<?php

namespace App\Core;

class Router
{
    protected $routes = [];

    public function __construct()
    {
        // Define Routes Here
        $this->add('/', 'HomeController', 'index');
        $this->add('/login', 'AuthController', 'login');
        $this->add('/authenticate', 'AuthController', 'authenticate');
        $this->add('/logout', 'AuthController', 'logout');

        // Future routes
        $this->add('/transfer', 'TransferController', 'index');
        $this->add('/transfer/process', 'TransferController', 'process');
    }

    public function add($route, $controller, $action)
    {
        $this->routes[$route] = ['controller' => $controller, 'action' => $action];
    }

    public function dispatch($uri)
    {
        // Strip query string
        $uri = strtok($uri, '?');

        // Basic subdirectory handling
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if (strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }
        if ($uri == '')
            $uri = '/';

        // Remove trailing slash if not root
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        if (array_key_exists($uri, $this->routes)) {
            $controllerName = "App\\Controllers\\" . $this->routes[$uri]['controller'];
            $actionName = $this->routes[$uri]['action'];

            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $actionName)) {
                    $controller->$actionName();
                    return;
                }
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "404 Not Found";
    }
}
