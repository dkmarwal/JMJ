<?php
/**
 * JMJ Enterprises Solutions - Core Configuration & Bootstrap
 * PHP 8+ Architecture
 */

declare(strict_types=1);

// Directory Constants
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', ROOT_PATH . '/config');
}
if (!defined('CORE_PATH')) {
    define('CORE_PATH', ROOT_PATH . '/core');
}
if (!defined('VIEWS_PATH')) {
    define('VIEWS_PATH', ROOT_PATH . '/views');
}
if (!defined('UPLOADS_PATH')) {
    define('UPLOADS_PATH', ROOT_PATH . '/uploads');
}
if (!defined('ASSETS_PATH')) {
    define('ASSETS_PATH', ROOT_PATH . '/assets');
}

/**
 * Environment (.env) parser
 */
if (!function_exists('loadEnv')) {
    function loadEnv(string $filePath): void {
        if (!file_exists($filePath)) {
            return;
        }
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                [$name, $value] = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                $value = trim($value, '"\'');
                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv(sprintf('%s=%s', $name, $value));
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }
}

// Load .env
loadEnv(CONFIG_PATH . '/.env');

/**
 * Helper to fetch environment variables
 */
if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        $val = getenv($key);
        if ($val === false) {
            $val = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        }
        if ($val === 'true' || $val === '(true)') return true;
        if ($val === 'false' || $val === '(false)') return false;
        if ($val === 'null' || $val === '(null)') return null;
        if ($val === 'empty' || $val === '(empty)') return '';
        return $val;
    }
}

// Determine Dynamic Base URL
$detectedScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
$detectedHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$detectedScript = $_SERVER['SCRIPT_NAME'] ?? '';
$detectedSubdir = rtrim(dirname($detectedScript), '/\\');
$defaultAppUrl = $detectedScheme . '://' . $detectedHost . ($detectedSubdir !== '' && $detectedSubdir !== '/' ? $detectedSubdir : '');

// Global Application Constants
define('APP_NAME', (string)env('APP_NAME', 'JMJ Enterprises Solutions'));
define('APP_URL', rtrim((string)env('APP_URL', $defaultAppUrl), '/'));
define('APP_ENV', (string)env('APP_ENV', 'local'));
define('APP_DEBUG', (bool)env('APP_DEBUG', true));
define('APP_KEY', (string)env('APP_KEY', 'jmj_enterprise_secret_key_2026_default'));

// Error Reporting
if (APP_DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

// Ensure Upload Directory Tree Exists
$uploadDirs = [
    UPLOADS_PATH,
    UPLOADS_PATH . '/media',
    UPLOADS_PATH . '/blogs',
    UPLOADS_PATH . '/services',
    UPLOADS_PATH . '/gallery',
    UPLOADS_PATH . '/testimonials'
];
foreach ($uploadDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// PSR-4 Style Class Autoloader
spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Core\\' => ROOT_PATH . '/core/',
        'Controllers\\' => ROOT_PATH . '/controllers/',
        'Models\\' => ROOT_PATH . '/models/',
        'Services\\' => ROOT_PATH . '/services/'
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// Require Core Helper Functions
require_once CORE_PATH . '/Helpers.php';
