<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Central Configuration Bootstrap
 */

declare(strict_types=1);

define('WF_START_TIME', microtime(true));
define('WF_ROOT_PATH', dirname(__DIR__));
define('WF_STORAGE_PATH', WF_ROOT_PATH . '/storage');
define('WF_LOGS_PATH', WF_STORAGE_PATH . '/logs');

// Load Environment Variables (.env)
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || str_starts_with($line, '#')) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $value = trim($value, '"\'');
            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Global Application Constants
define('WF_APP_NAME', $_ENV['APP_NAME'] ?? 'JMJ Workforce & Operations Hub');
define('WF_APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('WF_APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('WF_APP_URL', rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080/jmj/workforce', '/'));
define('WF_APP_KEY', $_ENV['APP_KEY'] ?? 'jmj_wf_sec_token_key_2026_99x821');
define('WF_APP_TIMEZONE', $_ENV['APP_TIMEZONE'] ?? 'Asia/Kolkata');

// Set Timezone
date_default_timezone_set(WF_APP_TIMEZONE);

// Database Constants
define('WF_DB_HOST', $_ENV['DB_HOST'] ?? '127.0.0.1');
define('WF_DB_PORT', (int)($_ENV['DB_PORT'] ?? 3308));
define('WF_DB_DATABASE', $_ENV['DB_DATABASE'] ?? 'jmj_workforce_db');
define('WF_DB_USERNAME', $_ENV['DB_USERNAME'] ?? 'root');
define('WF_DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');

// Error Handling & Reporting
if (WF_APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', WF_LOGS_PATH . '/app_error.log');
}

// Auto-Register Class Autoloader (PSR-4 compliant)
spl_autoload_register(function (string $class) {
    $prefixes = [
        'Core\\'        => WF_ROOT_PATH . '/core/',
        'Controllers\\' => WF_ROOT_PATH . '/controllers/',
        'Models\\'      => WF_ROOT_PATH . '/models/',
        'Services\\'    => WF_ROOT_PATH . '/services/',
        'Middleware\\'  => WF_ROOT_PATH . '/middleware/'
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load Global Helpers
require_once WF_ROOT_PATH . '/core/Helpers.php';

// Initialize Session
\Core\Session::start();
