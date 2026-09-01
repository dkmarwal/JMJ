<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Verified Attendance Payroll & Salary Slip Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;
use Core\Session;
use Services\PayrollService;

class PayrollController {
    public function index(): void {
        Auth::requirePermission('payroll.calculate');
        $db = Database::getInstance();
        $cid = Auth::companyId();

        $periods = $db->fetchAll(
            "SELECT pp.*,
                    (SELECT COUNT(*) FROM payroll_records pr WHERE pr.payroll_period_id = pp.id) as staff_count,
                    (SELECT SUM(gross_pay) FROM payroll_records pr WHERE pr.payroll_period_id = pp.id) as total_gross,
                    (SELECT SUM(net_pay) FROM payroll_records pr WHERE pr.payroll_period_id = pp.id) as total_net
             FROM payroll_periods pp
             WHERE pp.company_id = :cid
             ORDER BY pp.year DESC, pp.month DESC",
            ['cid' => $cid]
        );

        View::render('payroll.index', [
            'pageTitle' => 'Workforce Payroll Management',
            'periods'   => $periods
        ]);
    }

    public function viewPeriod(): void {
        Auth::requirePermission('payroll.calculate');
        $db = Database::getInstance();
        $id = (int)($_GET['id'] ?? 0);

        $period = $db->fetch("SELECT * FROM payroll_periods WHERE id = :id AND company_id = :cid", ['id' => $id, 'cid' => Auth::companyId()]);
        if (!$period) {
            Session::setFlash('error', 'Payroll period not found.');
            wf_redirect('payroll');
        }

        $records = $db->fetchAll(
            "SELECT pr.*, e.first_name, e.last_name, e.employee_code, e.bank_name, e.bank_account_no, e.ifsc_code,
                    c.name as category_name
             FROM payroll_records pr
             JOIN employees e ON pr.employee_id = e.id
             JOIN employee_categories c ON e.category_id = c.id
             WHERE pr.payroll_period_id = :pid
             ORDER BY e.first_name ASC",
            ['pid' => $id]
        );

        View::render('payroll.period', [
            'pageTitle' => 'Payroll Sheet - ' . date('F Y', mktime(0, 0, 0, $period['month'], 1, $period['year'])),
            'period'    => $period,
            'records'   => $records
        ]);
    }

    public function calculate(): void {
        Auth::requirePermission('payroll.calculate');
        $month = (int)($_POST['month'] ?? date('n'));
        $year = (int)($_POST['year'] ?? date('Y'));

        $res = PayrollService::calculatePeriod($month, $year, Auth::companyId());
        Session::setFlash('success', "Payroll calculated for {$res['processed_records']} staff. Total Net Pay: " . wf_format_currency($res['total_net']));
        wf_redirect('payroll/period?id=' . $res['period_id']);
    }
}
