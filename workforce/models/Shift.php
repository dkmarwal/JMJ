<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Shift Model
 */

declare(strict_types=1);

namespace Models;

use Core\Database;
use Core\Auth;

class Shift {
    public static function allBySite(int $siteId): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM shifts WHERE site_id = :sid AND status = 'active' ORDER BY start_time ASC", ['sid' => $siteId]);
    }

    public static function templates(?int $companyId = null): array {
        $db = Database::getInstance();
        $cid = $companyId ?? Auth::companyId();
        return $db->fetchAll("SELECT * FROM shift_templates WHERE company_id = :cid ORDER BY start_time ASC", ['cid' => $cid]);
    }
}
