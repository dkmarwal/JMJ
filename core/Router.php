<?php
/**
 * JMJ Enterprises Solutions - Clean URL Router Engine
 */

declare(strict_types=1);

namespace Core;

use Exception;

class Router {
    private static array $routes = [];
    private static mixed $notFoundCallback = null;

    public static function get(string $path, mixed $handler): void {
        self::addRoute('GET', $path, $handler);
    }

    public static function post(string $path, mixed $handler): void {
        self::addRoute('POST', $path, $handler);
    }

    public static function any(string $path, mixed $handler): void {
        self::addRoute('ANY', $path, $handler);
    }

    public static function notFound(mixed $handler): void {
        self::$notFoundCallback = $handler;
    }

    private static function addRoute(string $method, string $path, mixed $handler): void {
        $cleanPath = '/' . trim($path, '/');
        if ($cleanPath !== '/' && str_ends_with($cleanPath, '/')) {
            $cleanPath = rtrim($cleanPath, '/');
        }

        self::$routes[] = [
            'method'  => $method,
            'path'    => $cleanPath,
            'handler' => $handler
        ];
    }

    public static function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        // Strip subfolder if installed in subfolder e.g. /jmj/
        $base = parse_url(APP_URL, PHP_URL_PATH) ?? '';
        if ($base !== '' && $base !== '/' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        $uri = '/' . trim($uri, '/');
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        foreach (self::$routes as $route) {
            if ($route['method'] !== 'ANY' && $route['method'] !== $method) {
                continue;
            }

            // Convert route placeholder {param} to regex named group
            $pattern = preg_replace('~\{([a-zA-Z0-9_]+)\}~', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '~^' . $pattern . '$~i';

            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                self::executeHandler($route['handler'], $params);
                return;
            }
        }

        // No route matched: execute 404
        if (self::$notFoundCallback) {
            self::executeHandler(self::$notFoundCallback, []);
        } else {
            http_response_code(404);
            View::render('pages/404', ['pageTitle' => '404 - Page Not Found']);
        }
    }

    private static function executeHandler(mixed $handler, array $params = []): void {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$controllerName, $action] = explode('@', $handler, 2);
            $fullClass = str_starts_with($controllerName, 'Controllers\\') ? $controllerName : 'Controllers\\' . $controllerName;

            if (!class_exists($fullClass)) {
                throw new Exception("Controller class not found: {$fullClass}");
            }

            $controller = new $fullClass();

            if (!method_exists($controller, $action)) {
                throw new Exception("Action method {$action} not found on {$fullClass}");
            }

            call_user_func_array([$controller, $action], $params);
            return;
        }

        throw new Exception("Invalid route handler format.");
    }
}
