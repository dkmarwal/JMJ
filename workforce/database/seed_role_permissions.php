<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Seed Permissions for All 13 Roles
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

use Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "[*] Loading all permissions from database...\n";
$perms = $pdo->query("SELECT id, slug FROM permissions")->fetchAll(PDO::FETCH_KEY_PAIR);

$roleMappings = [
    // 1. SUPER_ADMIN: All permissions
    1 => array_keys($perms),

    // 2. ADMIN: All except cross-company tenant management
    2 => [
        'company.manage', 'branch.manage', 'roles.manage', 'users.manage',
        'clients.view', 'clients.manage', 'sites.view', 'sites.manage',
        'staff.view', 'staff.onboard', 'staff.documents',
        'shifts.manage', 'roster.manage', 'deployments.manage', 'handovers.manage', 'relievers.dispatch',
        'attendance.view', 'attendance.manage', 'attendance.override', 'attendance.disputes',
        'patrols.view', 'patrols.manage', 'tasks.manage', 'tasks.execute', 'consumables.manage',
        'audits.view', 'audits.conduct', 'audit.view',
        'incidents.report', 'incidents.manage', 'sos.respond',
        'leave.manage', 'payroll.calculate', 'payroll.approve',
        'billing.invoices', 'sla.monitor', 'reports.view', 'reports.export', 'settings.manage'
    ],

    // 3. HR_MANAGER
    3 => [
        'staff.view', 'staff.onboard', 'staff.documents', 'leave.manage',
        'payroll.calculate', 'payroll.approve', 'attendance.view', 'attendance.disputes',
        'reports.view', 'reports.export'
    ],

    // 4. OPERATIONS_MANAGER
    4 => [
        'clients.view', 'sites.view', 'sites.manage', 'staff.view',
        'shifts.manage', 'roster.manage', 'deployments.manage', 'handovers.manage', 'relievers.dispatch',
        'attendance.view', 'attendance.manage', 'attendance.override', 'attendance.disputes',
        'patrols.view', 'patrols.manage', 'tasks.manage', 'consumables.manage',
        'audits.view', 'audits.conduct', 'audit.view',
        'incidents.report', 'incidents.manage', 'sos.respond',
        'payroll.calculate', 'billing.invoices', 'sla.monitor', 'reports.view', 'reports.export'
    ],

    // 5. FIELD_OFFICER
    5 => [
        'sites.view', 'staff.view', 'attendance.view',
        'patrols.view', 'patrols.manage', 'tasks.manage',
        'audits.view', 'audits.conduct', 'audit.view',
        'incidents.report', 'incidents.manage', 'sos.respond',
        'relievers.dispatch', 'roster.manage'
    ],

    // 6. SUPERVISOR
    6 => [
        'sites.view', 'staff.view', 'attendance.view', 'attendance.manage', 'attendance.disputes',
        'roster.manage', 'handovers.manage', 'patrols.view', 'tasks.manage', 'tasks.execute',
        'consumables.manage', 'incidents.report', 'sos.respond'
    ],

    // 7. SECURITY_GUARD
    7 => [
        'patrols.view', 'tasks.execute', 'incidents.report', 'handovers.manage'
    ],

    // 8. CLEANING_STAFF
    8 => [
        'tasks.execute', 'consumables.manage', 'incidents.report'
    ],

    // 9. PANTRY_STAFF
    9 => [
        'tasks.execute', 'incidents.report'
    ],

    // 10. FACILITY_STAFF
    10 => [
        'tasks.execute', 'incidents.report'
    ],

    // 11. ACCOUNTANT
    11 => [
        'payroll.calculate', 'payroll.approve', 'billing.invoices', 'sla.monitor',
        'reports.view', 'reports.export', 'attendance.view', 'clients.view'
    ],

    // 12. CLIENT_ADMIN
    12 => [
        'client.portal.access', 'sites.view', 'attendance.view', 'patrols.view',
        'audits.view', 'billing.invoices', 'incidents.report', 'reports.view'
    ],

    // 13. CLIENT_VIEWER
    13 => [
        'client.portal.access', 'sites.view', 'attendance.view', 'reports.view'
    ]
];

$slugToId = array_flip($perms);
$insertStmt = $pdo->prepare("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES (?, ?)");

$totalLinked = 0;
foreach ($roleMappings as $roleId => $permList) {
    foreach ($permList as $permItem) {
        $permId = is_int($permItem) ? $permItem : ($slugToId[$permItem] ?? null);
        if ($permId) {
            $insertStmt->execute([$roleId, $permId]);
            $totalLinked++;
        }
    }
}

echo "[✓] Successfully linked {$totalLinked} permission mappings across all 13 RBAC roles!\n";
