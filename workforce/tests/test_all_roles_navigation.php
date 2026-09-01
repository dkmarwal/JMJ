<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Multi-Role Sidebar Navigation Verification Suite
 */

declare(strict_types=1);

$baseUrl = 'http://localhost:8080/jmj/workforce';

function testRoleNavigation(string $roleTitle, string $email, string $password, string $homePath = '/dashboard'): array {
    global $baseUrl;
    $cookieFile = __DIR__ . '/test_role_' . preg_replace('/[^a-z0-9]/', '_', strtolower($roleTitle)) . '.txt';
    if (file_exists($cookieFile)) unlink($cookieFile);

    // 1. Get Login Page & CSRF Token
    $ch = curl_init("{$baseUrl}/login");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $loginPage = curl_exec($ch);
    curl_close($ch);

    preg_match('/name="csrf_token" value="([^"]+)"/', (string)$loginPage, $matches);
    $csrfToken = $matches[1] ?? '';

    // 2. Perform Login
    $ch = curl_init("{$baseUrl}/login");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'csrf_token' => $csrfToken,
        'email'      => $email,
        'password'   => $password
    ]));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $authResp = curl_exec($ch);
    $authCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 3. Fetch Home Page (Dashboard or Mobile)
    $ch = curl_init("{$baseUrl}{$homePath}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $homeHtml = (string)curl_exec($ch);
    curl_close($ch);

    // 4. Extract all Sidebar navigation links from <nav> inside <aside>
    $sidebarLinks = [];
    if (preg_match('/<aside.*?>(.*?)<\/aside>/s', $homeHtml, $asideMatch)) {
        preg_match_all('/<a\s+[^>]*href=["\']([^"\']+)["\']/i', $asideMatch[1], $linkMatches);
        foreach ($linkMatches[1] as $href) {
            if ($href !== '#' && !str_starts_with($href, 'javascript:') && !str_contains($href, 'logout')) {
                $sidebarLinks[] = $href;
            }
        }
    }

    $sidebarLinks = array_unique($sidebarLinks);
    $results = [];

    foreach ($sidebarLinks as $linkUrl) {
        $targetUrl = str_starts_with($linkUrl, 'http') ? $linkUrl : (str_starts_with($linkUrl, '/') ? "http://localhost:8080{$linkUrl}" : "{$baseUrl}/{$linkUrl}");
        
        $ch = curl_init($targetUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $pageBody = (string)curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $hasAccessDenied = str_contains($pageBody, 'Access Denied');
        $hasFatalError = str_contains($pageBody, 'Fatal error') || str_contains($pageBody, 'Uncaught');

        $results[] = [
            'url'           => $linkUrl,
            'status_code'   => $httpCode,
            'access_denied' => $hasAccessDenied,
            'fatal_error'   => $hasFatalError,
            'ok'            => ($httpCode === 200 && !$hasAccessDenied && !$hasFatalError)
        ];
    }

    if (file_exists($cookieFile)) unlink($cookieFile);

    return [
        'role'        => $roleTitle,
        'links_count' => count($sidebarLinks),
        'results'     => $results
    ];
}

echo "====================================================================\n";
echo "  MULTI-ROLE SIDEBAR NAVIGATION VERIFICATION SUITE                  \n";
echo "====================================================================\n\n";

$rolesToTest = [
    ['Super Administrator', 'superadmin@jmjenterprisessolutions.com', 'Admin@123456'],
    ['Operations Manager',  'ops@jmjenterprisessolutions.com',        'Ops@123456'],
    ['Site Supervisor',     'supervisor@jmjenterprisessolutions.com', 'Super@123456'],
    ['Field Officer',       'fieldofficer@jmjenterprisessolutions.com', 'Field@123456'],
    ['HR Manager',          'hr@jmjenterprisessolutions.com',         'Hr@123456'],
    ['Client Administrator','client@abccorp.com',                     'Client@123456']
];

$totalFailed = 0;

foreach ($rolesToTest as [$roleTitle, $email, $pass]) {
    echo "--- Testing Role: {$roleTitle} ({$email}) ---\n";
    $test = testRoleNavigation($roleTitle, $email, $pass);
    echo "  Total Sidebar Links Discovered: {$test['links_count']}\n";

    foreach ($test['results'] as $r) {
        $pathOnly = parse_url($r['url'], PHP_URL_PATH) ?? $r['url'];
        if ($r['ok']) {
            echo "    [✓] {$pathOnly} -> HTTP 200 OK (Allowed & Rendered)\n";
        } else {
            echo "    [X] {$pathOnly} -> HTTP {$r['status_code']} (Access Denied: " . ($r['access_denied'] ? 'YES' : 'NO') . ", Fatal: " . ($r['fatal_error'] ? 'YES' : 'NO') . ")\n";
            $totalFailed++;
        }
    }
    echo "\n";
}

echo "====================================================================\n";
echo "  NAVIGATION TEST COMPLETE: Total Errors Across All Roles = {$totalFailed}\n";
echo "====================================================================\n";

exit($totalFailed > 0 ? 1 : 0);
