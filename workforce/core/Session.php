<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Secure Session Manager
 */

declare(strict_types=1);

namespace Core;

class Session {
    private static bool $started = false;

    public static function start(): void {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (!headers_sent() && php_sapi_name() !== 'cli') {
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_strict_mode', '1');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            $lifetime = (string)preg_replace('/[^0-9]/', '', (string)($_ENV['SESSION_LIFETIME'] ?? '28800'));
            ini_set('session.gc_maxlifetime', $lifetime ?: '28800');

            session_name('JMJ_WORKFORCE_SESSION');
            session_start();
        } else {
            @session_start();
        }
        self::$started = true;

        // Session hijacking prevention via user-agent validation
        $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        if (!isset($_SESSION['_wf_ua'])) {
            $_SESSION['_wf_ua'] = $currentUserAgent;
        } elseif ($_SESSION['_wf_ua'] !== $currentUserAgent) {
            self::destroy();
            return;
        }

        // Periodic session ID regeneration
        if (!isset($_SESSION['_wf_created'])) {
            $_SESSION['_wf_created'] = time();
        } elseif (time() - $_SESSION['_wf_created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['_wf_created'] = time();
        }
    }

    public static function set(string $key, mixed $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function setFlash(string $type, string $message): void {
        self::start();
        $_SESSION['_wf_flash'][$type][] = $message;
    }

    public static function getFlash(string $type): array {
        self::start();
        $messages = $_SESSION['_wf_flash'][$type] ?? [];
        unset($_SESSION['_wf_flash'][$type]);
        return $messages;
    }

    public static function hasFlash(string $type): bool {
        self::start();
        return !empty($_SESSION['_wf_flash'][$type]);
    }

    public static function destroy(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            session_destroy();
            self::$started = false;
        }
    }
}
