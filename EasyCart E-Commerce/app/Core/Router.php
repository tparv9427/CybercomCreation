<?php
/**
 * Router Class
 * 
 * Modern routing system with parameter extraction and named routes
 */

namespace EasyCart\Core;

class Router
{
    private array $routes = [];
    private array $namedRoutes = [];
    private array $middleware = [];

    /**
     * Add GET route
     */
    public function get(string $path, $handler, ?string $name = null, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $name, $middleware);
    }

    /**
     * Add POST route
     */
    public function post(string $path, $handler, ?string $name = null, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $name, $middleware);
    }

    /**
     * Add route for both GET and POST
     */
    public function any(string $path, $handler, ?string $name = null, array $middleware = []): void
    {
        $this->addRoute(['GET', 'POST'], $path, $handler, $name, $middleware);
    }

    /**
     * Add route for multiple methods
     */
    public function match(array $methods, string $path, $handler, ?string $name = null, array $middleware = []): void
    {
        $this->addRoute($methods, $path, $handler, $name, $middleware);
    }

    /**
     * Internal method to add route
     */
    private function addRoute($methods, string $path, $handler, ?string $name, array $middleware): void
    {
        $methods = (array) $methods;

        foreach ($methods as $method) {
            $this->routes[$method][$path] = [
                'handler' => $handler,
                'middleware' => $middleware
            ];
        }

        if ($name) {
            $this->namedRoutes[$name] = $path;
        }
    }

    /**
     * Dispatch request to appropriate handler
     */
    public function dispatch(string $method, string $uri): mixed
    {
        // Ensure we have valid values
        $method = strtoupper($method ?? 'GET');
        $uri = $uri ?? '/';

        // Remove query string
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Remove trailing slash (except for root)
        $uri = rtrim($uri, '/') ?: '/';

        // Check exact match first
        if (isset($this->routes[$method][$uri])) {
            return $this->executeRoute($this->routes[$method][$uri], []);
        }

        // Check pattern matches
        foreach ($this->routes[$method] ?? [] as $pattern => $route) {
            if ($params = $this->matchPattern($pattern, $uri)) {
                return $this->executeRoute($route, $params);
            }
        }

        // 404 Not Found
        $this->handleNotFound();
        return null;
    }

    /**
     * Match route pattern against URI
     */
    private function matchPattern(string $pattern, string $uri): array|false
    {
        // Convert route pattern to regex
        // {id} becomes (?P<id>[^/]+)
        // {id:\d+} becomes (?P<id>\d+)
        $pattern = preg_replace_callback(
            '/\{([a-zA-Z0-9_]+)(?::([^}]+))?\}/',
            function ($matches) {
                $name = $matches[1];
                $regex = $matches[2] ?? '[^/]+';
                return "(?P<$name>$regex)";
            },
            $pattern
        );

        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            // Extract named parameters only
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return false;
    }

    /**
     * Execute route with middleware and handler
     */
    private function executeRoute(array $route, array $params): mixed
    {
        // Execute middleware
        foreach ($route['middleware'] as $middleware) {
            if (is_callable($middleware)) {
                $middleware();
            } elseif (is_string($middleware) && method_exists(Middleware::class, $middleware)) {
                Middleware::$middleware();
            }
        }

        // Execute handler
        return $this->executeHandler($route['handler'], $params);
    }

    /**
     * Execute route handler
     */
    private function executeHandler($handler, array $params): mixed
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            return $controller->$method(...array_values($params));
        }

        if (is_callable($handler)) {
            return $handler(...array_values($params));
        }

        throw new \Exception("Invalid route handler");
    }

    /**
     * Handle 404 Not Found
     */
    private function handleNotFound(): void
    {
        http_response_code(404);

        // Check if we have an error controller
        if (class_exists('\EasyCart\Controllers\ErrorController')) {
            $controller = new \EasyCart\Controllers\ErrorController();
            if (method_exists($controller, 'notFound')) {
                $controller->notFound();
                return;
            }
        }

        // Default 404 page
        echo '<h1>404 - Page Not Found</h1>';
        echo '<p>The page you are looking for does not exist.</p>';
    }

    /**
     * Generate URL for named route
     */
    public function url(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("Route '$name' not found");
        }

        $path = $this->namedRoutes[$name];

        // Replace parameters in path
        foreach ($params as $key => $value) {
            $path = preg_replace(
                '/\{' . $key . '(?::[^}]+)?\}/',
                $value,
                $path
            );
        }

        return $path;
    }

    /**
     * Get all registered routes (for debugging)
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
