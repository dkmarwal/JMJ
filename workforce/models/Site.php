<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Site Model
 */

declare(strict_types=1);

namespace Models;

use Core\Database;
use Core\Auth;

class Site {
    public static function all(?int $companyId = null): array {
        $db = Database::getInstance();
        $cid = $companyId ?? Auth::companyId();

        $where = "WHERE s.company_id = :cid";
        $params = ['cid' => $cid];

        if (Auth::isClient()) {
            $where .= " AND s.client_id = :client_id";
            $params['client_id'] = Auth::clientId();
        }

        return $db->fetchAll(
            "SELECT s.*, c.company_name as client_name, b.name as branch_name,
                    (SELECT COUNT(*) FROM shifts sh WHERE sh.site_id = s.id AND sh.status = 'active') as shifts_count,
                    (SELECT COUNT(*) FROM employee_deployments ed WHERE ed.site_id = s.id AND ed.status = 'active') as deployed_staff_count,
                    (SELECT COUNT(*) FROM site_checkpoints cp WHERE cp.site_id = s.id AND cp.status = 'active') as checkpoints_count
             FROM sites s
             JOIN clients c ON s.client_id = c.id
             LEFT JOIN branches b ON s.branch_id = b.id
             {$where}
             ORDER BY s.site_name ASC",
            $params
        );
    }

    public static function find(int $id, ?int $companyId = null): ?array {
        $db = Database::getInstance();
        $cid = $companyId ?? Auth::companyId();
        return $db->fetch(
            "SELECT s.*, c.company_name as client_name, c.client_code, b.name as branch_name
             FROM sites s
             JOIN clients c ON s.client_id = c.id
             LEFT JOIN branches b ON s.branch_id = b.id
             WHERE s.id = :id AND s.company_id = :cid",
            ['id' => $id, 'cid' => $cid]
        );
    }

    public static function zones(int $siteId): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM site_zones WHERE site_id = :sid ORDER BY zone_name ASC", ['sid' => $siteId]);
    }

    public static function checkpoints(int $siteId): array {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT cp.*, z.zone_name 
             FROM site_checkpoints cp
             LEFT JOIN site_zones z ON cp.zone_id = z.id
             WHERE cp.site_id = :sid
             ORDER BY cp.checkpoint_name ASC",
            ['sid' => $siteId]
        );
    }

    public static function shifts(int $siteId): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM shifts WHERE site_id = :sid AND status = 'active' ORDER BY start_time ASC", ['sid' => $siteId]);
    }
}
