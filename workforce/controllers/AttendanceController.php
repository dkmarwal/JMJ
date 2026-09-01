<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Live Attendance, 4-Layer Verification, Disputes & Muster Roll Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;
use Core\Session;
use Models\Site;

class AttendanceController {
    public function index(): void {
        Auth::requirePermission('attendance.view');
        $db = Database::getInstance();
        $cid = Auth::companyId();
        $selectedDate = $_GET['date'] ?? date('Y-m-d');
        $selectedSite = !empty($_GET['site_id']) ? (int)$_GET['site_id'] : null;

        $sites = Site::all($cid);

        $where = "WHERE a.company_id = :cid AND a.attendance_date = :adate";
        $params = ['cid' => $cid, 'adate' => $selectedDate];

        if ($selectedSite) {
            $where .= " AND a.site_id = :sid";
            $params['sid'] = $selectedSite;
        }

        $records = $db->fetchAll(
            "SELECT a.*, e.first_name, e.last_name, e.employee_code, e.photo, e.designation,
                    s.site_name, s.site_code, sh.name as shift_name, sh.start_time, sh.end_time,
                    v.geofence_status, v.geofence_distance_meters, v.qr_status, v.selfie_path, v.face_match_status
             FROM attendance a
             JOIN employees e ON a.employee_id = e.id
             JOIN sites s ON a.site_id = s.id
             JOIN shifts sh ON a.shift_id = sh.id
             LEFT JOIN attendance_verifications v ON a.id = v.attendance_id AND v.event_type = 'check_in'
             {$where}
             ORDER BY a.check_in_time DESC",
            $params
        );

        View::render('attendance.index', [
            'pageTitle'    => 'Attendance & Biometric Verifications',
            'selectedDate' => $selectedDate,
            'selectedSite' => $selectedSite,
            'sites'        => $sites,
            'records'      => $records
        ]);
    }

    public function disputes(): void {
        Auth::requirePermission('attendance.disputes');
        $db = Database::getInstance();
        $cid = Auth::companyId();

        $disputes = $db->fetchAll(
            "SELECT d.*, e.first_name, e.last_name, e.employee_code, e.phone,
                    s.site_name, sh.name as shift_name
             FROM attendance_disputes d
             JOIN employees e ON d.employee_id = e.id
             JOIN sites s ON d.site_id = s.id
             JOIN shifts sh ON d.shift_id = sh.id
             WHERE d.company_id = :cid
             ORDER BY d.created_at DESC",
            ['cid' => $cid]
        );

        View::render('attendance.disputes', [
            'pageTitle' => 'Staff Attendance Dispute Management',
            'disputes'  => $disputes
        ]);
    }

    public function musterRoll(): void {
        Auth::requirePermission('attendance.view');
        $db = Database::getInstance();
        $cid = Auth::companyId();

        $month = (int)($_GET['month'] ?? date('n'));
        $year = (int)($_GET['year'] ?? date('Y'));
        $siteId = !empty($_GET['site_id']) ? (int)$_GET['site_id'] : 1;

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $daysInMonth = (int)date('t', strtotime($startDate));
        $sites = Site::all($cid);

        // Fetch deployed employees for this site
        $employees = $db->fetchAll(
            "SELECT e.*, c.name as category_name
             FROM employees e
             JOIN employee_categories c ON e.category_id = c.id
             WHERE e.company_id = :cid AND e.status = 'active'
             ORDER BY e.first_name ASC",
            ['cid' => $cid]
        );

        // Fetch all attendance records for this month
        $attRows = $db->fetchAll(
            "SELECT employee_id, DAY(attendance_date) as att_day, status, overtime_minutes
             FROM attendance
             WHERE company_id = :cid AND attendance_date BETWEEN :sdate AND :edate",
            ['cid' => $cid, 'sdate' => $startDate, 'edate' => date('Y-m-t', strtotime($startDate))]
        );

        $attendanceMatrix = [];
        foreach ($attRows as $row) {
            $attendanceMatrix[$row['employee_id']][$row['att_day']] = $row;
        }

        View::render('attendance.muster', [
            'pageTitle'        => 'Monthly Workforce Muster Roll',
            'month'            => $month,
            'year'             => $year,
            'siteId'           => $siteId,
            'daysInMonth'      => $daysInMonth,
            'sites'            => $sites,
            'employees'        => $employees,
            'attendanceMatrix' => $attendanceMatrix
        ]);
    }

    public function override(): void {
        Auth::requirePermission('attendance.override');
        $db = Database::getInstance();

        $attendanceId = (int)($_POST['attendance_id'] ?? 0);
        $newStatus = $_POST['status'] ?? 'VERIFIED';
        $reason = trim($_POST['override_reason'] ?? '');

        if (empty($reason)) {
            Session::setFlash('error', 'A valid justification reason is mandatory for manual attendance adjustment.');
            wf_redirect('attendance');
        }

        $db->update(
            'attendance',
            [
                'status'             => $newStatus,
                'is_manual_override' => 1,
                'override_reason'    => $reason,
                'override_by'        => Auth::id()
            ],
            'id = :id',
            ['id' => $attendanceId]
        );

        \Services\AuditService::log(
            "Manual attendance override on #{$attendanceId} to {$newStatus}. Reason: {$reason}",
            'attendance',
            $attendanceId,
            'MANUAL_OVERRIDE'
        );

        Session::setFlash('success', 'Attendance status manually updated with audit trail.');
        wf_redirect('attendance');
    }
}
