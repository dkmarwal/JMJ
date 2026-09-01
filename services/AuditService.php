<?php
/**
 * JMJ Enterprises Solutions - Security Audit Trail Service
 */

declare(strict_types=1);

namespace Services;

use Core\Database;
use Core\Auth;

class AuditService {
    public static function log(string $description, string $entityType = 'general', ?int $entityId = null, ?string $action = 'ACTION'): void {
        try {
            $user = Auth::user();
            $db = Database::getInstance();
            $db->insert('audit_logs', [
                'user_id'     => $user['id'] ?? null,
                'user_name'   => $user['name'] ?? 'System / Anonymous',
                'action'      => strtoupper($action),
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'description' => $description,
                'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent'  => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (\Throwable) {
            // Fail gracefully if database is undergoing setup
        }
    }
}
