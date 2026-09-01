<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Field Worker Mobile PWA Controller (/mobile/)
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;
use Models\Employee;

class MobileController {
    public function index(): void {
        Auth::requireLogin();
        $db = Database::getInstance();
        $user = Auth::user();
        $empId = (int)($user['employee_id'] ?? 1);
        $today = date('Y-m-d');

        $employee = Employee::find($empId);
        $deployment = Employee::activeDeployment($empId);

        // Check today's active attendance
        $todayAttendance = $db->fetch(
            "SELECT a.*, s.site_name, sh.name as shift_name
             FROM attendance a
             JOIN sites s ON a.site_id = s.id
             JOIN shifts sh ON a.shift_id = sh.id
             WHERE a.employee_id = :eid AND a.attendance_date = :adate
             ORDER BY a.check_in_time DESC LIMIT 1",
            ['eid' => $empId, 'adate' => $today]
        );

        // Check active patrol tour
        $activeTour = $db->fetch(
            "SELECT pt.*, pr.name as route_name 
             FROM patrol_tours pt
             JOIN patrol_routes pr ON pt.route_id = pr.id
             WHERE pt.guard_id = :gid AND pt.status = 'in_progress'
             LIMIT 1",
            ['gid' => $empId]
        );

        View::render('mobile.index', [
            'pageTitle'       => 'JMJ Field Operations PWA',
            'user'            => $user,
            'employee'        => $employee,
            'deployment'      => $deployment,
            'todayAttendance' => $todayAttendance,
            'activeTour'      => $activeTour
        ], 'mobile');
    }

    public function checkInScreen(): void {
        Auth::requireLogin();
        $user = Auth::user();
        $empId = (int)($user['employee_id'] ?? 1);
        $deployment = Employee::activeDeployment($empId);

        View::render('mobile.checkin', [
            'pageTitle'  => '4-Layer Verified Check-In',
            'user'       => $user,
            'deployment' => $deployment
        ], 'mobile');
    }

    public function patrolScreen(): void {
        Auth::requireLogin();
        $user = Auth::user();
        $empId = (int)($user['employee_id'] ?? 1);
        $deployment = Employee::activeDeployment($empId);
        $db = Database::getInstance();

        $routes = [];
        if ($deployment) {
            $routes = $db->fetchAll(
                "SELECT pr.*, 
                        (SELECT COUNT(*) FROM patrol_route_checkpoints rcp WHERE rcp.route_id = pr.id) as checkpoints_count
                 FROM patrol_routes pr
                 WHERE pr.site_id = :sid AND pr.status = 'active'",
                ['sid' => $deployment['site_id']]
            );
        }

        View::render('mobile.patrol', [
            'pageTitle'  => 'Guard Tour & Patrol',
            'user'       => $user,
            'deployment' => $deployment,
            'routes'     => $routes
        ], 'mobile');
    }
}
