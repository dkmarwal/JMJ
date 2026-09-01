<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Multi-Tenant Authentication & 13-Role RBAC Authorization Engine
 */

declare(strict_types=1);

namespace Core;

use Services\AuditService;

class Auth {
    private static ?array $cachedUser = null;
    private static ?array $cachedPermissions = null;

    public static function attempt(string $email, string $password): bool {
        if (!RateLimiter::check('login', 6, 300)) {
            Session::setFlash('error', 'Too many failed login attempts. Please wait 5 minutes.');
            return false;
        }

        $db = Database::getInstance();
        $user = $db->fetch(
            "SELECT u.*, r.name as role_name, r.label as role_label, c.name as company_name, c.status as company_status,
                    e.employee_code, e.photo as employee_photo, e.designation as employee_designation, e.category_id
             FROM users u
             JOIN roles r ON u.role_id = r.id
             JOIN companies c ON u.company_id = c.id
             LEFT JOIN employees e ON u.employee_id = e.id
             WHERE u.email = :email AND u.status = 'active' AND c.status = 'active'",
            ['email' => trim($email)]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            AuditService::log(
                "Failed login attempt for: {$email}",
                'security',
                0,
                'AUTH_FAIL',
                $user['company_id'] ?? null,
                null
            );
            return false;
        }

        // Login successful
        RateLimiter::reset('login');
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }

        $db->update(
            'users',
            [
                'last_login_at' => date('Y-m-d H:i:s'),
                'last_login_ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ],
            'id = :id',
            ['id' => $user['id']]
        );

        unset($user['password_hash']);
        Session::set('_wf_user', $user);
        self::$cachedUser = $user;

        // Load permissions
        self::loadPermissions((int)$user['role_id']);

        AuditService::log(
            "Successful login by user #{$user['id']} ({$user['name']}) [{$user['role_name']}]",
            'auth',
            (int)$user['id'],
            'LOGIN',
            (int)$user['company_id'],
            (int)$user['id']
        );

        return true;
    }

    public static function check(): bool {
        return self::user() !== null;
    }

    public static function user(): ?array {
        if (self::$cachedUser !== null) {
            return self::$cachedUser;
        }
        self::$cachedUser = Session::get('_wf_user');
        return self::$cachedUser;
    }

    public static function id(): ?int {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    public static function companyId(): int {
        $user = self::user();
        return $user ? (int)$user['company_id'] : 1;
    }

    public static function branchId(): ?int {
        $user = self::user();
        return $user && !empty($user['branch_id']) ? (int)$user['branch_id'] : null;
    }

    public static function clientId(): ?int {
        $user = self::user();
        return $user && !empty($user['client_id']) ? (int)$user['client_id'] : null;
    }

    public static function employeeId(): ?int {
        $user = self::user();
        return $user && !empty($user['employee_id']) ? (int)$user['employee_id'] : null;
    }

    public static function role(): string {
        $user = self::user();
        return $user['role_name'] ?? 'GUEST';
    }

    public static function hasRole(string|array $roles): bool {
        $currentRole = self::role();
        if ($currentRole === 'SUPER_ADMIN') {
            return true;
        }
        if (is_string($roles)) {
            return $currentRole === $roles;
        }
        return in_array($currentRole, $roles, true);
    }

    public static function isSuperAdmin(): bool {
        return self::role() === 'SUPER_ADMIN';
    }

    public static function isAdmin(): bool {
        return in_array(self::role(), ['SUPER_ADMIN', 'ADMIN'], true);
    }

    public static function isOperations(): bool {
        return in_array(self::role(), ['SUPER_ADMIN', 'ADMIN', 'OPERATIONS_MANAGER'], true);
    }

    public static function isFieldOfficer(): bool {
        return self::role() === 'FIELD_OFFICER';
    }

    public static function isSupervisor(): bool {
        return self::role() === 'SUPERVISOR';
    }

    public static function isWorker(): bool {
        return in_array(self::role(), ['SECURITY_GUARD', 'CLEANING_STAFF', 'PANTRY_STAFF', 'FACILITY_STAFF'], true);
    }

    public static function isClient(): bool {
        return in_array(self::role(), ['CLIENT_ADMIN', 'CLIENT_VIEWER'], true);
    }

    public static function can(string $permission): bool {
        if (self::isSuperAdmin()) {
            return true;
        }
        $perms = self::permissions();
        return in_array($permission, $perms, true);
    }

    public static function permissions(): array {
        if (self::$cachedPermissions !== null) {
            return self::$cachedPermissions;
        }
        $perms = Session::get('_wf_perms');
        if (!empty($perms)) {
            self::$cachedPermissions = $perms;
            return $perms;
        }
        $user = self::user();
        if ($user) {
            self::loadPermissions((int)$user['role_id']);
            return self::$cachedPermissions ?? [];
        }
        return [];
    }

    private static function loadPermissions(int $roleId): void {
        $db = Database::getInstance();
        $rows = $db->fetchAll(
            "SELECT p.slug FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = :rid",
            ['rid' => $roleId]
        );
        $perms = array_column($rows, 'slug');
        Session::set('_wf_perms', $perms);
        self::$cachedPermissions = $perms;
    }

    public static function requireLogin(): array {
        $user = self::user();
        if (!$user) {
            Session::setFlash('error', 'Please authenticate to access the Workforce portal.');
            redirect('login');
        }
        return $user;
    }

    public static function requirePermission(string $permission): void {
        self::requireLogin();
        if (!self::can($permission)) {
            Session::setFlash('error', "Access Denied. You do not possess the '{$permission}' capability.");
            redirect('dashboard');
        }
    }

    public static function requireRole(string|array $roles): void {
        self::requireLogin();
        if (!self::hasRole($roles)) {
            Session::setFlash('error', "Access Denied. Restricted to designated operational roles.");
            redirect('dashboard');
        }
    }

    /**
     * IDOR Protection Helper: Asserts that target resource belongs to current tenant
     */
    public static function assertTenantAccess(int $resourceCompanyId, string $resourceName = 'Resource'): void {
        if (self::isSuperAdmin()) {
            return;
        }
        if (self::companyId() !== $resourceCompanyId) {
            AuditService::log(
                "IDOR Access Violation Attempt on {$resourceName} (Tenant Mismatch: User Comp " . self::companyId() . " vs Resource Comp {$resourceCompanyId})",
                'security_violation',
                0,
                'IDOR_ATTEMPT'
            );
            http_response_code(403);
            die("<h1>403 Forbidden</h1><p>Unauthorized multi-tenant resource access.</p>");
        }
    }

    public static function logout(): void {
        $user = self::user();
        if ($user) {
            AuditService::log(
                "User logout: #{$user['id']} ({$user['name']})",
                'auth',
                (int)$user['id'],
                'LOGOUT'
            );
        }
        self::$cachedUser = null;
        self::$cachedPermissions = null;
        Session::destroy();
    }
}
