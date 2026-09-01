<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Cleaning Checklists, Pantry Tasks & Consumable Inventory Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;
use Core\Session;
use Models\Site;

class TaskController {
    public function index(): void {
        Auth::requirePermission('tasks.manage');
        $db = Database::getInstance();
        $cid = Auth::companyId();

        $tasks = $db->fetchAll(
            "SELECT t.*, s.site_name, z.zone_name, tt.title as template_title,
                    e.first_name, e.last_name, e.employee_code
             FROM tasks t
             JOIN sites s ON t.site_id = s.id
             LEFT JOIN site_zones z ON t.zone_id = z.id
             LEFT JOIN task_templates tt ON t.template_id = tt.id
             LEFT JOIN employees e ON t.assigned_employee_id = e.id
             WHERE t.company_id = :cid
             ORDER BY t.scheduled_date DESC, t.scheduled_time ASC LIMIT 50",
            ['cid' => $cid]
        );

        $templates = $db->fetchAll("SELECT * FROM task_templates WHERE company_id = :cid ORDER BY title ASC", ['cid' => $cid]);
        $inventory = $db->fetchAll(
            "SELECT ci.*, s.site_name 
             FROM consumable_inventory ci
             JOIN sites s ON ci.site_id = s.id
             WHERE ci.company_id = :cid
             ORDER BY s.site_name ASC, ci.item_name ASC",
            ['cid' => $cid]
        );

        View::render('tasks.index', [
            'pageTitle' => 'Cleaning Checklists, Pantry & Inventory',
            'tasks'     => $tasks,
            'templates' => $templates,
            'inventory' => $inventory
        ]);
    }
}
