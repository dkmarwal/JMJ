<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Shift Schedules, Operational Rosters & Reliever Matching Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;
use Core\Session;
use Models\Site;
use Models\Shift;
use Services\RelieverService;

class ShiftController {
    public function index(): void {
        Auth::requireLogin();
        if (!Auth::can('shifts.manage') && !Auth::can('roster.manage') && !Auth::can('sites.view')) {
            Session::setFlash('error', "Access Denied. Missing 'shifts.manage' permission.");
            wf_redirect('dashboard');
        }
        $db = Database::getInstance();
        $cid = Auth::companyId();

        $shifts = $db->fetchAll(
            "SELECT sh.*, s.site_name, s.site_code, c.company_name as client_name,
                    (SELECT COUNT(*) FROM employee_deployments ed WHERE ed.shift_id = sh.id AND ed.status = 'active') as deployed_count
             FROM shifts sh
             JOIN sites s ON sh.site_id = s.id
             JOIN clients c ON s.client_id = c.id
             WHERE s.company_id = :cid
             ORDER BY s.site_name ASC, sh.start_time ASC",
            ['cid' => $cid]
        );

        $templates = Shift::templates($cid);

        View::render('shifts.index', [
            'pageTitle' => 'Shift Schedules & Configurations',
            'shifts'    => $shifts,
            'templates' => $templates
        ]);
    }

    public function roster(): void {
        Auth::requirePermission('roster.manage');
        $db = Database::getInstance();
        $cid = Auth::companyId();
        $selectedDate = $_GET['date'] ?? date('Y-m-d');
        $selectedSite = !empty($_GET['site_id']) ? (int)$_GET['site_id'] : null;

        $sites = Site::all($cid);

        $where = "WHERE s.company_id = :cid AND r.roster_date = :rdate";
        $params = ['cid' => $cid, 'rdate' => $selectedDate];

        if ($selectedSite) {
            $where .= " AND r.site_id = :sid";
            $params['sid'] = $selectedSite;
        }

        $rosterEntries = $db->fetchAll(
            "SELECT r.*, e.first_name, e.last_name, e.employee_code, e.photo, e.phone, c.name as category_name,
                    s.site_name, s.site_code, sh.name as shift_name, sh.start_time, sh.end_time,
                    (SELECT a.status FROM attendance a WHERE a.roster_id = r.id LIMIT 1) as attendance_status,
                    (SELECT a.check_in_time FROM attendance a WHERE a.roster_id = r.id LIMIT 1) as check_in_time
             FROM shift_rosters r
             JOIN employees e ON r.employee_id = e.id
             JOIN employee_categories c ON e.category_id = c.id
             JOIN sites s ON r.site_id = s.id
             JOIN shifts sh ON r.shift_id = sh.id
             {$where}
             ORDER BY s.site_name ASC, sh.start_time ASC, e.first_name ASC",
            $params
        );

        View::render('shifts.roster', [
            'pageTitle'     => 'Operational Shift Roster',
            'selectedDate'  => $selectedDate,
            'selectedSite'  => $selectedSite,
            'sites'         => $sites,
            'rosterEntries' => $rosterEntries
        ]);
    }

    public function relievers(): void {
        Auth::requirePermission('relievers.dispatch');
        $siteId = (int)($_GET['site_id'] ?? 1);
        $shiftId = (int)($_GET['shift_id'] ?? 1);

        $relievers = RelieverService::findAvailableRelievers($siteId, $shiftId);
        $site = Site::find($siteId);

        View::render('shifts.relievers', [
            'pageTitle' => 'Emergency Reliever Matching',
            'site'      => $site,
            'relievers' => $relievers,
            'shiftId'   => $shiftId
        ]);
    }

    public function dispatchReliever(): void {
        Auth::requirePermission('relievers.dispatch');
        $siteId = (int)($_POST['site_id'] ?? 0);
        $shiftId = (int)($_POST['shift_id'] ?? 0);
        $relieverId = (int)($_POST['reliever_id'] ?? 0);
        $absentId = (int)($_POST['absent_employee_id'] ?? 0);

        $result = RelieverService::dispatchReliever($siteId, $shiftId, $relieverId, $absentId);
        if ($result['success']) {
            Session::setFlash('success', $result['message']);
        } else {
            Session::setFlash('error', $result['message'] ?? 'Dispatch failed.');
        }
        wf_redirect('shifts/roster');
    }
}
