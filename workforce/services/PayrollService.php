<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Verified Attendance-Driven Payroll Calculation Engine
 */

declare(strict_types=1);

namespace Services;

use Core\Database;

class PayrollService {
    /**
     * Compute monthly payroll for all active workforce staff based on verified attendance
     */
    public static function calculatePeriod(int $month, int $year, int $companyId = 1): array {
        $db = Database::getInstance();
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));
        $totalMonthDays = (int)date('t', strtotime($startDate));
        $standardWorkDays = 26;

        // Fetch or create payroll period
        $period = $db->fetch(
            "SELECT * FROM payroll_periods WHERE company_id = :cid AND month = :m AND year = :y",
            ['cid' => $companyId, 'm' => $month, 'y' => $year]
        );

        if (!$period) {
            $periodId = (int)$db->insert('payroll_periods', [
                'company_id' => $companyId,
                'month'      => $month,
                'year'       => $year,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'status'     => 'calculated'
            ]);
        } else {
            $periodId = (int)$period['id'];
        }

        // Fetch all active employees
        $employees = $db->fetchAll("SELECT * FROM employees WHERE company_id = :cid AND status = 'active'", ['cid' => $companyId]);
        $processedRecords = 0;
        $totalGross = 0;
        $totalNet = 0;

        foreach ($employees as $emp) {
            // Count verified present days in this period
            $attStats = $db->fetch(
                "SELECT COUNT(*) as present_days,
                        SUM(overtime_minutes) as total_ot_mins
                 FROM attendance
                 WHERE employee_id = :eid AND attendance_date BETWEEN :sdate AND :edate
                   AND status IN ('CHECKED_OUT', 'VERIFIED', 'CHECKED_IN')",
                ['eid' => $emp['id'], 'sdate' => $startDate, 'edate' => $endDate]
            );

            $presentDays = (float)($attStats['present_days'] ?? 0);
            $otHours = round(((float)($attStats['total_ot_mins'] ?? 0)) / 60, 2);

            // Fetch approved paid leaves
            $leaveStats = $db->fetch(
                "SELECT SUM(total_days) as paid_leaves 
                 FROM leave_requests 
                 WHERE employee_id = :eid AND status = 'approved' 
                   AND start_date BETWEEN :sdate AND :edate",
                ['eid' => $emp['id'], 'sdate' => $startDate, 'edate' => $endDate]
            );
            $paidLeaves = (float)($leaveStats['paid_leaves'] ?? 0);

            $payableDays = min($standardWorkDays, $presentDays + $paidLeaves);
            $absentDays = max(0, $standardWorkDays - $payableDays);

            // Earnings Formulas
            $basicSalary = (float)$emp['basic_salary'];
            $dailyBasic = $basicSalary / $standardWorkDays;
            $basicEarned = round($dailyBasic * $payableDays, 2);

            $hraEarned = round(((float)$emp['hra_allowance'] / $standardWorkDays) * $payableDays, 2);
            $specialEarned = round(((float)$emp['special_allowance'] / $standardWorkDays) * $payableDays, 2);

            // OT Pay calculation (Hourly rate = Daily basic / 8) * 1.5
            $hourlyRate = $dailyBasic / 8;
            $otPay = round($otHours * $hourlyRate * 1.5, 2);

            $grossPay = round($basicEarned + $hraEarned + $specialEarned + $otPay, 2);

            // Deductions (Statutory PF 12% on Basic, ESIC 0.75% on Gross)
            $pfDeduction = round($basicEarned * 0.12, 2);
            $esicDeduction = round($grossPay * 0.0075, 2);
            $uniformDeduction = 0.00;
            $advanceDeduction = 0.00;
            $totalDeductions = round($pfDeduction + $esicDeduction + $uniformDeduction + $advanceDeduction, 2);

            $netPay = max(0, round($grossPay - $totalDeductions, 2));

            // Insert or update payroll snapshot record
            $db->query(
                "INSERT INTO payroll_records 
                 (`payroll_period_id`, `employee_id`, `payable_days`, `present_days`, `absent_days`, `paid_leaves`, `overtime_hours`, `basic_earned`, `hra_earned`, `overtime_pay`, `special_allowance_earned`, `gross_pay`, `pf_deduction`, `esic_deduction`, `total_deductions`, `net_pay`, `payment_status`)
                 VALUES (:pid, :eid, :pdays, :pres, :abs, :pleave, :othrs, :be, :he, :otpay, :se, :gross, :pf, :esic, :totded, :net, 'processed')
                 ON DUPLICATE KEY UPDATE 
                    `payable_days` = VALUES(`payable_days`), `present_days` = VALUES(`present_days`),
                    `overtime_hours` = VALUES(`overtime_hours`), `gross_pay` = VALUES(`gross_pay`),
                    `total_deductions` = VALUES(`total_deductions`), `net_pay` = VALUES(`net_pay`)",
                [
                    'pid'     => $periodId,
                    'eid'     => $emp['id'],
                    'pdays'   => $payableDays,
                    'pres'    => $presentDays,
                    'abs'     => $absentDays,
                    'pleave'  => $paidLeaves,
                    'othrs'   => $otHours,
                    'be'      => $basicEarned,
                    'he'      => $hraEarned,
                    'otpay'   => $otPay,
                    'se'      => $specialEarned,
                    'gross'   => $grossPay,
                    'pf'      => $pfDeduction,
                    'esic'    => $esicDeduction,
                    'totded'  => $totalDeductions,
                    'net'     => $netPay
                ]
            );

            $processedRecords++;
            $totalGross += $grossPay;
            $totalNet += $netPay;
        }

        AuditService::log(
            "Monthly Payroll calculated for period {$month}/{$year} ({$processedRecords} staff, Gross: ₹{$totalGross}, Net: ₹{$totalNet})",
            'payroll',
            $periodId,
            'PAYROLL_CALCULATED'
        );

        return [
            'success'           => true,
            'period_id'         => $periodId,
            'processed_records' => $processedRecords,
            'total_gross'       => $totalGross,
            'total_net'         => $totalNet
        ];
    }
}
