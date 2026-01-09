<?php
/**
 * Router - Enrutador seguro del sistema
 */

class Router {
    private static $routes = [];
    private static $middleware = [];

    public static function get($path, $callback, $middleware = []) {
        self::addRoute('GET', $path, $callback, $middleware);
    }

    public static function post($path, $callback, $middleware = []) {
        self::addRoute('POST', $path, $callback, $middleware);
    }

    private static function addRoute($method, $path, $callback, $middleware) {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
            'middleware' => $middleware
        ];
    }

    public static function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach (self::$routes as $route) {
            if ($route['method'] === $requestMethod) {
                $pattern = '#^' . preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $route['path']) . '$#';

                if (preg_match($pattern, $requestUri, $matches)) {
                    array_shift($matches);

                    // Ejecutar middleware
                    foreach ($route['middleware'] as $middlewareName) {
                        if (!self::runMiddleware($middlewareName)) {
                            return;
                        }
                    }

                    // Ejecutar callback
                    call_user_func_array($route['callback'], $matches);
                    return;
                }
            }
        }

        // 404
        http_response_code(404);
        echo "Página no encontrada";
    }

    private static function runMiddleware($name) {
        $middlewareFile = APP_PATH . '/middleware/' . $name . '.php';

        if (file_exists($middlewareFile)) {
            return require $middlewareFile;
        }

        return true;
    }

    public static function redirect($url, $code = 302) {
        http_response_code($code);
        header('Location: ' . $url);
        exit;
    }
}
