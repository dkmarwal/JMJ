<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Live HTTP & REST Route Verification Script
 */

declare(strict_types=1);

$baseUrl = 'http://localhost:8080/jmj/workforce';
$cookieFile = __DIR__ . '/test_cookie.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

function makeRequest(string $url, string $method = 'GET', array $data = [], string $cookieFile = ''): array {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    return [
        'code'     => $httpCode,
        'body'     => (string)$response,
        'error'    => $error
    ];
}

echo "====================================================================\n";
echo "  JMJ WORKFORCE PLATFORM - LIVE HTTP ROUTE VERIFICATION (PORT 8080) \n";
echo "====================================================================\n\n";

// 1. Test Login Page (Guest)
$resLogin = makeRequest("{$baseUrl}/login", 'GET', [], $cookieFile);
echo "  [1] GET /login -> HTTP {$resLogin['code']}" . ($resLogin['code'] === 200 ? " [OK]" : " [FAIL]") . "\n";

// Extract CSRF Token from login page
preg_match('/name="csrf_token" value="([^"]+)"/', $resLogin['body'], $matches);
$csrfToken = $matches[1] ?? '';

// 2. Perform Login as Super Admin
$loginPost = makeRequest("{$baseUrl}/login", 'POST', [
    'csrf_token' => $csrfToken,
    'email'      => 'superadmin@jmjenterprisessolutions.com',
    'password'   => 'Admin@123456'
], $cookieFile);
echo "  [2] POST /login (Auth as Super Admin) -> HTTP {$loginPost['code']}" . ($loginPost['code'] === 200 ? " [OK]" : " [FAIL]") . "\n";

// 3. Test Core Web Routes Authenticated
$routes = [
    '/dashboard'               => 'Executive Command Dashboard',
    '/clients'                 => 'Client Portfolio & Accounts',
    '/clients/view?id=1'       => 'Client Profile (ABC Corp)',
    '/sites'                   => 'Client Sites & Infrastructure',
    '/sites/view?id=1'         => 'Site Details & Geofence (ABC Complex)',
    '/sites/radar'             => 'Live Operations Radar & Map',
    '/staff'                   => 'Workforce Staff Directory',
    '/staff/view?id=1'         => 'Staff Profile (Ramesh Kumar)',
    '/staff/id-card?id=1'      => 'Digital ID Card Generation',
    '/shifts'                  => 'Shift Schedules',
    '/shifts/roster'           => 'Operational Roster View',
    '/shifts/relievers'        => 'Emergency Reliever Matching',
    '/attendance'              => 'Live Attendance Stream',
    '/attendance/muster'       => 'Monthly Muster Roll Matrix',
    '/attendance/disputes'     => 'Staff Attendance Disputes',
    '/patrols'                 => 'Guard Tour Patrols',
    '/tasks'                   => 'Cleaning & Consumables',
    '/incidents'               => 'Incident Command & SOS Queue',
    '/audits'                  => 'Field Officer Site Audits',
    '/payroll'                 => 'Workforce Payroll Center',
    '/payroll/period?id=1'     => 'Salary Sheet Breakdown',
    '/billing'                 => 'Client Billing & Invoices',
    '/billing/invoice?id=1'    => 'Tax Invoice View',
    '/reports'                 => 'Operational Analytics',
    '/mobile'                  => 'Field Worker Mobile PWA Home',
    '/mobile/check-in'         => 'Mobile 4-Layer Check-In Screen',
    '/mobile/patrol'           => 'Mobile Guard Patrol Screen',
    '/api/sites/1/dynamic-qr'  => 'API Dynamic QR Generator',
    '/api/radar/live-sites'    => 'API Live Radar Feed'
];

$passedRoutes = 0;
$failedRoutes = 0;

foreach ($routes as $route => $name) {
    $res = makeRequest("{$baseUrl}{$route}", 'GET', [], $cookieFile);
    if ($res['code'] === 200) {
        echo "  [✓] {$route} -> HTTP 200 OK ({$name})\n";
        $passedRoutes++;
    } else {
        echo "  [X] {$route} -> HTTP {$res['code']} ({$name})\n";
        $failedRoutes++;
    }
}

echo "\n====================================================================\n";
echo "  HTTP VERIFICATION COMPLETE: {$passedRoutes} Passed, {$failedRoutes} Failed\n";
echo "====================================================================\n";

if (file_exists($cookieFile)) unlink($cookieFile);
exit($failedRoutes > 0 ? 1 : 0);
