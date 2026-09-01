<?php
/**
 * JMJ Enterprises Solutions - Authentication & RBAC Authorization
 */

declare(strict_types=1);

namespace Core;

class Auth {
    private const SESSION_USER_KEY = 'admin_user';

    public static function check(): bool {
        Session::start();
        return Session::has(self::SESSION_USER_KEY);
    }

    public static function user(): ?array {
        Session::start();
        return Session::get(self::SESSION_USER_KEY);
    }

    public static function id(): ?int {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    public static function role(): ?string {
        $user = self::user();
        return $user ? (string)$user['role_name'] : null;
    }

    public static function attempt(string $email, string $password): bool {
        $db = Database::getInstance();
        $user = $db->fetchOne(
            "SELECT u.*, r.name as role_name, r.label as role_label 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.email = :email AND u.is_archived = 0 AND u.status = 'active' 
             LIMIT 1",
            ['email' => trim($email)]
        );

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        // Update login stats
        $db->update(
            'users',
            [
                'last_login_at' => date('Y-m-d H:i:s'),
                'last_login_ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ],
            'id = :id',
            ['id' => $user['id']]
        );

        // Fetch user permissions
        $permissions = $db->fetchAll(
            "SELECT p.slug 
             FROM role_permissions rp 
             JOIN permissions p ON rp.permission_id = p.id 
             WHERE rp.role_id = :role_id",
            ['role_id' => $user['role_id']]
        );
        $user['permissions'] = array_column($permissions, 'slug');

        unset($user['password_hash']);
        Session::regenerate();
        Session::set(self::SESSION_USER_KEY, $user);

        // Log audit event
        \Services\AuditService::log('Admin logged in successfully', 'user', (int)$user['id']);

        return true;
    }

    public static function logout(): void {
        if (self::check()) {
            \Services\AuditService::log('Admin logged out', 'user', self::id());
        }
        Session::remove(self::SESSION_USER_KEY);
        Session::destroy();
    }

    public static function can(string $permission): bool {
        $user = self::user();
        if (!$user) {
            return false;
        }

        // Super admin has all permissions
        if ($user['role_name'] === 'super_admin') {
            return true;
        }

        $userPerms = $user['permissions'] ?? [];
        return in_array($permission, $userPerms, true);
    }

    public static function requireLogin(): array {
        if (!self::check()) {
            Session::setFlash('error', 'Please log in to access the administration console.');
            redirect('admin/login.php');
        }
        return self::user();
    }

    public static function requirePermission(string $permission): array {
        $user = self::requireLogin();
        if (!self::can($permission)) {
            Session::setFlash('error', 'You do not have permission to access that resource.');
            redirect('admin/dashboard.php');
        }
        return $user;
    }
}
