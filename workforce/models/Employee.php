<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Employee & Staff Management Model
 */

declare(strict_types=1);

namespace Models;

use Core\Database;
use Core\Auth;

class Employee {
    public static function all(?int $companyId = null, array $filters = []): array {
        $db = Database::getInstance();
        $cid = $companyId ?? Auth::companyId();

        $where = "WHERE e.company_id = :cid";
        $params = ['cid' => $cid];

        if (!empty($filters['category_id'])) {
            $where .= " AND e.category_id = :cat_id";
            $params['cat_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['status'])) {
            $where .= " AND e.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where .= " AND (e.first_name LIKE :s OR e.last_name LIKE :s OR e.employee_code LIKE :s OR e.phone LIKE :s)";
            $params['s'] = '%' . $filters['search'] . '%';
        }

        return $db->fetchAll(
            "SELECT e.*, c.name as category_name, c.department, b.name as branch_name,
                    (SELECT s.site_name FROM employee_deployments ed 
                     JOIN sites s ON ed.site_id = s.id 
                     WHERE ed.employee_id = e.id AND ed.status = 'active' LIMIT 1) as current_site_name,
                    (SELECT COUNT(*) FROM employee_documents doc WHERE doc.employee_id = e.id) as documents_count
             FROM employees e
             JOIN employee_categories c ON e.category_id = c.id
             LEFT JOIN branches b ON e.branch_id = b.id
             {$where}
             ORDER BY e.first_name ASC",
            $params
        );
    }

    public static function find(int $id, ?int $companyId = null): ?array {
        $db = Database::getInstance();
        $cid = $companyId ?? Auth::companyId();
        return $db->fetch(
            "SELECT e.*, c.name as category_name, c.department, c.standard_uniform, b.name as branch_name,
                    CONCAT(sup.first_name, ' ', sup.last_name) as supervisor_name
             FROM employees e
             JOIN employee_categories c ON e.category_id = c.id
             LEFT JOIN branches b ON e.branch_id = b.id
             LEFT JOIN employees sup ON e.supervisor_id = sup.id
             WHERE e.id = :id AND e.company_id = :cid",
            ['id' => $id, 'cid' => $cid]
        );
    }

    public static function categories(?int $companyId = null): array {
        $db = Database::getInstance();
        $cid = $companyId ?? Auth::companyId();
        return $db->fetchAll("SELECT * FROM employee_categories WHERE company_id = :cid ORDER BY name ASC", ['cid' => $cid]);
    }

    public static function documents(int $employeeId): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM employee_documents WHERE employee_id = :eid ORDER BY created_at DESC", ['eid' => $employeeId]);
    }

    public static function activeDeployment(int $employeeId): ?array {
        $db = Database::getInstance();
        return $db->fetch(
            "SELECT ed.*, s.site_name, s.site_code, s.latitude, s.longitude, s.geofence_radius,
                    sh.name as shift_name, sh.start_time, sh.end_time, sh.is_night_shift, c.company_name as client_name
             FROM employee_deployments ed
             JOIN sites s ON ed.site_id = s.id
             JOIN clients c ON s.client_id = c.id
             JOIN shifts sh ON ed.shift_id = sh.id
             WHERE ed.employee_id = :eid AND ed.status = 'active'
             LIMIT 1",
            ['eid' => $employeeId]
        );
    }
}
