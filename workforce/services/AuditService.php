<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Centralized Audit Logging Service
 */

declare(strict_types=1);

namespace Services;

use Core\Database;

class AuditService {
    public static function log(
        string $description,
        string $entityType = 'general',
        int $entityId = 0,
        string $action = 'ACTION',
        ?int $companyId = null,
        ?int $userId = null
    ): void {
        try {
            $db = Database::getInstance();
            $user = \Core\Session::get('_wf_user');

            $actualUserId = $userId ?? ($user['id'] ?? null);
            $actualUserName = $user['name'] ?? 'System/Guest';
            $actualCompanyId = $companyId ?? ($user['company_id'] ?? 1);

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'CLI/Unknown', 0, 250);

            $db->insert('audit_logs', [
                'company_id'  => $actualCompanyId,
                'user_id'     => $actualUserId,
                'user_name'   => $actualUserName,
                'action'      => strtoupper($action),
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'description' => $description,
                'ip_address'  => $ip,
                'user_agent'  => $userAgent
            ]);
        } catch (\Throwable $e) {
            // Do not crash primary execution if audit logging fails
            error_log("Audit Logging Failed: " . $e->getMessage());
        }
    }
}
