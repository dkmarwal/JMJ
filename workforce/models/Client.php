<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Client & Site Infrastructure Models
 */

declare(strict_types=1);

namespace Models;

use Core\Database;
use Core\Auth;

class Client {
    public static function all(?int $companyId = null): array {
        $db = Database::getInstance();
        $cid = $companyId ?? Auth::companyId();
        return $db->fetchAll(
            "SELECT c.*, 
                    (SELECT COUNT(*) FROM sites s WHERE s.client_id = c.id AND s.status = 'active') as active_sites_count,
                    (SELECT COUNT(*) FROM contracts ct WHERE ct.client_id = c.id AND ct.status = 'active') as active_contracts_count
             FROM clients c 
             WHERE c.company_id = :cid 
             ORDER BY c.company_name ASC",
            ['cid' => $cid]
        );
    }

    public static function find(int $id, ?int $companyId = null): ?array {
        $db = Database::getInstance();
        $cid = $companyId ?? Auth::companyId();
        return $db->fetch("SELECT * FROM clients WHERE id = :id AND company_id = :cid", ['id' => $id, 'cid' => $cid]);
    }

    public static function sites(int $clientId): array {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT s.*, b.name as branch_name,
                    (SELECT COUNT(*) FROM shifts sh WHERE sh.site_id = s.id AND sh.status = 'active') as shifts_count,
                    (SELECT COUNT(*) FROM employee_deployments ed WHERE ed.site_id = s.id AND ed.status = 'active') as deployed_staff_count
             FROM sites s
             LEFT JOIN branches b ON s.branch_id = b.id
             WHERE s.client_id = :cid
             ORDER BY s.site_name ASC",
            ['cid' => $clientId]
        );
    }

    public static function contacts(int $clientId): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM client_contacts WHERE client_id = :cid ORDER BY is_primary DESC, name ASC", ['cid' => $clientId]);
    }
}
