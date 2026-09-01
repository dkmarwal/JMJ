<?php
/**
 * JMJ Enterprises Solutions - Core Helper Functions
 */

declare(strict_types=1);

if (!function_exists('e')) {
    /**
     * Escape HTML output safely for XSS prevention
     */
    function e(?string $value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    /**
     * Generate an absolute URL within the application
     */
    function url(string $path = ''): string {
        $cleanPath = '/' . ltrim($path, '/');
        if ($path === '' || $path === '/') {
            return APP_URL . '/';
        }
        return APP_URL . $cleanPath;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate an asset URL with automatic cache-busting query parameter
     */
    function asset(string $path): string {
        $cleanPath = ltrim($path, '/');
        $filePath = ROOT_PATH . '/' . $cleanPath;
        $version = file_exists($filePath) ? filemtime($filePath) : '1.0';
        return url($cleanPath) . '?v=' . $version;
    }
}

if (!function_exists('upload_url')) {
    /**
     * Generate URL for uploaded assets with fallback placeholder
     */
    function upload_url(?string $path, string $fallback = 'img/logo.jpg'): string {
        if (empty($path)) {
            return url($fallback);
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        $cleanPath = 'uploads/' . ltrim($path, '/');
        if (file_exists(ROOT_PATH . '/' . $cleanPath)) {
            return url($cleanPath);
        }
        if (file_exists(ROOT_PATH . '/' . ltrim($path, '/'))) {
            return url(ltrim($path, '/'));
        }
        return url($fallback);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to another URL or application route
     */
    function redirect(string $path, int $statusCode = 302): never {
        if (!str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
            $path = url($path);
        }
        header("Location: {$path}", true, $statusCode);
        exit;
    }
}

if (!function_exists('slugify')) {
    /**
     * Convert any string to an SEO-friendly URL slug
     */
    function slugify(string $text): string {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text) ?: $text;
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'n-a-' . time() : $text;
    }
}

if (!function_exists('setting')) {
    /**
     * Fetch global site setting with fallback
     */
    function setting(string $key, mixed $default = null): mixed {
        return \Services\SettingService::get($key, $default);
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a timestamp or MySQL date
     */
    function format_date(?string $date, string $format = 'M d, Y'): string {
        if (empty($date)) return '';
        $timestamp = strtotime($date);
        return $timestamp ? date($format, $timestamp) : '';
    }
}

if (!function_exists('truncate_words')) {
    /**
     * Truncate text to a given word limit
     */
    function truncate_words(string $text, int $limit = 25, string $end = '...'): string {
        $clean = strip_tags($text);
        $words = preg_split('/\s+/u', trim($clean), -1, PREG_SPLIT_NO_EMPTY);
        if (count($words) <= $limit) {
            return $clean;
        }
        return implode(' ', array_slice($words, 0, $limit)) . $end;
    }
}

if (!function_exists('calculate_reading_time')) {
    /**
     * Calculate approximate reading time in minutes
     */
    function calculate_reading_time(string $text, int $wpm = 200): int {
        $words = str_word_count(strip_tags($text));
        $minutes = (int)ceil($words / $wpm);
        return max(1, $minutes);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Retrieve current CSRF token
     */
    function csrf_token(): string {
        return \Core\Csrf::getToken();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Render hidden HTML CSRF input field
     */
    function csrf_field(): string {
        return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('flash')) {
    /**
     * Set a flash message in session
     */
    function flash(string $type, string $message): void {
        \Core\Session::setFlash($type, $message);
    }
}

if (!function_exists('is_active_route')) {
    /**
     * Check if the current URI matches a pattern for active navigation state
     */
    function is_active_route(string $pattern): bool {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '', '/');
        $base = trim(parse_url(APP_URL, PHP_URL_PATH) ?? '', '/');
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = trim(substr($uri, strlen($base)), '/');
        }
        $pattern = trim($pattern, '/');
        if ($pattern === '' || $pattern === '/') {
            return $uri === '';
        }
        return str_starts_with($uri, $pattern);
    }
}

if (!function_exists('current_user')) {
    /**
     * Retrieve authenticated admin user array or object
     */
    function current_user(): ?array {
        return \Core\Auth::user();
    }
}

if (!function_exists('has_permission')) {
    /**
     * Check if logged in user has a specific permission
     */
    function has_permission(string $permission): bool {
        return \Core\Auth::can($permission);
    }
}
