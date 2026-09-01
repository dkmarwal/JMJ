<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Company & Branch Models
 */

declare(strict_types=1);

namespace Models;

use Core\Database;
use Core\Auth;

class Company {
    public static function find(int $id): ?array {
        $db = Database::getInstance();
        return $db->fetch("SELECT * FROM companies WHERE id = :id", ['id' => $id]);
    }

    public static function current(): ?array {
        return self::find(Auth::companyId());
    }

    public static function all(): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM companies ORDER BY name ASC");
    }
}
