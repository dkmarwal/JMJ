<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Automated End-to-End Verification Test Suite
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

use Core\Database;
use Core\Auth;
use Services\GeofenceService;
use Services\DynamicQRService;
use Services\AttendanceService;
use Services\PatrolTourService;
use Services\RelieverService;
use Services\PayrollService;
use Services\BillingService;

echo "====================================================================\n";
echo "  JMJ WORKFORCE PLATFORM - COMPREHENSIVE AUTOMATED TEST RUNNER      \n";
echo "====================================================================\n\n";

$passedTests = 0;
$failedTests = 0;

function assertTest(string $name, bool $condition, string $details = ''): void {
    global $passedTests, $failedTests;
    if ($condition) {
        echo "  [PASS] {$name}" . ($details ? " ({$details})" : "") . "\n";
        $passedTests++;
    } else {
        echo "  [FAIL] {$name}" . ($details ? " -> {$details}" : "") . "\n";
        $failedTests++;
    }
}

// ============================================================================
// TEST 1: Database Connection & Tenancy
// ============================================================================
echo "[1] Testing Database Connection & Master Tenancy...\n";
try {
    $db = Database::getInstance();
    $company = $db->fetch("SELECT * FROM companies WHERE id = 1");
    assertTest("Workforce Database Connection", $company !== null, "Connected to DB '{$company['name']}'");
} catch (\Throwable $e) {
    assertTest("Workforce Database Connection", false, $e->getMessage());
}

// ============================================================================
// TEST 2: RBAC Roles & Authentication
// ============================================================================
echo "\n[2] Testing RBAC Authentication & Role Verification...\n";
$superLogin = Auth::attempt('superadmin@jmjenterprisessolutions.com', 'Admin@123456');
assertTest("Super Admin Authentication", $superLogin === true, "Role: " . Auth::role());
assertTest("Super Admin Permission Gate", Auth::can('company.manage') && Auth::can('payroll.calculate'), "All permissions granted");

$guardLogin = Auth::attempt('guard@jmjenterprises.com', 'Guard@123456');
assertTest("Security Guard Authentication", $guardLogin === true, "Role: " . Auth::role());
assertTest("Guard Role Verification", Auth::isWorker() === true, "Proper worker classification");

// ============================================================================
// TEST 3: Geofence Engine Math (Haversine Formula)
// ============================================================================
echo "\n[3] Testing Geofence Engine & Haversine Distance...\n";
// Distance between Delhi Connaught Place (28.6304, 77.2270) and Sant Nagar (28.5562, 77.2514) ~ 8.6 km
$distKm = GeofenceService::calculateDistance(28.6304, 77.2270, 28.5562, 77.2514) / 1000;
assertTest("Haversine Distance Calculation", ($distKm > 8.0 && $distKm < 9.5), "Calculated distance: " . round($distKm, 2) . " km");

// Inside 75m circle test
$insideDist = GeofenceService::calculateDistance(28.6304, 77.2270, 28.6305, 77.2271);
assertTest("Proximity Detection (<75m)", $insideDist < 75, "Distance: {$insideDist}m (Inside 75m radius)");

// ============================================================================
// TEST 4: HMAC-SHA256 Dynamic QR Token Generation & Validation
// ============================================================================
echo "\n[4] Testing Cryptographic Dynamic QR Service...\n";
$qrData = DynamicQRService::generateToken(1, 30);
assertTest("Dynamic QR Token Generation", !empty($qrData['token']), "Expires in: {$qrData['expires_in']}s");

$validation = DynamicQRService::validateToken($qrData['token'], 1);
assertTest("Valid Dynamic QR Signature Verification", $validation['valid'] === true, "Status: {$validation['status']}");

// Test Replay Prevention (Token already consumed)
$replayValidation = DynamicQRService::validateToken($qrData['token'], 1);
assertTest("QR Replay Prevention Attack Shield", $replayValidation['valid'] === false, "Status: {$replayValidation['status']}");

// Test Forged Token Signature
$forgedToken = "JMJQR:1:nonce123:9999999999:fake_forged_hash";
$forgedValidation = DynamicQRService::validateToken($forgedToken, 1);
assertTest("Forged Token Rejection", $forgedValidation['valid'] === false, "Reason: {$forgedValidation['reason']}");

// ============================================================================
// TEST 5: 4-Layer Attendance Engine (Check-In & Check-Out)
// ============================================================================
echo "\n[5] Testing 4-Layer Attendance Verification Engine...\n";
$freshQR = DynamicQRService::generateToken(1, 30);
$checkInPayload = [
    'employee_id'    => 1, // Ramesh Kumar
    'site_id'        => 1, // ABC Towers
    'shift_id'       => 1,
    'latitude'       => 28.6304,
    'longitude'      => 77.2270,
    'accuracy'       => 10,
    'qr_token'       => $freshQR['token'],
    'selfie_base64'  => 'data:image/jpeg;base64,' . base64_encode(str_repeat('SAMPLE_IMAGE_FRAME_DATA', 100)),
    'device_id'      => 'TEST_SUITE_RUNNER'
];

$checkInResult = AttendanceService::processCheckIn($checkInPayload);
assertTest("4-Layer Verified Check-In", $checkInResult['success'] === true, "Code: " . ($checkInResult['attendance_code'] ?? 'N/A') . " (Score: " . ($checkInResult['verification_score'] ?? 0) . "%)");

// Test Duplicate Check-In Prevention
$dupCheckIn = AttendanceService::processCheckIn($checkInPayload);
assertTest("Duplicate Active Check-In Block", $dupCheckIn['success'] === false, "Message: {$dupCheckIn['message']}");

// Test Check-Out Processing
$checkOutResult = AttendanceService::processCheckOut(1);
assertTest("Verified Check-Out & Hours Calculation", $checkOutResult['success'] === true, "Recorded at: {$checkOutResult['check_out_time']}");

// ============================================================================
// TEST 6: Guard Tour Patrol Checkpoint Scanning
// ============================================================================
echo "\n[6] Testing Guard Tour Patrol System...\n";
$tourStart = PatrolTourService::startTour(1, 1, 1, 1);
assertTest("Guard Patrol Tour Initialization", $tourStart['success'] === true, "Tour ID: " . ($tourStart['tour_id'] ?? 0));

if ($tourStart['success']) {
    $scanResult = PatrolTourService::scanCheckpoint($tourStart['tour_id'], 'JMJ-CP-ABC-A-001', 28.6304, 77.2270);
    assertTest("Checkpoint 1 Scan & Timing Evaluation", $scanResult['success'] === true, "Status: " . ($scanResult['status'] ?? 'N/A'));
}

// ============================================================================
// TEST 7: Emergency Reliever Matching
// ============================================================================
echo "\n[7] Testing Emergency Standby Reliever Matching...\n";
$relievers = RelieverService::findAvailableRelievers(1, 1);
assertTest("Standby Reliever Discovery", is_array($relievers), count($relievers) . " standby candidates identified");

// ============================================================================
// TEST 8: Monthly Payroll Engine Calculation
// ============================================================================
echo "\n[8] Testing Verified Attendance Payroll Engine...\n";
$payrollResult = PayrollService::calculatePeriod((int)date('n'), (int)date('Y'), 1);
assertTest("Automated Payroll Calculation", $payrollResult['success'] === true, "Processed {$payrollResult['processed_records']} staff. Net Disbursable: " . wf_format_currency($payrollResult['total_net']));

// ============================================================================
// TEST 9: Client Invoicing & Tax Calculation
// ============================================================================
echo "\n[9] Testing Client Invoicing & GST Engine...\n";
$invoiceResult = BillingService::generateClientInvoice(1, (int)date('n'), (int)date('Y'), 1);
assertTest("GST Tax Invoice Generation", $invoiceResult['success'] === true, "Invoice #{$invoiceResult['invoice_number']} (Total: " . wf_format_currency($invoiceResult['grand_total']) . ")");

// ============================================================================
// TEST 10: Scheduled Cron Execution
// ============================================================================
echo "\n[10] Testing Scheduled Cron Execution...\n";
ob_start();
require dirname(__DIR__) . '/cron/runner.php';
$cronOutput = ob_get_clean();
assertTest("Minute Background Cron Execution", str_contains($cronOutput, 'Cron Cycle Finished Successfully'), "Cron completed without errors");

echo "\n====================================================================\n";
echo "  TEST RUN COMPLETE: {$passedTests} Passed, {$failedTests} Failed\n";
echo "====================================================================\n";

if ($failedTests > 0) {
    exit(1);
}
exit(0);
