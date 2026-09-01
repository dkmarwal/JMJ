<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Branch Model
 */

declare(strict_types=1);

namespace Models;

use Core\Database;
use Core\Auth;

class Branch {
    public static function allByCompany(int $companyId): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM branches WHERE company_id = :cid AND status = 'active' ORDER BY name ASC", ['cid' => $companyId]);
    }

    public static function find(int $id, ?int $companyId = null): ?array {
        $db = Database::getInstance();
        $cid = $companyId ?? Auth::companyId();
        return $db->fetch("SELECT * FROM branches WHERE id = :id AND company_id = :cid", ['id' => $id, 'cid' => $cid]);
    }
}
