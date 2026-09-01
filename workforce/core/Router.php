<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * High-Performance REST & MVC Route Dispatcher
 */

declare(strict_types=1);

namespace Core;

class Router {
    private static array $routes = [];

    public static function get(string $path, string|callable $handler, array $middleware = []): void {
        self::addRoute('GET', $path, $handler, $middleware);
    }

    public static function post(string $path, string|callable $handler, array $middleware = []): void {
        self::addRoute('POST', $path, $handler, $middleware);
    }

    public static function match(array $methods, string $path, string|callable $handler, array $middleware = []): void {
        foreach ($methods as $method) {
            self::addRoute(strtoupper($method), $path, $handler, $middleware);
        }
    }

    private static function addRoute(string $method, string $path, string|callable $handler, array $middleware): void {
        $path = '/' . trim($path, '/');
        self::$routes[$method][] = [
            'path'       => $path,
            'handler'    => $handler,
            'middleware' => $middleware
        ];
    }

    public static function dispatch(string $uri, string $method): void {
        $cleanUri = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Strip subfolder if running inside /jmj/workforce/
        $base = parse_url(WF_APP_URL, PHP_URL_PATH) ?? '';
        if ($base !== '' && str_starts_with($cleanUri, $base)) {
            $cleanUri = substr($cleanUri, strlen($base));
        }

        $cleanUri = '/' . trim($cleanUri, '/');
        $method = strtoupper($method);

        $routesForMethod = self::$routes[$method] ?? [];

        foreach ($routesForMethod as $route) {
            $pattern = self::convertPathToRegex($route['path']);
            if (preg_match($pattern, $cleanUri, $matches)) {
                array_shift($matches); // remove full match

                // Execute Middleware
                foreach ($route['middleware'] as $mw) {
                    if (!self::executeMiddleware($mw)) {
                        return;
                    }
                }

                // Execute Handler
                $handler = $route['handler'];
                if (is_callable($handler)) {
                    call_user_func_array($handler, $matches);
                    return;
                }

                if (is_string($handler) && str_contains($handler, '@')) {
                    [$controllerName, $actionName] = explode('@', $handler);
                    $fullController = 'Controllers\\' . $controllerName;

                    if (!class_exists($fullController)) {
                        self::handleError(500, "Controller class not found: {$fullController}");
                        return;
                    }

                    $controller = new $fullController();
                    if (!method_exists($controller, $actionName)) {
                        self::handleError(500, "Action method not found: {$fullController}@{$actionName}");
                        return;
                    }

                    call_user_func_array([$controller, $actionName], $matches);
                    return;
                }
            }
        }

        // 404 Route Not Found
        self::handle404($cleanUri);
    }

    private static function convertPathToRegex(string $path): string {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#i';
    }

    private static function executeMiddleware(string $mw): bool {
        if ($mw === 'auth') {
            if (!Auth::check()) {
                if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api') || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))) {
                    wf_json_response(false, 'Unauthenticated. Please log in.', null, 401);
                }
                wf_redirect('login');
                return false;
            }
        } elseif ($mw === 'guest') {
            if (Auth::check()) {
                if (Auth::isWorker()) {
                    wf_redirect('mobile');
                } else {
                    wf_redirect('dashboard');
                }
                return false;
            }
        } elseif (str_starts_with($mw, 'role:')) {
            $requiredRoles = explode(',', substr($mw, 5));
            if (!Auth::hasRole($requiredRoles)) {
                if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api')) {
                    wf_json_response(false, 'Access Denied: Insufficient operational privileges.', null, 403);
                }
                Session::setFlash('error', 'Access Denied: Restricted operational domain.');
                wf_redirect('dashboard');
                return false;
            }
        } elseif (str_starts_with($mw, 'permission:')) {
            $requiredPerm = substr($mw, 11);
            if (!Auth::can($requiredPerm)) {
                if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api')) {
                    wf_json_response(false, "Access Denied: Permission '{$requiredPerm}' required.", null, 403);
                }
                Session::setFlash('error', "Access Denied: Missing '{$requiredPerm}' permission.");
                wf_redirect('dashboard');
                return false;
            }
        }
        return true;
    }

    private static function handle404(string $uri): void {
        http_response_code(404);
        if (str_starts_with($uri, '/api')) {
            wf_json_response(false, "API Endpoint Not Found: {$uri}", null, 404);
        }
        View::render('auth.404', ['uri' => $uri, 'pageTitle' => '404 - Page Not Found'], 'auth');
    }

    private static function handleError(int $code, string $message): void {
        http_response_code($code);
        if (WF_APP_DEBUG) {
            die("<h1>HTTP {$code} Error</h1><p>" . wf_e($message) . "</p>");
        }
        die("<h1>Application Error</h1><p>An internal system error occurred.</p>");
    }
}
