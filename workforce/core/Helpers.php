<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Global Helper Functions & Formatters
 */

declare(strict_types=1);

if (!function_exists('wf_e')) {
    /**
     * Escape HTML output safely for XSS prevention
     */
    function wf_e(?string $value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('wf_url')) {
    /**
     * Generate an absolute URL within the workforce application
     */
    function wf_url(string $path = ''): string {
        $cleanPath = '/' . ltrim($path, '/');
        if ($path === '' || $path === '/') {
            return WF_APP_URL . '/';
        }
        return WF_APP_URL . $cleanPath;
    }
}

if (!function_exists('wf_asset')) {
    /**
     * Generate asset URL with cache busting
     */
    function wf_asset(string $path): string {
        $cleanPath = ltrim($path, '/');
        $filePath = WF_ROOT_PATH . '/' . $cleanPath;
        $version = file_exists($filePath) ? filemtime($filePath) : '1.0';
        return wf_url($cleanPath) . '?v=' . $version;
    }
}

if (!function_exists('wf_redirect')) {
    /**
     * Redirect to another URL or workforce application route
     */
    function wf_redirect(string $path, int $statusCode = 302): never {
        if (!str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
            $path = wf_url($path);
        }
        header("Location: {$path}", true, $statusCode);
        exit;
    }
}

if (!function_exists('wf_json_response')) {
    /**
     * Render a standardized JSON response
     */
    function wf_json_response(bool $success, string $message, mixed $data = null, int $statusCode = 200, array $errors = []): never {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data ?? (object)[],
            'errors'  => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (!function_exists('wf_format_date')) {
    /**
     * Format a timestamp or date
     */
    function wf_format_date(?string $date, string $format = 'd M Y'): string {
        if (empty($date)) return '-';
        $time = strtotime($date);
        return $time ? date($format, $time) : '-';
    }
}

if (!function_exists('wf_format_time')) {
    /**
     * Format a time string (e.g. 06:00:00 to 06:00 AM)
     */
    function wf_format_time(?string $time, string $format = 'h:i A'): string {
        if (empty($time)) return '-';
        $timestamp = strtotime($time);
        return $timestamp ? date($format, $timestamp) : '-';
    }
}

if (!function_exists('wf_format_currency')) {
    /**
     * Format INR Currency (₹)
     */
    function wf_format_currency(float|int|string|null $amount): string {
        $val = (float)($amount ?? 0);
        return '₹' . number_format($val, 2, '.', ',');
    }
}

if (!function_exists('wf_csrf_field')) {
    /**
     * Render hidden CSRF token input
     */
    function wf_csrf_field(): string {
        return '<input type="hidden" name="csrf_token" value="' . wf_e(\Core\Csrf::getToken()) . '">';
    }
}

if (!function_exists('wf_flash')) {
    /**
     * Set a flash notification message
     */
    function wf_flash(string $type, string $message): void {
        \Core\Session::setFlash($type, $message);
    }
}

if (!function_exists('wf_is_active_route')) {
    /**
     * Check if the current route matches a path for navigation highlighting
     */
    function wf_is_active_route(string $pattern): bool {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '', '/');
        $base = trim(parse_url(WF_APP_URL, PHP_URL_PATH) ?? '', '/');
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = trim(substr($uri, strlen($base)), '/');
        }
        $pattern = trim($pattern, '/');
        if ($pattern === '' || $pattern === '/') {
            return $uri === '' || $uri === 'dashboard';
        }
        return str_starts_with($uri, $pattern);
    }
}

if (!function_exists('wf_current_user')) {
    /**
     * Retrieve authenticated user array
     */
    function wf_current_user(): ?array {
        return \Core\Auth::user();
    }
}

if (!function_exists('wf_has_permission')) {
    /**
     * Check granular permission for current user
     */
    function wf_has_permission(string $permission): bool {
        return \Core\Auth::can($permission);
    }
}

if (!function_exists('wf_generate_code')) {
    /**
     * Generate standard human-readable enterprise codes (e.g. ATT-2026-000123)
     */
    function wf_generate_code(string $prefix, int $id): string {
        $year = date('Y');
        $padded = str_pad((string)$id, 6, '0', STR_PAD_LEFT);
        return "{$prefix}-{$year}-{$padded}";
    }
}
