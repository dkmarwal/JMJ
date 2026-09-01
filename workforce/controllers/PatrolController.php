<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Guard Tour Routes, Checkpoints & Patrol Compliance Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;
use Models\Site;

class PatrolController {
    public function index(): void {
        Auth::requirePermission('patrols.view');
        $db = Database::getInstance();
        $cid = Auth::companyId();

        $tours = $db->fetchAll(
            "SELECT pt.*, pr.name as route_name, s.site_name, s.site_code,
                    e.first_name, e.last_name, e.employee_code, sh.name as shift_name
             FROM patrol_tours pt
             JOIN patrol_routes pr ON pt.route_id = pr.id
             JOIN sites s ON pt.site_id = s.id
             JOIN employees e ON pt.guard_id = e.id
             JOIN shifts sh ON pt.shift_id = sh.id
             WHERE pt.company_id = :cid
             ORDER BY pt.start_time DESC LIMIT 50",
            ['cid' => $cid]
        );

        $routes = $db->fetchAll(
            "SELECT pr.*, s.site_name,
                    (SELECT COUNT(*) FROM patrol_route_checkpoints rcp WHERE rcp.route_id = pr.id) as checkpoints_count
             FROM patrol_routes pr
             JOIN sites s ON pr.site_id = s.id
             WHERE s.company_id = :cid
             ORDER BY s.site_name ASC, pr.name ASC",
            ['cid' => $cid]
        );

        View::render('patrols.index', [
            'pageTitle' => 'Guard Tour Patrols & Checkpoint Compliance',
            'tours'     => $tours,
            'routes'    => $routes
        ]);
    }
}
