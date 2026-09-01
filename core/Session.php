<?php
/**
 * JMJ Enterprises Solutions - Session Manager
 * Hardened Cookie & Flash Message Engine
 */

declare(strict_types=1);

namespace Core;

class Session {
    private static bool $started = false;

    public static function start(): void {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $lifetime = (int)env('SESSION_LIFETIME', 7200);
        $secure = (bool)env('SESSION_SECURE', false);

        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_strict_mode', '1');

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        session_start();
        self::$started = true;

        // Session timeout validation
        if (isset($_SESSION['_last_activity']) && (time() - $_SESSION['_last_activity'] > $lifetime)) {
            self::destroy();
            session_start();
        }
        $_SESSION['_last_activity'] = time();
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
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
        $_SESSION['_flash'][] = [
            'type'    => $type,
            'message' => $message
        ];
    }

    public static function getFlashes(): array {
        self::start();
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flashes;
    }

    public static function regenerate(): void {
        self::start();
        session_regenerate_id(true);
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
