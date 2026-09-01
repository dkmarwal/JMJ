<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * CSRF Protection Token Manager
 */

declare(strict_types=1);

namespace Core;

class Csrf {
    public static function getToken(): string {
        Session::start();
        $token = Session::get('_wf_csrf_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set('_wf_csrf_token', $token);
        }
        return $token;
    }

    public static function validate(?string $token = null): bool {
        Session::start();
        $sessionToken = Session::get('_wf_csrf_token');
        if (!$sessionToken) {
            return false;
        }

        if ($token === null) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        }

        if (!$token || !is_string($token)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    public static function regenerate(): string {
        Session::start();
        $token = bin2hex(random_bytes(32));
        Session::set('_wf_csrf_token', $token);
        return $token;
    }
}
