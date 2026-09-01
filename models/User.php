<?php
/**
 * JMJ Enterprises Solutions - User & Staff Model
 */

declare(strict_types=1);

namespace Models;

use Core\Database;

class User {
    public static function all(): array {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT u.*, r.name as role_name, r.label as role_label 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.is_archived = 0 
             ORDER BY u.id ASC"
        );
    }

    public static function allRoles(): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM roles ORDER BY id ASC");
    }
}
