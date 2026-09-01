<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Cryptographically Signed Dynamic QR Code Engine (HMAC-SHA256)
 */

declare(strict_types=1);

namespace Services;

use Core\Database;

class DynamicQRService {
    /**
     * Generate a short-lived signed QR token for a physical client site
     * Refreshed every 30 seconds to prevent screenshot reuse
     */
    public static function generateToken(int $siteId, int $validitySeconds = 30): array {
        $db = Database::getInstance();
        $nonce = bin2hex(random_bytes(16));
        $expiresAt = time() + $validitySeconds;
        $expiresAtFormatted = date('Y-m-d H:i:s', $expiresAt);

        $payload = "{$siteId}|{$nonce}|{$expiresAt}";
        $signature = hash_hmac('sha256', $payload, WF_APP_KEY);
        $fullToken = "JMJQR:{$siteId}:{$nonce}:{$expiresAt}:{$signature}";

        $db->insert('qr_tokens', [
            'site_id'         => $siteId,
            'token_signature' => $signature,
            'nonce'           => $nonce,
            'expires_at'      => $expiresAtFormatted,
            'is_used'         => 0
        ]);

        return [
            'token'      => $fullToken,
            'expires_in' => $validitySeconds,
            'expires_at' => $expiresAtFormatted
        ];
    }

    /**
     * Validate an incoming scanned QR token
     * @return array [valid (bool), reason, site_id]
     */
    public static function validateToken(string $tokenString, int $expectedSiteId): array {
        $parts = explode(':', trim($tokenString));
        if (count($parts) !== 5 || $parts[0] !== 'JMJQR') {
            return ['valid' => false, 'reason' => 'Invalid QR token format.', 'status' => 'INVALID'];
        }

        [$prefix, $siteId, $nonce, $expiresAt, $signature] = $parts;
        $siteId = (int)$siteId;
        $expiresAt = (int)$expiresAt;

        // 1. Verify Site Match
        if ($siteId !== $expectedSiteId) {
            return ['valid' => false, 'reason' => 'QR Code belongs to a different client site.', 'status' => 'WRONG_SITE'];
        }

        // 2. Verify Cryptographic HMAC Signature
        $expectedPayload = "{$siteId}|{$nonce}|{$expiresAt}";
        $expectedSignature = hash_hmac('sha256', $expectedPayload, WF_APP_KEY);
        if (!hash_equals($expectedSignature, $signature)) {
            return ['valid' => false, 'reason' => 'Forged or tampered QR signature.', 'status' => 'FORGED'];
        }

        // 3. Verify Expiration Time
        if (time() > $expiresAt) {
            return ['valid' => false, 'reason' => 'QR token has expired. Please rescan live QR screen.', 'status' => 'EXPIRED'];
        }

        // 4. Verify Single-Use Replay Prevention in Database
        $db = Database::getInstance();
        $record = $db->fetch(
            "SELECT * FROM qr_tokens WHERE token_signature = :sig AND site_id = :sid",
            ['sig' => $signature, 'sid' => $siteId]
        );

        if (!$record) {
            return ['valid' => false, 'reason' => 'QR token not registered in site terminal.', 'status' => 'NOT_FOUND'];
        }

        if ($record['is_used']) {
            return ['valid' => false, 'reason' => 'Replay attack prevented. QR code already consumed.', 'status' => 'REPLAYED'];
        }

        // Mark as used
        $db->update('qr_tokens', ['is_used' => 1], 'id = :id', ['id' => $record['id']]);

        return ['valid' => true, 'reason' => 'Valid verified token.', 'status' => 'VALID', 'site_id' => $siteId];
    }
}
