<?php
/**
 * JMJ Enterprise Solutions - Workforce Database Setup & Multi-Tenant Seeder
 * CLI & Web Runner with Automatic Multi-Port Detection (3308 / 3306)
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "===============================================================\n";
echo "  JMJ WORKFORCE PLATFORM - MULTI-TENANT DATABASE INITIALIZER   \n";
echo "===============================================================\n\n";

$ports = [3308, 3306, 3307];
$pdo = null;
$connectedPort = null;

foreach ($ports as $port) {
    try {
        $dsn = "mysql:host=127.0.0.1;port={$port};charset=utf8mb4";
        $pdo = new PDO($dsn, 'root', '', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ]);
        $connectedPort = $port;
        echo "[✓] Connected to MySQL/MariaDB server on 127.0.0.1:{$port}\n";
        break;
    } catch (PDOException $e) {
        // try next port
    }
}

if (!$pdo) {
    die("[X] FATAL: Could not connect to MySQL server on ports " . implode(', ', $ports) . "\n");
}

// 1. Create and Select Database
echo "[*] Creating database `jmj_workforce_db`...\n";
$pdo->exec("CREATE DATABASE IF NOT EXISTS `jmj_workforce_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `jmj_workforce_db`");
echo "[✓] Database `jmj_workforce_db` selected.\n";

// 2. Execute Schema DDL
$schemaFile = __DIR__ . '/schema.sql';
if (!file_exists($schemaFile)) {
    die("[X] FATAL: schema.sql not found at: {$schemaFile}\n");
}

echo "[*] Executing full schema table definitions...\n";
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

$sql = file_get_contents($schemaFile);
// Split on semicolons that are followed by a newline or end of file
$statements = preg_split('/;\s*(\r\n|\n|$)/', $sql);

$executedTables = 0;
foreach ($statements as $stmt) {
    $cleanStmt = trim($stmt);
    if (empty($cleanStmt)) {
        continue;
    }
    // Remove standalone comments if entire block is comment
    if (preg_match('/^--/m', $cleanStmt) && !preg_match('/CREATE|ALTER|INSERT|DROP/i', $cleanStmt)) {
        continue;
    }
    try {
        $pdo->exec($cleanStmt);
        $executedTables++;
    } catch (PDOException $e) {
        echo "   [!] Error on: " . substr(preg_replace('/\s+/', ' ', $cleanStmt), 0, 50) . "... -> " . $e->getMessage() . "\n";
    }
}
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
echo "[✓] Schema tables verified and initialized ({$executedTables} DDL statements).\n\n";

// ============================================================================
// 3. SEED INITIAL MULTI-TENANT DATA
// ============================================================================
echo "[*] Seeding Multi-Tenant Master Company & Branches...\n";

// Company Master
$pdo->exec("
INSERT INTO `companies` (`id`, `code`, `name`, `legal_name`, `email`, `phone`, `address`, `city`, `state`, `pincode`, `gst_number`, `pan_number`, `psara_license_no`, `status`)
VALUES 
(1, 'JMJ-CORP-01', 'JMJ Enterprise Solutions', 'JMJ Enterprise Solutions Private Limited', 'ops@jmjenterprisessolutions.com', '+91-9999381777', '250, Sant Nagar, East of Kailash', 'New Delhi', 'Delhi', '110065', '07AACFJ1234F1Z5', 'AACFJ1234F', 'PSARA/DL/2016/9821', 'active')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
");

// Branches
$pdo->exec("
INSERT INTO `branches` (`id`, `company_id`, `code`, `name`, `state`, `city`, `address`, `contact_person`, `contact_phone`, `contact_email`, `status`)
VALUES
(1, 1, 'DEL-HQ', 'Delhi NCR Head Office', 'Delhi', 'New Delhi', '250, Sant Nagar, East of Kailash, New Delhi', 'Command Dispatcher', '+91-9999381777', 'delhi@jmjenterprisessolutions.com', 'active'),
(2, 1, 'GGN-RO', 'Gurgaon Regional Operations', 'Haryana', 'Gurgaon', 'Tower B, Cyber City, Sector 29', 'Gurgaon Field Officer', '0124-4567890', 'gurgaon@jmjenterprisessolutions.com', 'active'),
(3, 1, 'NOI-RO', 'Noida Industrial Division', 'Uttar Pradesh', 'Noida', 'Sector 62, Electronic City', 'Noida Field Officer', '0120-9876543', 'noida@jmjenterprisessolutions.com', 'active'),
(4, 1, 'BLR-HUB', 'Bangalore South Hub', 'Karnataka', 'Bangalore', 'Whitefield Tech Zone', 'Bangalore Lead', '080-67891234', 'blr@jmjenterprisessolutions.com', 'active')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
");

// 4. Seed All 13 Roles
echo "[*] Seeding 13 RBAC Roles...\n";
$roles = [
    [1, 'SUPER_ADMIN', 'Super Administrator', 'Complete unrestricted system & multi-tenant access'],
    [2, 'ADMIN', 'Administrator', 'Operations & HR administration with configuration restrictions'],
    [3, 'HR_MANAGER', 'HR & Payroll Manager', 'Workforce onboarding, statutory compliance, leaves and payroll'],
    [4, 'OPERATIONS_MANAGER', 'Operations Manager', 'Client site deployments, rosters, live radar, SLA and incidents'],
    [5, 'FIELD_OFFICER', 'Field Inspection Officer', 'Mobile audits, surprise inspections, and site compliance verifications'],
    [6, 'SUPERVISOR', 'Site Supervisor', 'Daily site management, attendance disputes, shift handovers & patrol oversight'],
    [7, 'SECURITY_GUARD', 'Manned Security Guard', 'Mobile PWA check-in, dynamic QR scan, patrol tour and SOS reporting'],
    [8, 'CLEANING_STAFF', 'Cleaning & Sanitization Staff', 'Zone checklist execution, QR scanning, before/after photo evidence'],
    [9, 'PANTRY_STAFF', 'Pantry & Hospitality Staff', 'Water/tea inventory tracking, pantry replenishment and hospitality'],
    [10, 'FACILITY_STAFF', 'Facility & Utility Technician', 'Maintenance checklists, equipment upkeep and utility logs'],
    [11, 'ACCOUNTANT', 'Financial Accountant', 'Client billing, invoices, attendance muster rolls and tax compliance'],
    [12, 'CLIENT_ADMIN', 'Client Administrator', 'Client portal: site deployments, live muster, invoices, SLA and complaints'],
    [13, 'CLIENT_VIEWER', 'Client Stakeholder Viewer', 'Read-only client dashboard: deployed staff and daily attendance summary']
];

$roleStmt = $pdo->prepare("INSERT INTO `roles` (`id`, `name`, `label`, `description`) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `label` = VALUES(`label`), `description` = VALUES(`description`)");
foreach ($roles as $r) {
    $roleStmt->execute($r);
}

// 5. Seed Granular Permissions
echo "[*] Seeding Granular System Permissions...\n";
$permissions = [
    // Tenancy & Core
    ['company.manage', 'Manage Companies & Subscriptions', 'core'],
    ['branch.manage', 'Manage Regional Branches', 'core'],
    ['users.manage', 'Create & Manage Admin Users', 'core'],
    ['roles.manage', 'Manage Roles & Permissions Matrix', 'core'],
    ['settings.manage', 'Configure System & Global Parameters', 'core'],
    ['audit.view', 'View Comprehensive Security Audit Logs', 'core'],
    // Clients & Sites
    ['clients.view', 'View Client Portfolio', 'clients'],
    ['clients.manage', 'Create & Edit Client Profiles', 'clients'],
    ['sites.view', 'View Client Sites & Infrastructure', 'sites'],
    ['sites.manage', 'Configure Sites, Geofences & Checkpoints', 'sites'],
    ['contracts.manage', 'Manage Master Service Agreements & SLAs', 'contracts'],
    // Staff & Workforce
    ['staff.view', 'View Employee Directory', 'workforce'],
    ['staff.onboard', 'Onboard & Manage Employee Profiles', 'workforce'],
    ['staff.documents', 'Access & Verify Statutory Documents', 'workforce'],
    ['deployments.manage', 'Deploy Workforce to Client Sites', 'workforce'],
    // Shifts & Attendance
    ['shifts.manage', 'Define Shift Configurations & Templates', 'shifts'],
    ['roster.manage', 'Plan & Assign Daily Operational Rosters', 'shifts'],
    ['attendance.view', 'View Real-time Attendance & Muster', 'attendance'],
    ['attendance.manage', 'Verify & Review Flagged Attendance', 'attendance'],
    ['attendance.override', 'Perform Manual Attendance Adjustments', 'attendance'],
    ['attendance.disputes', 'Approve or Reject Staff Disputes', 'attendance'],
    ['relievers.dispatch', 'Dispatch Emergency Relievers', 'shifts'],
    // Patrols & Audits
    ['patrols.manage', 'Build Patrol Routes & Checkpoints', 'patrols'],
    ['patrols.view', 'Monitor Live Patrol Tours & Deviations', 'patrols'],
    ['audits.conduct', 'Submit Physical & Surprise Site Audits', 'audits'],
    ['audits.view', 'Inspect Field Officer Audit Reports', 'audits'],
    // Tasks & Facility
    ['tasks.manage', 'Configure Checklist Templates & Zones', 'tasks'],
    ['tasks.execute', 'Execute Cleaning & Pantry Checklists', 'tasks'],
    ['consumables.manage', 'Track & Issue Cleaning Inventory', 'tasks'],
    // Incidents & SOS
    ['incidents.report', 'Report Site Incidents & Breaches', 'incidents'],
    ['incidents.manage', 'Investigate & Resolve Incident Tickets', 'incidents'],
    ['sos.respond', 'Acknowledge & Dispatch SOS Emergencies', 'incidents'],
    ['handovers.manage', 'Record & Acknowledge Shift Handovers', 'shifts'],
    // HR & Payroll
    ['leave.manage', 'Approve & Track Employee Leave Requests', 'hr'],
    ['payroll.calculate', 'Calculate & Review Monthly Payrolls', 'payroll'],
    ['payroll.approve', 'Authorize & Finalize Salary Disbursements', 'payroll'],
    // Finance & Invoicing
    ['billing.invoices', 'Generate & Issue Client Invoices', 'finance'],
    ['sla.monitor', 'Track SLA Compliance & Penalties', 'finance'],
    // Reports & Exports
    ['reports.view', 'Access Analytics & Executive Reports', 'reports'],
    ['reports.export', 'Export PDF, Excel, and CSV Reports', 'reports'],
    // Client Portal
    ['client.portal.access', 'Access Client Self-Service Portal', 'client_portal']
];

$permStmt = $pdo->prepare("INSERT INTO `permissions` (`slug`, `label`, `module_group`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `label` = VALUES(`label`), `module_group` = VALUES(`module_group`)");
foreach ($permissions as $p) {
    $permStmt->execute($p);
}

// Assign Permissions to Roles
echo "[*] Linking Permissions to Roles...\n";
$perms = $pdo->query("SELECT id, slug FROM permissions")->fetchAll(PDO::FETCH_KEY_PAIR);
$slugToId = array_flip($perms);

$roleMappings = [
    1 => array_keys($perms),
    2 => [
        'company.manage', 'branch.manage', 'roles.manage', 'users.manage',
        'clients.view', 'clients.manage', 'sites.view', 'sites.manage',
        'staff.view', 'staff.onboard', 'staff.documents',
        'shifts.manage', 'roster.manage', 'deployments.manage', 'handovers.manage', 'relievers.dispatch',
        'attendance.view', 'attendance.manage', 'attendance.override', 'attendance.disputes',
        'patrols.view', 'patrols.manage', 'tasks.manage', 'tasks.execute', 'consumables.manage',
        'audits.view', 'audits.conduct', 'audit.view',
        'incidents.report', 'incidents.manage', 'sos.respond',
        'leave.manage', 'payroll.calculate', 'payroll.approve',
        'billing.invoices', 'sla.monitor', 'reports.view', 'reports.export', 'settings.manage'
    ],
    3 => [
        'staff.view', 'staff.onboard', 'staff.documents', 'leave.manage',
        'payroll.calculate', 'payroll.approve', 'attendance.view', 'attendance.disputes',
        'reports.view', 'reports.export'
    ],
    4 => [
        'clients.view', 'sites.view', 'sites.manage', 'staff.view',
        'shifts.manage', 'roster.manage', 'deployments.manage', 'handovers.manage', 'relievers.dispatch',
        'attendance.view', 'attendance.manage', 'attendance.override', 'attendance.disputes',
        'patrols.view', 'patrols.manage', 'tasks.manage', 'consumables.manage',
        'audits.view', 'audits.conduct', 'audit.view',
        'incidents.report', 'incidents.manage', 'sos.respond',
        'payroll.calculate', 'billing.invoices', 'sla.monitor', 'reports.view', 'reports.export'
    ],
    5 => [
        'sites.view', 'staff.view', 'attendance.view',
        'patrols.view', 'patrols.manage', 'tasks.manage',
        'audits.view', 'audits.conduct', 'audit.view',
        'incidents.report', 'incidents.manage', 'sos.respond',
        'relievers.dispatch', 'roster.manage'
    ],
    6 => [
        'sites.view', 'staff.view', 'attendance.view', 'attendance.manage', 'attendance.disputes',
        'roster.manage', 'handovers.manage', 'patrols.view', 'tasks.manage', 'tasks.execute',
        'consumables.manage', 'incidents.report', 'sos.respond'
    ],
    7 => [
        'patrols.view', 'tasks.execute', 'incidents.report', 'handovers.manage'
    ],
    8 => [
        'tasks.execute', 'consumables.manage', 'incidents.report'
    ],
    9 => [
        'tasks.execute', 'incidents.report'
    ],
    10 => [
        'tasks.execute', 'incidents.report'
    ],
    11 => [
        'payroll.calculate', 'payroll.approve', 'billing.invoices', 'sla.monitor',
        'reports.view', 'reports.export', 'attendance.view', 'clients.view'
    ],
    12 => [
        'client.portal.access', 'sites.view', 'attendance.view', 'patrols.view',
        'audits.view', 'billing.invoices', 'incidents.report', 'reports.view'
    ],
    13 => [
        'client.portal.access', 'sites.view', 'attendance.view', 'reports.view'
    ]
];

$rolePermInsert = $pdo->prepare("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES (?, ?)");
foreach ($roleMappings as $roleId => $permList) {
    foreach ($permList as $permItem) {
        $permId = is_int($permItem) ? $permItem : ($slugToId[$permItem] ?? null);
        if ($permId) {
            $rolePermInsert->execute([$roleId, $permId]);
        }
    }
}

// 6. Seed Employee Categories
echo "[*] Seeding Employee Categories...\n";
$categories = [
    [1, 1, 'SEC-UNARMED', 'Security Guard (Unarmed)', 'security', 'Black Trousers, Navy Blue Shirt, Cap, Lanyard, Combat Boots', 0.00],
    [2, 1, 'SEC-ARMED', 'Armed Security Guard', 'security', 'Safari Suit / Tactical Uniform, Holster, Identity Badge', 150.00],
    [3, 1, 'SEC-LADY', 'Lady Security Guard', 'security', 'Corporate Blue Blazer / Kurti, Security Badge', 50.00],
    [4, 1, 'SEC-SUP', 'Security Supervisor', 'supervision', 'Dark Blue Blazer, Whistle, Radio Holster, Formal Shoes', 100.00],
    [5, 1, 'CLN-COMM', 'Commercial Cleaner', 'cleaning', 'Teal Blue Scrubs / Uniform, Non-slip Shoes, Rubber Gloves', 0.00],
    [6, 1, 'CLN-HOSP', 'Healthcare Sanitization Specialist', 'cleaning', 'Medical Grade Scrubs, N95 Mask, Hairnet, Disposable Gloves', 75.00],
    [7, 1, 'PAN-ATT', 'Pantry & Hospitality Attendant', 'pantry', 'White Shirt, Black Vest, Bow Tie / Apron, Formal Trousers', 0.00],
    [8, 1, 'FAC-TECH', 'Facility & Utility Technician', 'facility', 'Safety Coverall, Hard Hat, Tool Belt, Steel-toe Boots', 50.00],
    [9, 1, 'FLD-OFF', 'Field Operations Officer', 'operations', 'Corporate Formal Attire / Field Jacket, ID Card, Tablet', 250.00]
];

$catStmt = $pdo->prepare("INSERT INTO `employee_categories` (`id`, `company_id`, `code`, `name`, `department`, `standard_uniform`, `daily_allowance_default`) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`)");
foreach ($categories as $c) {
    $catStmt->execute($c);
}

// 7. Seed Sample Administrative & Operations Users
echo "[*] Seeding Admin & Operations Users...\n";
$defaultHash = password_hash('Admin@123456', PASSWORD_BCRYPT);
$opsHash = password_hash('Ops@123456', PASSWORD_BCRYPT);
$hrHash = password_hash('Hr@123456', PASSWORD_BCRYPT);
$fieldHash = password_hash('Field@123456', PASSWORD_BCRYPT);
$superHash = password_hash('Super@123456', PASSWORD_BCRYPT);
$clientHash = password_hash('Client@123456', PASSWORD_BCRYPT);

$users = [
    [1, 1, 1, 1, 'Super Administrator', 'superadmin@jmjenterprisessolutions.com', '+91-9999381777', $defaultHash, 'active'],
    [2, 1, 1, 2, 'Operations Lead', 'admin@jmjenterprisessolutions.com', '+91-9999381778', $defaultHash, 'active'],
    [3, 1, 2, 4, 'Operations Manager Gurgaon', 'ops@jmjenterprisessolutions.com', '+91-9811223344', $opsHash, 'active'],
    [4, 1, 1, 3, 'HR & Compliance Manager', 'hr@jmjenterprisessolutions.com', '+91-9877665544', $hrHash, 'active'],
    [5, 1, 2, 5, 'Field Officer - Rajesh Kumar', 'fieldofficer@jmjenterprisessolutions.com', '+91-9988776655', $fieldHash, 'active'],
    [6, 1, 1, 6, 'Site Supervisor - Vikram Singh', 'supervisor@jmjenterprisessolutions.com', '+91-9766554433', $superHash, 'active'],
    [7, 1, 1, 12, 'Client Admin - ABC Tower', 'client@abccorp.com', '+91-9855443322', $clientHash, 'active']
];

$userStmt = $pdo->prepare("
INSERT INTO `users` (`id`, `company_id`, `branch_id`, `role_id`, `name`, `email`, `phone`, `password_hash`, `status`)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `password_hash` = VALUES(`password_hash`);
");
foreach ($users as $u) {
    $userStmt->execute($u);
}

// 8. Seed Sample Enterprise Clients
echo "[*] Seeding Sample Enterprise Clients...\n";
$clients = [
    [1, 1, 'CLI-ABC-01', 'ABC Corporate Towers Ltd', 'Corporate Real Estate', 'Mr. Amit Sharma (Facility Director)', 'amit.sharma@abccorp.com', '+91-9811001122', 'Plot 14, Barakhamba Road, Connaught Place', 'New Delhi', 'Delhi', '110001', '07AAACA1234A1Z1', 'AAACA1234A', 'monthly', 'active'],
    [2, 1, 'CLI-MAX-02', 'Maxima Super Specialty Hospital', 'Healthcare & Medical', 'Dr. Sunita Rao (COO)', 's.rao@maximahospital.in', '+91-9822334455', 'Sector 56, Golf Course Road', 'Gurgaon', 'Haryana', '122011', '06AAACM5678M1Z2', 'AAACM5678M', 'monthly', 'active'],
    [3, 1, 'CLI-APX-03', 'Apex Logistics & Cold Storage Hub', 'Warehousing & Supply Chain', 'Mr. Rakesh Verma (VP Ops)', 'r.verma@apexlogistics.com', '+91-9833445566', 'Ecotech III, Industrial Area', 'Greater Noida', 'Uttar Pradesh', '201306', '09AAACA9876A1Z3', 'AAACA9876A', 'monthly', 'active']
];

$clientStmt = $pdo->prepare("
INSERT INTO `clients` (`id`, `company_id`, `client_code`, `company_name`, `industry`, `contact_person`, `email`, `phone`, `billing_address`, `city`, `state`, `pincode`, `gst_number`, `pan_number`, `billing_cycle`, `status`)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE `company_name` = VALUES(`company_name`);
");
foreach ($clients as $cl) {
    $clientStmt->execute($cl);
}

// 9. Seed Sample Sites with GPS & Geofences
echo "[*] Seeding Sample Client Sites with Geofences...\n";
$sites = [
    [1, 1, 1, 1, 'SITE-ABC-HQ', 'ABC Corporate Towers - Main Gate & Complex', 'corporate_office', 'Plot 14, Barakhamba Road, Connaught Place', 'New Delhi', 'Delhi', '110001', 28.6304, 77.2270, 'circle', 75, 'Mr. Amit Sharma', '+91-9811001122', '100 / 112', '24/7 Diplomatic Vetting, Fire Door Protocol active', 'active'],
    [2, 1, 2, 2, 'SITE-MAX-MED', 'Maxima Hospital - Trauma & ICU Complex', 'hospital', 'Sector 56, Golf Course Road', 'Gurgaon', 'Haryana', '122011', 28.4320, 77.0980, 'circle', 100, 'Chief Medical Superintendent', '+91-9822334455', '102 / 108', 'Hospital Grade Bio-hazard Sanitization Protocols apply', 'active'],
    [3, 1, 3, 3, 'SITE-APX-WH', 'Apex Logistics Center - Gate 1 & Warehouses', 'warehouse', 'Ecotech III, Industrial Area', 'Greater Noida', 'Uttar Pradesh', '201306', 28.5110, 77.4920, 'circle', 120, 'Gate Marshal Incharge', '+91-9833445566', '112', 'Truck in/out weight verification mandatory', 'active']
];

$siteStmt = $pdo->prepare("
INSERT INTO `sites` (`id`, `company_id`, `client_id`, `branch_id`, `site_code`, `site_name`, `site_type`, `address`, `city`, `state`, `pincode`, `latitude`, `longitude`, `geofence_type`, `geofence_radius`, `contact_person`, `contact_phone`, `emergency_contact`, `instructions`, `status`)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE `site_name` = VALUES(`site_name`), `latitude` = VALUES(`latitude`), `longitude` = VALUES(`longitude`);
");
foreach ($sites as $st) {
    $siteStmt->execute($st);
}

// Seed Site Zones & Checkpoints
echo "[*] Seeding Site Zones & Checkpoints...\n";
$zones = [
    [1, 1, 'ZN-ABC-REC', 'Reception & Front Atrium', 'Ground Floor', 'reception', 'QR-ZONE-ABC-REC'],
    [2, 1, 'ZN-ABC-SRV', 'Data Center & Server Vault', 'Basement 1', 'server_room', 'QR-ZONE-ABC-SRV'],
    [3, 1, 'ZN-ABC-WSH', '2nd Floor Executive Washrooms', '2nd Floor', 'washroom', 'QR-ZONE-ABC-WSH'],
    [4, 1, 'ZN-ABC-PAN', '4th Floor Corporate Pantry', '4th Floor', 'pantry', 'QR-ZONE-ABC-PAN'],
    [5, 2, 'ZN-MAX-ICU', 'ICU & Sterile Surgical Wing', '1st Floor', 'office_floor', 'QR-ZONE-MAX-ICU']
];

$zoneStmt = $pdo->prepare("INSERT INTO `site_zones` (`id`, `site_id`, `zone_code`, `zone_name`, `floor_level`, `zone_type`, `qr_code_token`) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `zone_name` = VALUES(`zone_name`)");
foreach ($zones as $z) {
    $zoneStmt->execute($z);
}

$checkpoints = [
    [1, 1, 1, 'CP-ABC-01', 'Checkpoint A: Main Turnstiles', 'qr', 'JMJ-CP-ABC-A-001', 28.6304, 77.2270, 20],
    [2, 1, 2, 'CP-ABC-02', 'Checkpoint B: Server Vault Perimeter', 'qr', 'JMJ-CP-ABC-B-002', 28.6305, 77.2272, 20],
    [3, 1, NULL, 'CP-ABC-03', 'Checkpoint C: Rear Emergency Fire Stairwell', 'qr', 'JMJ-CP-ABC-C-003', 28.6302, 77.2268, 25],
    [4, 1, NULL, 'CP-ABC-04', 'Checkpoint D: Basement Parking Exit Gate', 'qr', 'JMJ-CP-ABC-D-004', 28.6301, 77.2271, 25]
];

$cpStmt = $pdo->prepare("INSERT INTO `site_checkpoints` (`id`, `site_id`, `zone_id`, `checkpoint_code`, `checkpoint_name`, `checkpoint_type`, `qr_token`, `latitude`, `longitude`, `tolerance_radius`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `checkpoint_name` = VALUES(`checkpoint_name`)");
foreach ($checkpoints as $cp) {
    $cpStmt->execute($cp);
}

// 10. Seed Shift Templates & Site Shifts
echo "[*] Seeding Shift Templates & Site Shifts...\n";
$templates = [
    [1, 1, 'SHT-MORN', 'General Morning Shift', '06:00:00', '14:00:00', 60, 0, 15],
    [2, 1, 'SHT-EVEN', 'General Evening Shift', '14:00:00', '22:00:00', 60, 0, 15],
    [3, 1, 'SHT-NIGHT', 'General Night Shift', '22:00:00', '06:00:00', 60, 1, 15],
    [4, 1, 'SHT-GEN', 'Corporate Office Hours', '09:00:00', '18:00:00', 60, 0, 30]
];

$tmplStmt = $pdo->prepare("INSERT INTO `shift_templates` (`id`, `company_id`, `code`, `name`, `start_time`, `end_time`, `break_duration_mins`, `is_night_shift`, `grace_period_mins`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`)");
foreach ($templates as $t) {
    $tmplStmt->execute($t);
}

$siteShifts = [
    [1, 1, 1, 'ABC Morning Guard Shift', '06:00:00', '14:00:00', 0, 15, 6, 4, 2, 1],
    [2, 1, 2, 'ABC Evening Guard Shift', '14:00:00', '22:00:00', 0, 15, 6, 3, 1, 1],
    [3, 1, 3, 'ABC Night Patrol Shift', '22:00:00', '06:00:00', 1, 15, 5, 1, 0, 1],
    [4, 2, 1, 'Maxima Hospital Morning Shift', '06:00:00', '14:00:00', 0, 15, 8, 8, 2, 1],
    [5, 2, 3, 'Maxima Hospital Night Shift', '22:00:00', '06:00:00', 1, 15, 6, 4, 0, 1]
];

$ssStmt = $pdo->prepare("INSERT INTO `shifts` (`id`, `site_id`, `template_id`, `name`, `start_time`, `end_time`, `is_night_shift`, `grace_period_mins`, `required_guards`, `required_cleaners`, `required_pantry`, `required_supervisors`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`)");
foreach ($siteShifts as $ss) {
    $ssStmt->execute($ss);
}

// 11. Seed Sample Staff & Employees
echo "[*] Seeding Sample Field Staff & Guards...\n";
$employees = [
    [1, 1, 1, 1, 'EMP-00101', 'Ramesh', 'Kumar', 'male', '1992-05-14', '+91-9876543210', '+91-9876543211', 'ramesh.guard@jmjenterprises.com', 'Sant Nagar, East of Kailash', 'Vill. Rampur, Dist. Aligarh, UP', 'New Delhi', 'Delhi', '110065', '2023-01-15', 'full_time', 'Senior Security Guard', 18500.00, 711.50, 4500.00, 1600.00, 1200.00, 'State Bank of India', '30291823901', 'SBIN0001234', '101293849102', '112938491029', 'verified', 'fit', 'active'],
    [2, 1, 1, 2, 'EMP-00102', 'Kuldeep', 'Singh', 'male', '1988-11-20', '+91-9876543212', '+91-9876543213', 'kuldeep.guard@jmjenterprises.com', 'Kalkaji Extn, New Delhi', 'Dist. Rohtak, Haryana', 'New Delhi', 'Delhi', '110019', '2022-06-10', 'full_time', 'Armed Security Officer', 22500.00, 865.00, 5000.00, 1800.00, 1500.00, 'Punjab National Bank', '40192839102', 'PUNB0004321', '101293849103', '112938491030', 'verified', 'fit', 'active'],
    [3, 1, 1, 5, 'EMP-00103', 'Sunita', 'Devi', 'female', '1995-03-12', '+91-9876543214', '+91-9876543215', 'sunita.cleaner@jmjenterprises.com', 'Govindpuri, New Delhi', 'Dist. Patna, Bihar', 'New Delhi', 'Delhi', '110019', '2023-04-01', 'full_time', 'Corporate Facility Cleaner', 16000.00, 615.00, 3500.00, 1200.00, 1000.00, 'Bank of Baroda', '50192839102', 'BARB0005678', '101293849104', '112938491031', 'verified', 'fit', 'active'],
    [4, 1, 1, 7, 'EMP-00104', 'Manoj', 'Sharma', 'male', '1996-08-25', '+91-9876543216', '+91-9876543217', 'manoj.pantry@jmjenterprises.com', 'Lajpat Nagar, New Delhi', 'Dist. Meerut, UP', 'New Delhi', 'Delhi', '110024', '2023-08-15', 'full_time', 'Pantry Attendant', 16500.00, 634.50, 3500.00, 1200.00, 1000.00, 'HDFC Bank', '60192839102', 'HDFC0001122', '101293849105', '112938491032', 'verified', 'fit', 'active'],
    [5, 1, 1, 4, 'EMP-00105', 'Vikram', 'Singh', 'male', '1985-02-18', '+91-9766554433', '+91-9766554434', 'supervisor@jmjenterprisessolutions.com', 'Sant Nagar, East of Kailash', 'Dist. Jhunjhunu, Rajasthan', 'New Delhi', 'Delhi', '110065', '2021-03-01', 'full_time', 'Site Security Supervisor', 26000.00, 1000.00, 6000.00, 2000.00, 2000.00, 'State Bank of India', '30291823999', 'SBIN0001234', '101293849106', '112938491033', 'verified', 'fit', 'active']
];

$empStmt = $pdo->prepare("
INSERT INTO `employees` (`id`, `company_id`, `branch_id`, `category_id`, `employee_code`, `first_name`, `last_name`, `gender`, `dob`, `phone`, `emergency_phone`, `email`, `current_address`, `permanent_address`, `city`, `state`, `pincode`, `joining_date`, `employment_type`, `designation`, `basic_salary`, `daily_rate`, `hra_allowance`, `conveyance_allowance`, `special_allowance`, `bank_name`, `bank_account_no`, `ifsc_code`, `pf_uan`, `esic_no`, `police_verification_status`, `medical_fitness_status`, `status`)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE `first_name` = VALUES(`first_name`), `last_name` = VALUES(`last_name`);
");
foreach ($employees as $emp) {
    $empStmt->execute($emp);
}

// Link employee user accounts for mobile login
$guardHash = password_hash('Guard@123456', PASSWORD_BCRYPT);
$cleanHash = password_hash('Clean@123456', PASSWORD_BCRYPT);

$pdo->exec("
INSERT INTO `users` (`id`, `company_id`, `branch_id`, `role_id`, `employee_id`, `name`, `email`, `phone`, `password_hash`, `status`)
VALUES
(8, 1, 1, 7, 1, 'Ramesh Kumar (Guard)', 'guard@jmjenterprises.com', '+91-9876543210', '{$guardHash}', 'active'),
(9, 1, 1, 8, 3, 'Sunita Devi (Cleaning)', 'cleaning@jmjenterprises.com', '+91-9876543214', '{$cleanHash}', 'active')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
");

// 12. Seed Patrol Route
echo "[*] Seeding Patrol Route & Sequences...\n";
$pdo->exec("
INSERT INTO `patrol_routes` (`id`, `site_id`, `name`, `description`, `estimated_minutes`, `status`)
VALUES
(1, 1, 'ABC Corporate Perimeter & Server Tour', 'Standard 4-checkpoint security patrol covering Atrium, Server Room, Rear Fire Door, and Basement Parking', 30, 'active')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
");

$pdo->exec("
INSERT INTO `patrol_route_checkpoints` (`id`, `route_id`, `checkpoint_id`, `sequence_order`, `expected_interval_mins`, `is_mandatory`)
VALUES
(1, 1, 1, 1, 0, 1),
(2, 1, 2, 2, 10, 1),
(3, 1, 3, 3, 20, 1),
(4, 1, 4, 4, 30, 1)
ON DUPLICATE KEY UPDATE `sequence_order` = VALUES(`sequence_order`);
");

// 13. Seed Cleaning Task Templates
echo "[*] Seeding Cleaning Checklists & Inventory...\n";
$washroomTasks = json_encode([
    'Floor sanitization and wet mopping with Taski R2',
    'Mirror glass cleaning and smudge removal with Taski R3',
    'WC and urinal bowl disinfection with Taski R6',
    'Consumable refill: Hand soap, paper towels, and tissue rolls',
    'Dustbin emptying and sanitization spray'
]);

$receptionTasks = json_encode([
    'Atrium floor sweeping and automatic scrubber operation',
    'Glass facade and entrance door dusting',
    'Reception counter and visitor register sanitization',
    'Indoor plant dusting and floor mat vacuuming'
]);

$pdo->exec("
INSERT INTO `task_templates` (`id`, `company_id`, `title`, `department`, `frequency`, `items_checklist`, `requires_qr_scan`, `requires_photo_evidence`)
VALUES
(1, 1, 'Executive Washroom Sanitization Standard', 'cleaning', 'hourly', '{$washroomTasks}', 1, 1),
(2, 1, 'Corporate Reception & Atrium Upkeep', 'cleaning', 'per_shift', '{$receptionTasks}', 1, 1)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);
");

$pdo->exec("
INSERT INTO `consumable_inventory` (`id`, `company_id`, `site_id`, `item_name`, `category`, `unit`, `current_stock`, `min_alert_level`)
VALUES
(1, 1, 1, 'Taski R2 All-Purpose Floor Cleaner', 'chemicals', 'Litres', 45.00, 10.00),
(2, 1, 1, 'Taski R3 Glass Cleaner Spray', 'chemicals', 'Litres', 20.00, 5.00),
(3, 1, 1, 'Premium Liquid Hand Soap', 'sanitizers', 'Litres', 35.00, 10.00),
(4, 1, 1, 'Commercial 2-Ply Toilet Paper Rolls', 'paper_products', 'Rolls', 120.00, 30.00),
(5, 1, 1, 'Heavy Duty Microfiber Wet Mops', 'tools', 'Pcs', 18.00, 5.00)
ON DUPLICATE KEY UPDATE `item_name` = VALUES(`item_name`);
");

// 14. Seed Leave Types & Settings
echo "[*] Seeding Leave Types & Global Settings...\n";
$pdo->exec("
INSERT INTO `leave_types` (`id`, `company_id`, `name`, `code`, `annual_quota_days`, `is_paid`)
VALUES
(1, 1, 'Casual Leave', 'CL', 12, 1),
(2, 1, 'Sick / Medical Leave', 'SL', 10, 1),
(3, 1, 'Earned Privilege Leave', 'EL', 15, 1),
(4, 1, 'Emergency Unpaid Leave', 'EUL', 30, 0)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
");

$settings = [
    ['general', 'app_name', 'JMJ Workforce & Field Operations Hub'],
    ['general', 'support_phone', '+91-9999381777'],
    ['general', 'toll_free', '18008890832'],
    ['attendance', 'default_geofence_radius', '75'],
    ['attendance', 'qr_token_lifetime_seconds', '30'],
    ['attendance', 'selfie_required_default', '1'],
    ['attendance', 'face_verification_enabled', '0'],
    ['attendance', 'face_data_retention_days', '90'],
    ['attendance', 'no_show_grace_minutes', '30'],
    ['patrol', 'patrol_tolerance_minutes', '15'],
    ['payroll', 'pf_employee_percent', '12.0'],
    ['payroll', 'esic_employee_percent', '0.75'],
    ['payroll', 'standard_month_days', '26']
];

$setStmt = $pdo->prepare("INSERT INTO `settings` (`company_id`, `setting_group`, `key_name`, `key_value`, `field_type`) VALUES (1, ?, ?, ?, 'text') ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`)");
foreach ($settings as $s) {
    $setStmt->execute($s);
}

// 15. Seed Deployments & Today's Roster for End-to-End Testing
$today = date('Y-m-d');
echo "[*] Seeding Active Deployments & Today's Shift Roster ({$today})...\n";

$pdo->exec("
INSERT INTO `employee_deployments` (`id`, `company_id`, `employee_id`, `site_id`, `shift_id`, `start_date`, `end_date`, `assigned_role`, `status`)
VALUES
(1, 1, 1, 1, 1, '{$today}', NULL, 'Senior Security Guard', 'active'),
(2, 1, 2, 1, 3, '{$today}', NULL, 'Armed Security Guard (Night)', 'active'),
(3, 1, 3, 1, 1, '{$today}', NULL, 'Facility Cleaner', 'active'),
(4, 1, 4, 1, 1, '{$today}', NULL, 'Pantry Attendant', 'active')
ON DUPLICATE KEY UPDATE `assigned_role` = VALUES(`assigned_role`);
");

$pdo->exec("
INSERT INTO `shift_rosters` (`id`, `company_id`, `site_id`, `shift_id`, `employee_id`, `roster_date`, `is_reliever`, `status`)
VALUES
(1, 1, 1, 1, 1, '{$today}', 0, 'scheduled'),
(2, 1, 1, 3, 2, '{$today}', 0, 'scheduled'),
(3, 1, 1, 1, 3, '{$today}', 0, 'scheduled'),
(4, 1, 1, 1, 4, '{$today}', 0, 'scheduled')
ON DUPLICATE KEY UPDATE `status` = VALUES(`status`);
");

echo "\n===============================================================\n";
echo "  [✓] WORKFORCE DATABASE INITIALIZATION COMPLETED SUCCESSFULLY!  \n";
echo "===============================================================\n";
echo "  Database: jmj_workforce_db on port {$connectedPort}\n";
echo "  Super Admin: superadmin@jmjenterprisessolutions.com / Admin@123456\n";
echo "  Operations:  ops@jmjenterprisessolutions.com / Ops@123456\n";
echo "  Guard Login: guard@jmjenterprises.com / Guard@123456\n";
echo "===============================================================\n";
