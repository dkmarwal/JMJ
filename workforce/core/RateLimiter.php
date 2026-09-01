<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * IP & User Rate Limiter
 */

declare(strict_types=1);

namespace Core;

class RateLimiter {
    public static function check(string $action, int $maxAttempts = 5, int $decaySeconds = 300): bool {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = "_wf_rate_{$action}_" . md5($ip);

        $data = Session::get($key, ['attempts' => 0, 'first_attempt' => time()]);

        if (time() - $data['first_attempt'] > $decaySeconds) {
            $data = ['attempts' => 0, 'first_attempt' => time()];
        }

        if ($data['attempts'] >= $maxAttempts) {
            return false;
        }

        $data['attempts']++;
        Session::set($key, $data);
        return true;
    }

    public static function reset(string $action): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = "_wf_rate_{$action}_" . md5($ip);
        Session::remove($key);
    }
}
