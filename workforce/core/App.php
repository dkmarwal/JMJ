<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Application Kernel & Security Middleware Pipeline
 */

declare(strict_types=1);

namespace Core;

class App {
    public static function run(): void {
        self::setSecurityHeaders();

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Automatic CSRF protection on web state-modifying requests
        if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            $isApi = str_contains($uri, '/api/');
            if (!$isApi && !Csrf::validate()) {
                Session::setFlash('error', 'Security token expired or invalid. Please retry.');
                wf_redirect($_SERVER['HTTP_REFERER'] ?? 'dashboard');
                return;
            }
        }

        // Register all routes
        require_once WF_ROOT_PATH . '/routes.php';

        // Dispatch
        Router::dispatch($uri, $method);
    }

    private static function setSecurityHeaders(): void {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(self), camera=(self), microphone=()');
    }
}
