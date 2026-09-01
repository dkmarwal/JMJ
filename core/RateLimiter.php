<?php
/**
 * JMJ Enterprises Solutions - Anti-Bruteforce & Request Rate Limiter
 */

declare(strict_types=1);

namespace Core;

class RateLimiter {
    public static function check(string $action, int $maxAttempts = 5, int $decaySeconds = 300): bool {
        Session::start();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = "_rate_limit_{$action}_" . md5($ip);

        $data = Session::get($key, ['attempts' => 0, 'first_attempt' => time()]);

        if (time() - $data['first_attempt'] > $decaySeconds) {
            $data = ['attempts' => 1, 'first_attempt' => time()];
            Session::set($key, $data);
            return true;
        }

        if ($data['attempts'] >= $maxAttempts) {
            return false;
        }

        $data['attempts']++;
        Session::set($key, $data);
        return true;
    }

    public static function clear(string $action): void {
        Session::start();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = "_rate_limit_{$action}_" . md5($ip);
        Session::remove($key);
    }
}
