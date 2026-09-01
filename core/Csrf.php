<?php
/**
 * JMJ Enterprises Solutions - Cryptographic CSRF Protection
 */

declare(strict_types=1);

namespace Core;

class Csrf {
    private const SESSION_KEY = '_csrf_token';

    public static function getToken(): string {
        Session::start();
        $token = Session::get(self::SESSION_KEY);
        if (empty($token)) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::SESSION_KEY, $token);
        }
        return $token;
    }

    public static function validate(?string $token = null): bool {
        Session::start();
        $stored = Session::get(self::SESSION_KEY);
        if (empty($stored)) {
            return false;
        }

        if ($token === null) {
            // Check POST parameter
            $token = $_POST['csrf_token'] ?? '';
            // Or check X-CSRF-Token header for AJAX requests
            if (empty($token)) {
                $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            }
        }

        if (empty($token)) {
            return false;
        }

        return hash_equals($stored, $token);
    }

    public static function verifyOrDie(): void {
        if (!self::validate()) {
            http_response_code(419);
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'CSRF security token expired. Please refresh the page.']);
                exit;
            }
            die("<div style='font-family:sans-serif;padding:30px;text-align:center;'><h2>419 - Security Token Expired</h2><p>Your session has expired. Please go back, refresh the page, and try again.</p></div>");
        }
    }
}
