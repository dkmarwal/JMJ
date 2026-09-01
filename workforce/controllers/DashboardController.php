<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Central Operations & Executive Dashboard Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;

class DashboardController {
    public function index(): void {
        Auth::requireLogin();
        $db = Database::getInstance();
        $cid = Auth::companyId();
        $today = date('Y-m-d');

        if (Auth::isClient()) {
            $this->clientDashboard();
            return;
        }

        // Metrics aggregation
        $totalClients = (int)$db->fetchColumn("SELECT COUNT(*) FROM clients WHERE company_id = :cid AND status = 'active'", ['cid' => $cid]);
        $totalSites = (int)$db->fetchColumn("SELECT COUNT(*) FROM sites WHERE company_id = :cid AND status = 'active'", ['cid' => $cid]);
        $totalStaff = (int)$db->fetchColumn("SELECT COUNT(*) FROM employees WHERE company_id = :cid AND status = 'active'", ['cid' => $cid]);

        // Today's Live Attendance Metrics
        $presentToday = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM attendance 
             WHERE company_id = :cid AND attendance_date = :tdate AND status IN ('CHECKED_IN', 'CHECKED_OUT', 'VERIFIED')",
            ['cid' => $cid, 'tdate' => $today]
        );

        $scheduledToday = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM shift_rosters 
             WHERE company_id = :cid AND roster_date = :tdate",
            ['cid' => $cid, 'tdate' => $today]
        );

        $noShowsToday = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM shift_rosters 
             WHERE company_id = :cid AND roster_date = :tdate AND status = 'no_show'",
            ['cid' => $cid, 'tdate' => $today]
        );

        $activeIncidents = (int)$db->fetchColumn("SELECT COUNT(*) FROM incidents WHERE company_id = :cid AND status NOT IN ('RESOLVED', 'CLOSED')", ['cid' => $cid]);
        $openSOS = (int)$db->fetchColumn("SELECT COUNT(*) FROM sos_alerts WHERE company_id = :cid AND status IN ('TRIGGERED', 'RESPONDING')", ['cid' => $cid]);

        // Recent Live Attendance Feed
        $recentAttendance = $db->fetchAll(
            "SELECT a.*, e.first_name, e.last_name, e.employee_code, e.photo, e.designation,
                    s.site_name, s.site_code, sh.name as shift_name
             FROM attendance a
             JOIN employees e ON a.employee_id = e.id
             JOIN sites s ON a.site_id = s.id
             JOIN shifts sh ON a.shift_id = sh.id
             WHERE a.company_id = :cid AND a.attendance_date = :tdate
             ORDER BY a.check_in_time DESC LIMIT 8",
            ['cid' => $cid, 'tdate' => $today]
        );

        // Active Sites with Staffing Status
        $sitesStatus = $db->fetchAll(
            "SELECT s.*, c.company_name as client_name,
                    (SELECT COUNT(*) FROM employee_deployments ed WHERE ed.site_id = s.id AND ed.status = 'active') as assigned_count,
                    (SELECT COUNT(*) FROM attendance a WHERE a.site_id = s.id AND a.attendance_date = :tdate AND a.status IN ('CHECKED_IN', 'VERIFIED')) as live_present_count
             FROM sites s
             JOIN clients c ON s.client_id = c.id
             WHERE s.company_id = :cid AND s.status = 'active'
             ORDER BY s.site_name ASC LIMIT 6",
            ['cid' => $cid, 'tdate' => $today]
        );

        View::render('dashboard.executive', [
            'pageTitle'        => 'Executive Command Dashboard',
            'totalClients'     => $totalClients,
            'totalSites'       => $totalSites,
            'totalStaff'       => $totalStaff,
            'presentToday'     => $presentToday,
            'scheduledToday'   => $scheduledToday,
            'noShowsToday'     => $noShowsToday,
            'activeIncidents'  => $activeIncidents,
            'openSOS'          => $openSOS,
            'recentAttendance' => $recentAttendance,
            'sitesStatus'      => $sitesStatus
        ]);
    }

    private function clientDashboard(): void {
        $db = Database::getInstance();
        $clientId = Auth::clientId();
        $today = date('Y-m-d');

        $client = $db->fetch("SELECT * FROM clients WHERE id = :id", ['id' => $clientId]);
        $sites = $db->fetchAll("SELECT * FROM sites WHERE client_id = :cid AND status = 'active'", ['cid' => $clientId]);

        $deployedStaff = (int)$db->fetchColumn(
            "SELECT COUNT(DISTINCT employee_id) FROM employee_deployments ed 
             JOIN sites s ON ed.site_id = s.id 
             WHERE s.client_id = :cid AND ed.status = 'active'",
            ['cid' => $clientId]
        );

        $presentToday = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM attendance a 
             JOIN sites s ON a.site_id = s.id 
             WHERE s.client_id = :cid AND a.attendance_date = :tdate AND a.status IN ('CHECKED_IN', 'VERIFIED', 'CHECKED_OUT')",
            ['cid' => $clientId, 'tdate' => $today]
        );

        $recentAttendance = $db->fetchAll(
            "SELECT a.*, e.first_name, e.last_name, e.employee_code, e.designation, s.site_name, sh.name as shift_name
             FROM attendance a
             JOIN employees e ON a.employee_id = e.id
             JOIN sites s ON a.site_id = s.id
             JOIN shifts sh ON a.shift_id = sh.id
             WHERE s.client_id = :cid AND a.attendance_date = :tdate
             ORDER BY a.check_in_time DESC LIMIT 10",
            ['cid' => $clientId, 'tdate' => $today]
        );

        View::render('dashboard.client', [
            'pageTitle'        => 'Client Operations Portal',
            'client'           => $client,
            'sites'            => $sites,
            'deployedStaff'    => $deployedStaff,
            'presentToday'     => $presentToday,
            'recentAttendance' => $recentAttendance
        ]);
    }
}
