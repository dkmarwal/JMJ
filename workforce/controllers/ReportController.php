<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Analytics, Compliance Reports & CSV/Excel Exports Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;

class ReportController {
    public function index(): void {
        Auth::requirePermission('reports.view');
        $db = Database::getInstance();
        $cid = Auth::companyId();

        // 30-day attendance trend
        $attSummary = $db->fetchAll(
            "SELECT attendance_date, 
                    COUNT(CASE WHEN status IN ('CHECKED_IN', 'CHECKED_OUT', 'VERIFIED') THEN 1 END) as present_count,
                    COUNT(CASE WHEN status = 'MANUAL_REVIEW' THEN 1 END) as review_count,
                    AVG(verification_score) as avg_score
             FROM attendance
             WHERE company_id = :cid AND attendance_date >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
             GROUP BY attendance_date
             ORDER BY attendance_date ASC",
            ['cid' => $cid]
        );

        View::render('reports.index', [
            'pageTitle'  => 'Operational Reports & Analytics',
            'attSummary' => $attSummary
        ]);
    }

    public function exportAttendance(): void {
        Auth::requirePermission('reports.export');
        $db = Database::getInstance();
        $cid = Auth::companyId();

        $rows = $db->fetchAll(
            "SELECT a.attendance_code, a.attendance_date, a.check_in_time, a.check_out_time,
                    a.total_work_minutes, a.overtime_minutes, a.status, a.verification_score,
                    e.employee_code, e.first_name, e.last_name, e.designation,
                    s.site_name, sh.name as shift_name
             FROM attendance a
             JOIN employees e ON a.employee_id = e.id
             JOIN sites s ON a.site_id = s.id
             JOIN shifts sh ON a.shift_id = sh.id
             WHERE a.company_id = :cid
             ORDER BY a.attendance_date DESC, a.check_in_time DESC",
            ['cid' => $cid]
        );

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="JMJ_Attendance_Export_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Attendance Code', 'Date', 'Employee Code', 'Staff Name', 'Designation', 'Site Name', 'Shift', 'Check In', 'Check Out', 'Work Hours', 'OT Hours', 'Status', 'Verification Score']);

        foreach ($rows as $r) {
            fputcsv($output, [
                $r['attendance_code'],
                $r['attendance_date'],
                $r['employee_code'],
                $r['first_name'] . ' ' . $r['last_name'],
                $r['designation'],
                $r['site_name'],
                $r['shift_name'],
                $r['check_in_time'],
                $r['check_out_time'] ?? 'Active',
                round(((int)$r['total_work_minutes']) / 60, 2),
                round(((int)$r['overtime_minutes']) / 60, 2),
                $r['status'],
                $r['verification_score'] . '%'
            ]);
        }
        fclose($output);
        exit;
    }
}
