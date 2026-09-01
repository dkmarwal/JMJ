<?php
/**
 * JMJ Enterprises Solutions - Mail Service
 */

declare(strict_types=1);

namespace Services;

class MailService {
    public static function send(string $to, string $subject, string $message, array $headers = []): bool {
        $fromEmail = SettingService::get('email_corporate', (string)env('SMTP_FROM_ADDRESS', 'info@jmjenterprisessolutions.com'));
        $fromName = (string)env('SMTP_FROM_NAME', 'JMJ Enterprises Operations');

        $defaultHeaders = [
            'From'         => "{$fromName} <{$fromEmail}>",
            'Reply-To'     => $fromEmail,
            'X-Mailer'     => 'PHP/' . phpversion(),
            'Content-Type' => 'text/plain; charset=UTF-8'
        ];

        $mergedHeaders = array_merge($defaultHeaders, $headers);
        $headerStr = '';
        foreach ($mergedHeaders as $k => $v) {
            $headerStr .= "{$k}: {$v}\r\n";
        }

        // Native mail delivery
        return @mail($to, $subject, $message, $headerStr);
    }
}
