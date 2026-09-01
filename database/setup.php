<?php
/**
 * JMJ Enterprises Solutions - Database Schema & Comprehensive Seed Runner
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

echo "=== JMJ Enterprises Solutions: Database Setup & Seeding ===" . PHP_EOL;

try {
    $host = (string)env('DB_HOST', '127.0.0.1');
    $port = (int)env('DB_PORT', 3308);
    $dbname = (string)env('DB_DATABASE', 'jmj_enterprise_db');
    $user = (string)env('DB_USERNAME', 'root');
    $pass = (string)env('DB_PASSWORD', '');

    $pdoOptions = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    // Try connecting to MySQL server
    $serverPdo = null;
    $ports = [$port, 3306, 3307];
    foreach ($ports as $p) {
        try {
            $serverPdo = new PDO("mysql:host={$host};port={$p};charset=utf8mb4", $user, $pass, $pdoOptions);
            echo "Connected to MySQL server on port {$p}." . PHP_EOL;
            break;
        } catch (PDOException) {
            continue;
        }
    }

    if (!$serverPdo) {
        throw new Exception("Unable to connect to MySQL server on ports " . implode(', ', $ports));
    }

    // Create database if not exists
    $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database `{$dbname}` verified." . PHP_EOL;

    // Use database
    $serverPdo->exec("USE `{$dbname}`");

    // Execute schema.sql statement by statement
    $schemaFile = __DIR__ . '/schema.sql';
    if (file_exists($schemaFile)) {
        $schemaSql = file_get_contents($schemaFile);
        $queries = array_filter(array_map('trim', explode(';', $schemaSql)));
        foreach ($queries as $q) {
            if (!empty($q)) {
                $serverPdo->exec($q);
            }
        }
        echo "Schema tables created successfully." . PHP_EOL;
    }

    $db = \Core\Database::getInstance();

    // 1. Seed Roles
    echo "Seeding Roles & Permissions..." . PHP_EOL;
    $roles = [
        ['name' => 'super_admin', 'label' => 'Super Administrator', 'description' => 'Full administrative access to all modules and configurations.'],
        ['name' => 'admin', 'label' => 'Administrator', 'description' => 'Manage services, blogs, media, enquiries, FAQs, and testimonials.'],
        ['name' => 'editor', 'label' => 'Content Editor', 'description' => 'Create, edit, review, and schedule blog articles and service pages.'],
        ['name' => 'author', 'label' => 'Staff Author', 'description' => 'Draft blog posts and view content.'],
    ];
    foreach ($roles as $r) {
        $db->query(
            "INSERT INTO `roles` (`name`, `label`, `description`) 
             VALUES (:name, :label, :description) 
             ON DUPLICATE KEY UPDATE `label` = VALUES(`label`), `description` = VALUES(`description`)",
            $r
        );
    }

    // 2. Seed Permissions
    $permissions = [
        ['slug' => 'posts.view', 'label' => 'View Blog Posts', 'group_name' => 'Blog'],
        ['slug' => 'posts.create', 'label' => 'Create Blog Posts', 'group_name' => 'Blog'],
        ['slug' => 'posts.edit', 'label' => 'Edit Blog Posts', 'group_name' => 'Blog'],
        ['slug' => 'posts.publish', 'label' => 'Publish Blog Posts', 'group_name' => 'Blog'],
        ['slug' => 'posts.delete', 'label' => 'Delete / Archive Posts', 'group_name' => 'Blog'],
        ['slug' => 'services.manage', 'label' => 'Manage Services', 'group_name' => 'Services'],
        ['slug' => 'categories.manage', 'label' => 'Manage Taxonomies', 'group_name' => 'Taxonomies'],
        ['slug' => 'media.manage', 'label' => 'Manage Media Library', 'group_name' => 'Media'],
        ['slug' => 'leads.manage', 'label' => 'Manage Enquiries & Quotes', 'group_name' => 'CRM'],
        ['slug' => 'users.manage', 'label' => 'Manage Admin Users', 'group_name' => 'Security'],
        ['slug' => 'settings.manage', 'label' => 'Manage Global Settings', 'group_name' => 'Settings'],
        ['slug' => 'seo.manage', 'label' => 'Manage SEO Settings', 'group_name' => 'SEO'],
        ['slug' => 'audit.view', 'label' => 'View Security Audit Logs', 'group_name' => 'Security'],
    ];
    foreach ($permissions as $p) {
        $db->query(
            "INSERT INTO `permissions` (`slug`, `label`, `group_name`) 
             VALUES (:slug, :label, :group_name) 
             ON DUPLICATE KEY UPDATE `label` = VALUES(`label`), `group_name` = VALUES(`group_name`)",
            $p
        );
    }

    // Assign Permissions to Roles
    $superAdminRoleId = (int)$db->fetchColumn("SELECT id FROM roles WHERE name = 'super_admin'");
    $adminRoleId = (int)$db->fetchColumn("SELECT id FROM roles WHERE name = 'admin'");
    $editorRoleId = (int)$db->fetchColumn("SELECT id FROM roles WHERE name = 'editor'");

    $allPermIds = $db->fetchAll("SELECT id FROM permissions");
    foreach ($allPermIds as $p) {
        $db->query("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES (:r, :p)", ['r' => $superAdminRoleId, 'p' => $p['id']]);
        $db->query("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES (:r, :p)", ['r' => $adminRoleId, 'p' => $p['id']]);
    }

    $editorPerms = ['posts.view', 'posts.create', 'posts.edit', 'posts.publish', 'media.manage', 'categories.manage'];
    foreach ($editorPerms as $slug) {
        $pid = $db->fetchColumn("SELECT id FROM permissions WHERE slug = :s", ['s' => $slug]);
        if ($pid) {
            $db->query("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES (:r, :p)", ['r' => $editorRoleId, 'p' => $pid]);
        }
    }

    // 3. Seed Users
    echo "Seeding Admin Users..." . PHP_EOL;
    $adminPassHash = password_hash('Admin@123456', PASSWORD_DEFAULT);
    $editorPassHash = password_hash('Editor@123456', PASSWORD_DEFAULT);

    $db->query(
        "INSERT INTO `users` (`role_id`, `name`, `email`, `password_hash`, `status`) 
         VALUES (:role_id, :name, :email, :hash, 'active') 
         ON DUPLICATE KEY UPDATE `password_hash` = VALUES(`password_hash`), `role_id` = VALUES(`role_id`)",
        [
            'role_id' => $superAdminRoleId,
            'name' => 'JMJ Super Administrator',
            'email' => 'admin@jmjenterprises.com',
            'hash' => $adminPassHash
        ]
    );

    $db->query(
        "INSERT INTO `users` (`role_id`, `name`, `email`, `password_hash`, `status`) 
         VALUES (:role_id, :name, :email, :hash, 'active') 
         ON DUPLICATE KEY UPDATE `password_hash` = VALUES(`password_hash`), `role_id` = VALUES(`role_id`)",
        [
            'role_id' => $editorRoleId,
            'name' => 'Content Editorial Team',
            'email' => 'editor@jmjenterprises.com',
            'hash' => $editorPassHash
        ]
    );

    $adminUserId = (int)$db->fetchColumn("SELECT id FROM users WHERE email = 'admin@jmjenterprises.com'");

    // 4. Seed Service Categories
    echo "Seeding Service Categories..." . PHP_EOL;
    $serviceCats = [
        ['name' => 'Security Services', 'slug' => 'security-services', 'icon' => 'fas fa-shield-halved', 'short_description' => 'Manned guarding, executive protection, ATM logistics, institutional and tactical security solutions across India.', 'display_order' => 1],
        ['name' => 'Cleaning Services', 'slug' => 'cleaning-services', 'icon' => 'fas fa-sparkles', 'short_description' => 'Hospital-grade sanitization, industrial floor waxing, commercial housekeeping, and deep restorative cleaning services.', 'display_order' => 2],
        ['name' => 'Facility Management', 'slug' => 'facility-management', 'icon' => 'fas fa-building-user', 'short_description' => 'Integrated corporate workplace support, pantry management, and building maintenance rosters.', 'display_order' => 3]
    ];
    foreach ($serviceCats as $sc) {
        $db->query(
            "INSERT INTO `service_categories` (`name`, `slug`, `icon`, `short_description`, `display_order`, `status`) 
             VALUES (:name, :slug, :icon, :short_description, :display_order, 'active') 
             ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `icon` = VALUES(`icon`), `short_description` = VALUES(`short_description`)",
            $sc
        );
    }

    $secCatId = (int)$db->fetchColumn("SELECT id FROM service_categories WHERE slug = 'security-services'");
    $cleanCatId = (int)$db->fetchColumn("SELECT id FROM service_categories WHERE slug = 'cleaning-services'");

    // 5. Seed 12 Security Services
    echo "Seeding 12 Manned Security Services..." . PHP_EOL;
    $securityServicesData = [
        [
            'name' => 'Corporate Offices Security Guard Services',
            'slug' => 'corporate-security',
            'icon' => 'fas fa-building',
            'short_summary' => 'Professional front-desk concierges, access control gate personnel, and perimeter patrol units for commercial hubs and IT parks.',
            'overview' => '<p>JMJ Enterprises Solutions delivers elite corporate security services tailored for multinational office towers, corporate campuses, IT/ITeS centers, and business parks. Our personnel are trained not only in rigorous access-control enforcement and badge verification but also in executive customer hospitality.</p><p>We integrate manned physical guarding with electronic visitor management software, optical turnstiles, structural perimeter surveillance, and emergency fire evacuation protocols to ensure total business continuity.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'IT Parks, Financial Centers, Headquarters, Shared Workspaces, Commercial Towers',
            'methodology' => '<ol><li><strong>Perimeter & Access Point Assessment:</strong> Analyzing pedestrian turnstiles, loading docks, and underground parking entryways.</li><li><strong>Bilingual Officer Profiling:</strong> Deploying certified guards equipped with communication radios and emergency response training.</li><li><strong>Automated Visitor Verification:</strong> Fast digital sign-in, material in-out gate pass auditing, and CCTV monitoring.</li><li><strong>Periodic Security Drills:</strong> Evacuation coordination and unannounced night patrol inspections.</li></ol>',
            'compliance' => 'PSARA Licensed, ISO 9001:2015, ESI/EPF Compliant, Verified Police Background Checks',
            'meta_title' => 'Corporate Office Security Guard Services in Delhi NCR | JMJ Enterprises',
            'meta_description' => 'Premier corporate office security guard services in Delhi, Gurgaon, Noida & Bangalore. Trained manned guarding, visitor access management & 24/7 patrol.',
            'features' => [
                ['title' => 'Access Control & Turnstile Management', 'description' => 'Strict verification of corporate IDs, biometric checkpoints, and visitor tracking.'],
                ['title' => 'Executive Front-Desk Concierge', 'description' => 'Well-groomed, multilingual security staff reflecting high corporate professionalism.'],
                ['title' => 'Parking & Traffic Logistics', 'description' => 'Orderly multi-level basement parking flow, speed monitoring, and vehicle inspection.'],
                ['title' => 'Fire Safety & Crisis Evacuation', 'description' => 'First-responder certified guards capable of managing emergencies and evacuation.'],
            ],
            'faqs' => [
                ['question' => 'Are your security guards PSARA certified?', 'answer' => 'Yes, 100% of our security personnel are trained and compliant with the Private Security Agencies (Regulation) Act (PSARA) with rigorous police background verification.'],
                ['question' => 'Can you provide 24/7 rotating shifts for corporate campuses?', 'answer' => 'Yes, we provide 8-hour and 12-hour structured shift rotations complete with site supervisors and unannounced mobile inspection marshals.']
            ]
        ],
        [
            'name' => 'ATM Security Guard Services',
            'slug' => 'atm-security',
            'icon' => 'fas fa-credit-card',
            'short_summary' => 'Vigilant, combat-ready security personnel protecting ATM kiosks, cash depository locations, and off-site teller points.',
            'overview' => '<p>Automated Teller Machines (ATMs) represent high-vulnerability financial assets requiring vigilant on-site presence. JMJ Enterprises Solutions provides dedicated, physically fit, and alert ATM security officers drilled in anti-tampering surveillance, customer safety, and cash replenishment escort protocols.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1501167786227-4cba60f6d58f?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Retail Banks, Non-Banking Financial Kiosks, Cash Replenishment Hubs',
            'methodology' => '<p>Guards maintain continuous visual line-of-sight, verify non-loitering rules inside kiosk cabins, monitor skimming devices, and maintain immediate panic-button contact with bank dispatch centers.</p>',
            'compliance' => 'RBI Security Guidelines Compliant, PSARA Registered',
            'meta_title' => 'ATM Security Guard Services Delhi NCR & Pan-India | JMJ Enterprises',
            'meta_description' => 'Reliable 24/7 ATM security guard services for banks and financial institutions. Anti-vandalism, cash replenishment safety & emergency response.',
            'features' => [
                ['title' => '24/7 Vigilant Presence', 'description' => 'Uninterrupted booth monitoring with night shift alertness checks.'],
                ['title' => 'Skimming & Tampering Prevention', 'description' => 'Routine physical checks of card slots, keypad covers, and camera angles.'],
                ['title' => 'Immediate Alarm Dispatch Link', 'description' => 'Direct escalation matrix connected with local police control rooms and bank coordinators.']
            ],
            'faqs' => [
                ['question' => 'How do you monitor night-time alertness at ATMs?', 'answer' => 'We deploy geofenced hourly check-ins via mobile devices and unannounced mobile patrol checks throughout the night.']
            ]
        ],
        [
            'name' => 'Lady Security Officers',
            'slug' => 'lady-security-officers',
            'icon' => 'fas fa-user-shield',
            'short_summary' => 'Elite female security professionals for corporate facilities, embassies, luxury hotels, educational institutes, and VIP events.',
            'overview' => '<p>JMJ Enterprises Solutions maintains a dedicated division of highly trained Lady Security Officers (LSOs) specializing in female visitor frisking, corporate access control, hospital maternity wards, women dormitories, and executive hospitality security.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Embassies, IT Companies, Luxury Retail, Girls Hostels, Hospitals, VIP Events',
            'methodology' => '<p>LSOs undergo training in unarmed combat, polite communication, metal detector screening, emergency medical response, and crisis de-escalation.</p>',
            'compliance' => 'PSARA Certified, Gender Equality & Diversity Compliant',
            'meta_title' => 'Lady Security Officers & Female Guard Services in Delhi NCR | JMJ',
            'meta_description' => 'Hire trained Lady Security Officers for corporate offices, embassies, hotels, retail and hospitals across Delhi NCR and India.',
            'features' => [
                ['title' => 'Discreet Female Frisking', 'description' => 'Courteous and thorough screening using handheld and door-frame metal detectors.'],
                ['title' => 'High Customer-Interface Tact', 'description' => 'Polite communication combined with firm enforcement of facility regulations.'],
                ['title' => 'Hostel & Maternity Ward Security', 'description' => 'Specialized posting for sensitive healthcare and institutional environments.']
            ],
            'faqs' => [
                ['question' => 'What training do Lady Security Officers receive?', 'answer' => 'They undergo 21 days of intensive PSARA module training covering access control, firefighting, physical fitness, and first aid.']
            ]
        ],
        [
            'name' => 'Security Guard Company in Delhi',
            'slug' => 'delhi-security-guard',
            'icon' => 'fas fa-city',
            'short_summary' => 'Top-rated, PSARA-licensed security guard agency serving Delhi NCR, South Delhi, Central Delhi, and industrial belts.',
            'overview' => '<p>As a premier security guard company headquartered in New Delhi (Sant Nagar, East of Kailash), JMJ Enterprises Solutions has spent over a decade securing Delhi NCR corporate, industrial, and diplomatic infrastructure.</p>',
            'hero_image' => 'img/security.JPG',
            'target_sectors' => 'Delhi NCR Corporate, Commercial Complexes, Embassies, Residential Colonies',
            'methodology' => '<p>Localized command, rapid reserve deployment teams, round-the-clock patrol jeeps, and direct integration with Delhi Police emergency channels.</p>',
            'compliance' => 'Delhi PSARA License No. Verified, Central Labor Law Compliant',
            'meta_title' => 'Best Security Guard Company in Delhi NCR | Manned Security Services',
            'meta_description' => 'Leading PSARA-licensed security guard company in Delhi. Reliable corporate, industrial, and residential security guards across Delhi, Gurgaon & Noida.',
            'features' => [
                ['title' => 'Local Dispatch Command', 'description' => 'Headquartered in New Delhi for immediate operational responsiveness.'],
                ['title' => 'Rapid Replacement Guarantee', 'description' => 'Standby guard reserves ensure zero vacant posts.'],
                ['title' => 'Rigorous Background Vetting', 'description' => 'Aadhaar, address, and criminal record verification for every guard.']
            ],
            'faqs' => [
                ['question' => 'How quickly can guards be deployed in Delhi NCR?', 'answer' => 'We can deploy vetted security personnel within 24 to 48 hours following a site assessment.']
            ]
        ],
        [
            'name' => 'Security Guard Services for Embassies',
            'slug' => 'embassy-security',
            'icon' => 'fas fa-flag',
            'short_summary' => 'High-protocol diplomatic security details, access filtering, and perimeter reinforcement for foreign missions and consulates.',
            'overview' => '<p>Diplomatic missions and consular offices demand zero-tolerance security protocols. Our embassy security personnel are trained in international diplomatic courtesies, vehicle anti-sabotage screening, perimeter defense, and rapid liaison with state security agencies.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Embassies, High Commissions, Diplomatic Residences, Consular Offices',
            'methodology' => '<p>Under-vehicle scanning mirrors, explosive trace detection protocol coordination, biometric visitor validation, and armed perimeter backups.</p>',
            'compliance' => 'Diplomatic Protocol Vetted, PSARA Compliant',
            'meta_title' => 'Embassy Security Guard Services Delhi | Diplomatic Mission Protection',
            'meta_description' => 'Specialized security guard services for foreign embassies, consulates and diplomatic residences in Delhi. Protocol-trained personnel.',
            'features' => [
                ['title' => 'Anti-Sabotage Vehicle Screening', 'description' => 'Under-chassis and trunk inspection with detection mirrors and mirrors.'],
                ['title' => 'Diplomatic Protocol Courtesy', 'description' => 'Impeccably groomed personnel adhering to international mission etiquette.']
            ],
            'faqs' => [
                ['question' => 'Do guards speak English?', 'answer' => 'Yes, our embassy security guards and supervisors are trained in functional English communication.']
            ]
        ],
        [
            'name' => 'Industrial Security Guard Services',
            'slug' => 'industrial-security',
            'icon' => 'fas fa-industry',
            'short_summary' => 'Heavy industrial plant security, raw material weighbridge monitoring, loading bay management, and perimeter safeguarding.',
            'overview' => '<p>Manufacturing plants, industrial parks, and logistics warehouses present complex security challenges involving labor relations, theft prevention, raw material pilferage, and heavy vehicle tracking. JMJ provides rugged, disciplined industrial security personnel equipped to enforce industrial discipline.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Manufacturing Units, Warehouses, Automobile Plants, Chemical Plants',
            'methodology' => '<p>Inward/outward material challan verification, gross/tare weighbridge auditing, employee physical search at shift transitions, and nighttime fence patrols.</p>',
            'compliance' => 'Factories Act Compliant, PSARA Certified, Safety Equipment (PPE) Trained',
            'meta_title' => 'Industrial Security Guard Services in Delhi NCR, Manesar, Noida | JMJ',
            'meta_description' => 'Comprehensive industrial security services for factories, plants, and warehouses. Raw material tracking, weighbridge audit & labor dispute prevention.',
            'features' => [
                ['title' => 'Material Gate Pass Auditing', 'description' => 'Zero-discrepancy verification of invoice, challan, and physical goods.'],
                ['title' => 'Weighbridge & Truck In-Out Control', 'description' => 'Systematic logging of vehicle weight and driver credentials.'],
                ['title' => 'Shift Change Frisking', 'description' => 'Systematic tool, asset, and component theft prevention.']
            ],
            'faqs' => [
                ['question' => 'Can guards handle labor strike situations?', 'answer' => 'Our industrial supervisors and guards are trained in lawful de-escalation and perimeter defense during industrial unrest.']
            ]
        ],
        [
            'name' => 'Hotels Security Guards Services',
            'slug' => 'hotel-security',
            'icon' => 'fas fa-hotel',
            'short_summary' => 'Hospitality-focused security officers balancing discrete guest safety with proactive surveillance for 5-star and boutique hotels.',
            'overview' => '<p>Hotels require a sophisticated security approach that protects guest privacy, assets, and safety without feeling restrictive. JMJ provides courteous, sharp security personnel trained in luxury hospitality standards.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => '5-Star Hotels, Luxury Resorts, Boutique Properties, Banquet Venues',
            'methodology' => '<p>Luggage scanner screening, valet parking control, banquet crowd control, floor patrols, and keycard verification.</p>',
            'compliance' => 'Hospitality Standards Compliant, PSARA Certified',
            'meta_title' => 'Hotel Security Guard Services in Delhi, Gurgaon & Pan-India | JMJ',
            'meta_description' => 'Premium hotel security services for luxury resorts, hotels, and banquet facilities. Guest hospitality and 24/7 discreet protection.',
            'features' => [
                ['title' => 'Luggage X-Ray & Screening', 'description' => 'Polite screening of guest baggage at hotel entry points.'],
                ['title' => 'Banquet & Event Security', 'description' => 'Managing large guest volumes, VIP arrivals, and parking flow.']
            ],
            'faqs' => [
                ['question' => 'How do guards handle unruly guests?', 'answer' => 'Personnel use polite conflict de-escalation techniques to resolve issues quietly without disturbing other patrons.']
            ]
        ],
        [
            'name' => 'Security Guards for Educational Institutions, Schools and Colleges',
            'slug' => 'educational-security',
            'icon' => 'fas fa-graduation-cap',
            'short_summary' => 'Child safety certified, compassionate security personnel for universities, international schools, and college campuses.',
            'overview' => '<p>The safety of students and faculty is paramount. JMJ Enterprises Solutions provides thoroughly background-vetted guards specializing in school bus pickup/drop-off monitoring, anti-bullying perimeter patrols, visitor credential verification, and stranger danger protocols.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'K-12 International Schools, Universities, Colleges, Coaching Hubs',
            'methodology' => '<p>Strict parent ID verification at gates, student dispersal supervision, dormitory access control, and CCTV monitoring.</p>',
            'compliance' => 'POCSO Awareness Vetted, PSARA Certified',
            'meta_title' => 'School & College Security Guard Services Delhi NCR | JMJ Enterprises',
            'meta_description' => 'Trusted security guard services for schools, colleges, and university campuses. Child safety vetted, visitor management & gate control.',
            'features' => [
                ['title' => 'School Bus Logistics & Dispersal', 'description' => 'Ensuring students board the correct buses safely.'],
                ['title' => 'Strict Parent Authorization Checks', 'description' => 'No unauthorized person is permitted onto school grounds without verification.']
            ],
            'faqs' => [
                ['question' => 'Are guards police-verified for child safety?', 'answer' => 'Yes, every guard deployed in an educational institution undergoes mandatory enhanced police and identity verification.']
            ]
        ],
        [
            'name' => 'Security Guard Services for Hospitals',
            'slug' => 'hospital-security',
            'icon' => 'fas fa-hospital',
            'short_summary' => 'Crisis-trained emergency room guards, ICU access managers, and hospital crowd controllers protecting medical staff.',
            'overview' => '<p>Healthcare environments demand high emotional intelligence combined with firm security. Our hospital security teams protect doctors and nurses from emergency room violence, manage crowded OPD zones, control ICU visiting hours, and prevent newborn abduction.</p>',
            'hero_image' => 'img/hospital.JPG',
            'target_sectors' => 'Multi-Specialty Hospitals, Medical Colleges, Trauma Centers, Clinics',
            'methodology' => '<p>24/7 Emergency Room triaged guarding, attendant pass validation, ambulance bay traffic clearance, and pharmacy asset protection.</p>',
            'compliance' => 'NABH Security Standard Compliant, PSARA Certified',
            'meta_title' => 'Hospital Security Guard Services in Delhi NCR | Healthcare Safety',
            'meta_description' => 'Professional hospital security guard services. Emergency room safety, doctor protection, ICU crowd control & ambulance bay management.',
            'features' => [
                ['title' => 'Emergency Room Violence Prevention', 'description' => 'Fast intervention to protect medical staff during high-stress crises.'],
                ['title' => 'Maternity & Neonatal Ward Security', 'description' => 'Strict infant safety protocols and mother-baby tag verification.']
            ],
            'faqs' => [
                ['question' => 'Can your staff handle doctor-patient conflict situations?', 'answer' => 'Yes, our hospital guards are trained in healthcare crisis management and verbal de-escalation.']
            ]
        ],
        [
            'name' => 'Security Guards for Residences and Homes in Delhi',
            'slug' => 'residential-security',
            'icon' => 'fas fa-house-chimney-user',
            'short_summary' => 'Trustworthy residential society guards, gatekeepers, and private villa security personnel across Delhi NCR.',
            'overview' => '<p>Protect your family and gated community with JMJ’s residential security force. We manage resident association gates, delivery personnel verification, domestic help registry, and overnight perimeter patrols.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Gated Communities, Luxury Villas, Apartment Complexes, Farmhouses',
            'methodology' => '<p>Visitor intercom verification, courier pass logging, nocturnal boundary fence checks, and swift emergency medical assistance.</p>',
            'compliance' => 'PSARA Certified, Police Verified',
            'meta_title' => 'Residential Security Guard Services in Delhi, Gurgaon, Noida | JMJ',
            'meta_description' => 'Reliable residential security guards for gated societies, apartments, and private homes in Delhi NCR. Delivery validation & 24/7 patrols.',
            'features' => [
                ['title' => 'Visitor & Delivery App Coordination', 'description' => 'Seamless integration with MyGate, NoBrokerHood, and manual registers.'],
                ['title' => 'Overnight Guard Patrolling', 'description' => 'Torch-lit patrols with checkpoint baton scans.']
            ],
            'faqs' => [
                ['question' => 'Do you provide security for individual villas?', 'answer' => 'Yes, we provide 24-hour armed and unarmed residential guards for private villas and farmhouses.']
            ]
        ],
        [
            'name' => 'Security Guards for Multinational Companies in Delhi, India',
            'slug' => 'mnc-security',
            'icon' => 'fas fa-globe',
            'short_summary' => 'Global-standard enterprise security solutions for Fortune 500 companies and multinational tech campuses.',
            'overview' => '<p>Multinational companies require global compliance, stringent environmental health & safety (EHS) alignment, and modern data security measures. JMJ provides tech-enabled, highly disciplined personnel matching global corporate mandates.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Fortune 500 Campuses, MNC R&D Centers, Global Capability Centers (GCCs)',
            'methodology' => '<p>NDA-bonded guards, clean desk policy enforcement, server room dual-custody access, and real-time incident reporting dashboards.</p>',
            'compliance' => 'ISO 27001 Physical Security Compliant, PSARA Licensed',
            'meta_title' => 'MNC Security Guard Services Delhi NCR & Bangalore | JMJ Enterprises',
            'meta_description' => 'Enterprise security guard services for multinational corporations and Fortune 500 hubs in Delhi, Gurgaon, Noida & Bangalore.',
            'features' => [
                ['title' => 'Server Room & Data Center Control', 'description' => 'Dual authorization access logging and anti-piggybacking enforcement.'],
                ['title' => 'EHS & Compliance Auditing', 'description' => 'Adherence to global health, safety, and security compliance matrices.']
            ],
            'faqs' => [
                ['question' => 'Do you sign NDAs for sensitive corporate campuses?', 'answer' => 'Yes, all our deployment contracts and deployed personnel adhere to comprehensive Non-Disclosure Agreements.']
            ]
        ],
        [
            'name' => 'CCTV Digital Solutions',
            'slug' => 'cctv-security',
            'icon' => 'fas fa-video',
            'short_summary' => 'Integrated IP surveillance cameras, AI video analytics, control room monitoring, and biometric perimeter access.',
            'overview' => '<p>Physical security is exponentially more effective when backed by intelligent technology. JMJ designs, installs, and manages state-of-the-art CCTV surveillance networks, AI facial recognition, ANPR vehicle cameras, and central command control rooms.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1557597774-9d273605dfa9?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Industrial Plants, Commercial High-Rises, Retail Chains, Warehouses',
            'methodology' => '<p>Site line-of-sight mapping, high-definition IP camera deployment, cloud NVR recording, and 24/7 video wall dispatching.</p>',
            'compliance' => 'CE & UL Certified Hardware, Cyber-Secure Firmware',
            'meta_title' => 'CCTV Surveillance & Digital Security Solutions Delhi NCR | JMJ',
            'meta_description' => 'Complete CCTV camera installation, AI video analytics, and 24/7 control room surveillance solutions for corporate and industrial facilities.',
            'features' => [
                ['title' => 'AI Facial Recognition & ANPR', 'description' => 'Automated vehicle license plate reading and unauthorized intruder detection.'],
                ['title' => '24/7 Control Room Operators', 'description' => 'Trained video analysts monitoring live feeds and dispatching field response units.']
            ],
            'faqs' => [
                ['question' => 'Can you integrate existing CCTV cameras into a central monitoring room?', 'answer' => 'Yes, our surveillance engineers can audit and integrate your legacy analog or IP cameras into a unified monitoring network.']
            ]
        ]
    ];

    foreach ($securityServicesData as $idx => $s) {
        $db->query(
            "INSERT INTO `services` (
                `category_id`, `name`, `slug`, `icon`, `short_summary`, `overview`, 
                `hero_image`, `target_sectors`, `methodology`, `standards_compliance`, 
                `meta_title`, `meta_description`, `is_featured`, `status`, `display_order`
            ) VALUES (
                :cat_id, :name, :slug, :icon, :short_summary, :overview, 
                :hero_image, :target_sectors, :methodology, :standards_compliance, 
                :meta_title, :meta_description, :is_featured, 'published', :display_order
            ) ON DUPLICATE KEY UPDATE 
                `name` = VALUES(`name`), `overview` = VALUES(`overview`), 
                `short_summary` = VALUES(`short_summary`), `hero_image` = VALUES(`hero_image`),
                `meta_title` = VALUES(`meta_title`), `meta_description` = VALUES(`meta_description`)",
            [
                'cat_id' => $secCatId,
                'name' => $s['name'],
                'slug' => $s['slug'],
                'icon' => $s['icon'],
                'short_summary' => $s['short_summary'],
                'overview' => $s['overview'],
                'hero_image' => $s['hero_image'],
                'target_sectors' => $s['target_sectors'],
                'methodology' => $s['methodology'],
                'standards_compliance' => $s['compliance'],
                'meta_title' => $s['meta_title'],
                'meta_description' => $s['meta_description'],
                'is_featured' => ($idx < 4) ? 1 : 0,
                'display_order' => $idx + 1
            ]
        );

        $srvId = (int)$db->fetchColumn("SELECT id FROM services WHERE slug = :slug", ['slug' => $s['slug']]);

        // Insert features
        $db->query("DELETE FROM service_features WHERE service_id = :sid", ['sid' => $srvId]);
        foreach ($s['features'] as $fIdx => $feat) {
            $db->query(
                "INSERT INTO service_features (service_id, title, description, display_order) VALUES (:sid, :title, :desc, :ord)",
                ['sid' => $srvId, 'title' => $feat['title'], 'desc' => $feat['description'], 'ord' => $fIdx + 1]
            );
        }

        // Insert FAQs
        $db->query("DELETE FROM service_faqs WHERE service_id = :sid", ['sid' => $srvId]);
        foreach ($s['faqs'] as $qIdx => $faq) {
            $db->query(
                "INSERT INTO service_faqs (service_id, question, answer, display_order) VALUES (:sid, :q, :a, :ord)",
                ['sid' => $srvId, 'q' => $faq['question'], 'a' => $faq['answer'], 'ord' => $qIdx + 1]
            );
        }
    }

    // 6. Seed 14 Cleaning Services
    echo "Seeding 14 Professional Cleaning & Sanitization Services..." . PHP_EOL;
    $cleaningServicesData = [
        [
            'name' => 'Industrial Cleaning',
            'slug' => 'industrial-cleaning',
            'icon' => 'fas fa-industry',
            'short_summary' => 'Heavy machine degreasing, high-bay warehouse dust extraction, chemical spill sanitization, and production line deep cleans.',
            'overview' => '<p>Manufacturing environments accumulate stubborn grease, toxic chemical residues, heavy particulate dust, and machinery oils. JMJ Enterprises Solutions deploys industrial scrubbers, steam blasters, and chemical degreasers to restore clean, safe production floors.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Automotive Plants, Heavy Machinery, Food Processing, Warehouses',
            'methodology' => '<p>High-pressure hot water blasting, automated scrubber-drier passes, oil-absorbent chemical treatment, and air duct decontamination.</p>',
            'compliance' => 'OSHA Industrial Hygiene Standards, ISO 14001 Compliant',
            'meta_title' => 'Industrial Cleaning Services Delhi NCR, Manesar, Noida | JMJ',
            'meta_description' => 'Heavy industrial cleaning and factory degreasing services. High-bay vacuuming, chemical scrubbing & warehouse maintenance across India.',
            'features' => [
                ['title' => 'Heavy Oil & Grease Degreasing', 'description' => 'Biodegradable industrial solvents breaking down deep oil deposits.'],
                ['title' => 'High-Bay Ceiling & Truss Vacuuming', 'description' => 'Removal of combustible dust from overhead structural beams.'],
                ['title' => 'Automated Ride-on Scrubbers', 'description' => 'Rapid, mirror-finish cleaning of massive warehouse floor plates.']
            ],
            'faqs' => [
                ['question' => 'Can you execute industrial cleaning during factory shutdowns?', 'answer' => 'Yes, we specialize in high-speed 24/7 turnarounds during planned weekend maintenance or holiday plant shutdowns.']
            ]
        ],
        [
            'name' => 'Hospital Cleaning',
            'slug' => 'hospital-cleaning',
            'icon' => 'fas fa-hospital-user',
            'short_summary' => 'Pathogen micro-sanitization, sterile operating room scrub-downs, bio-waste protocol handling, and ICU disinfection.',
            'overview' => '<p>Healthcare facilities require strict infection control to prevent Hospital-Acquired Infections (HAIs). JMJ utilizes EPA-registered hospital disinfectants, microfiber color-coded mops, HEPA vacuum filtration, and UV sanitization technology.</p>',
            'hero_image' => 'img/hospital.JPG',
            'target_sectors' => 'Operating Theaters, ICUs, Dialysis Units, Diagnostic Labs, Maternity Wards',
            'methodology' => '<p>Terminal room cleaning, cross-contamination prevention via color coding, contact-time disinfectant spraying, and ATP bioluminescence hygiene validation.</p>',
            'compliance' => 'NABH Infection Control Guidelines, CDC Healthcare Sanitation Protocol',
            'meta_title' => 'Hospital Cleaning & Healthcare Sanitization Services Delhi | JMJ',
            'meta_description' => 'Hospital-grade pathogen sanitization and medical facility cleaning. ICU, operation theater & clinical deep disinfection across Delhi NCR.',
            'features' => [
                ['title' => 'Zero Cross-Contamination System', 'description' => 'Four-color microfiber protocol preventing pathogen transfer between wards.'],
                ['title' => 'Bio-Hazard Waste Handling', 'description' => 'Strict segregation and disposal of medical waste in compliance with statutory norms.'],
                ['title' => 'Terminal Clean Protocols for OTs', 'description' => 'Deep sterile scrubbing of walls, surgical lights, tables, and vents.']
            ],
            'faqs' => [
                ['question' => 'Do you use eco-friendly hospital disinfectants?', 'answer' => 'Yes, we use non-toxic, quaternary ammonium and hydrogen peroxide-based EPA-approved disinfectants that kill 99.99% of pathogens safely.']
            ]
        ],
        [
            'name' => 'Restaurant Cleaning',
            'slug' => 'restaurant-cleaning',
            'icon' => 'fas fa-utensils',
            'short_summary' => 'Commercial kitchen deep degreasing, exhaust hood steam cleaning, kitchen tile scrubbing, and dining hall hygiene.',
            'overview' => '<p>Commercial kitchens face strict FSSAI hygiene audits and fire safety regulations. JMJ cleans exhaust hoods, grease traps, kitchen stoves, prep surfaces, walk-in freezers, and dining floors to ensure flawless food hygiene.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Fine Dining Restaurants, Cloud Kitchens, Food Courts, Hotel Banquet Kitchens',
            'methodology' => '<p>Caustic grease stripping, pressurized steam injection, grout mold eradication, and food-safe surface sanitization.</p>',
            'compliance' => 'FSSAI Hygiene Standards, NFPA 96 Fire Safety Guidelines',
            'meta_title' => 'Commercial Restaurant & Kitchen Cleaning Services Delhi NCR | JMJ',
            'meta_description' => 'Professional restaurant kitchen deep cleaning and hood degreasing in Delhi NCR. FSSAI compliant food-grade sanitization services.',
            'features' => [
                ['title' => 'Exhaust Canopy & Duct Degreasing', 'description' => 'Eliminating flammable grease buildup to prevent kitchen chimney fires.'],
                ['title' => 'Walk-in Chiller & Freezer Sanitation', 'description' => 'Mold eradication and deep shelf disinfection.'],
                ['title' => 'Food-Safe Surface Sanitizers', 'description' => 'Residue-free chemicals safe for culinary preparation counters.']
            ],
            'faqs' => [
                ['question' => 'When do you clean restaurant kitchens?', 'answer' => 'We perform deep kitchen cleaning during overnight hours after closing (11:00 PM to 6:00 AM) so your business never pauses.']
            ]
        ],
        [
            'name' => 'Commercial Building Cleaning',
            'slug' => 'commercial-cleaning',
            'icon' => 'fas fa-building-flag',
            'short_summary' => 'Complete facility upkeep for shopping malls, IT parks, airports, and commercial real estate high-rises.',
            'overview' => '<p>High-traffic commercial real estate requires continuous, organized housekeeping rosters. JMJ handles high-gloss atrium maintenance, escalator scrub-downs, public washroom continuous cycles, and waste management.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Shopping Malls, Commercial Atriums, Tech Hubs, Transit Centers',
            'methodology' => '<p>Scheduled peak/non-peak janitorial loops, automated floor buffing, high-touch sanitization, and washroom odor management.</p>',
            'compliance' => 'ISO 9001:2015 Quality Certified',
            'meta_title' => 'Commercial Building Cleaning Services Delhi, Gurgaon, Noida | JMJ',
            'meta_description' => 'Comprehensive commercial building and mall housekeeping services across Delhi NCR. High-traffic cleaning management.',
            'features' => [
                ['title' => 'Atrium & Glass Facade Cleaning', 'description' => 'Streak-free high-visibility lobby and glass railing polishing.'],
                ['title' => 'Intensive Washroom Sanitization', 'description' => 'Frequent checklist-driven disinfection and automated air freshening.']
            ],
            'faqs' => [
                ['question' => 'Do you provide on-site janitorial supplies and machines?', 'answer' => 'Yes, our contracts include complete industrial machinery (Taski, Roots, Karcher) and eco-friendly cleaning consumables.']
            ]
        ],
        [
            'name' => 'Floor Waxing Cleaning',
            'slug' => 'floor-waxing',
            'icon' => 'fas fa-brush',
            'short_summary' => 'Heavy chemical floor stripping, high-solids polymer wax application, and high-speed burnishing for commercial vinyl and linoleum.',
            'overview' => '<p>Restore worn, scratched commercial vinyl, VCT, linoleum, and terrazzo floors to a durable, brilliant wet-look gloss. JMJ’s multi-coat stripping and polymer waxing process protects floors from heavy foot traffic and stains.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Hospitals, Retail Supermarkets, Schools, Corporate Offices',
            'methodology' => '<p>Baseboard edging, rotary chemical stripping, neutralizer rinse, 4 to 5 coats of high-solids polymer sealants, and 2000-RPM thermal burnishing.</p>',
            'compliance' => 'Slip-Resistant ASTM D-2047 Certified',
            'meta_title' => 'Commercial Floor Stripping & Waxing Services Delhi NCR | JMJ',
            'meta_description' => 'High-gloss commercial floor waxing, stripping, and high-speed burnishing for hospitals, offices, and retail stores in Delhi NCR.',
            'features' => [
                ['title' => 'Multi-Coat Polymer Sealant', 'description' => 'Long-lasting high-durability protection resisting scuffs and shoe marks.'],
                ['title' => 'Non-Slip High Gloss Finish', 'description' => 'Ultra-glossy aesthetic meeting international coefficient of friction safety standards.']
            ],
            'faqs' => [
                ['question' => 'How often should commercial floors be stripped and waxed?', 'answer' => 'High-traffic facilities typically require annual deep stripping with quarterly high-speed burnishing and top-coat re-applications.']
            ]
        ],
        [
            'name' => 'Professional Floor Cleaning',
            'slug' => 'professional-floor-cleaning',
            'icon' => 'fas fa-gem',
            'short_summary' => 'Italian marble diamond polishing, granite crystallization, terrazzo honing, and epoxy floor deep scrubbing.',
            'overview' => '<p>Natural stone and engineered floors require specialized care. JMJ uses diamond abrasive resin pads, Italian crystallization powders, and heavy single-disc machines to restore mirror-like clarity to dull marble and granite surfaces.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Luxury Hotels, Corporate Lobbies, High-End Residences, Embassies',
            'methodology' => '<p>Lippage grinding, multi-grit diamond honing (400 to 3000 grit), chemical fluorosilicate crystallization, and hydro-oleophobic stone sealing.</p>',
            'compliance' => 'Stone Care International Certified',
            'meta_title' => 'Marble Polishing & Floor Crystallization Services Delhi NCR | JMJ',
            'meta_description' => 'Diamond marble polishing, granite restoration & terrazzo floor cleaning services in Delhi, Gurgaon, Noida. Mirror finish guarantee.',
            'features' => [
                ['title' => 'Diamond Grit Honing', 'description' => 'Removes scratches, micro-chips, and dull oxidation layers.'],
                ['title' => 'Chemical Crystallization', 'description' => 'Hardens the stone surface creating deep optical clarity and reflectivity.']
            ],
            'faqs' => [
                ['question' => 'Does diamond marble polishing create dust?', 'answer' => 'No, we use 100% wet diamond polishing which captures all slurry without generating airborne dust.']
            ]
        ],
        [
            'name' => 'Post Construction Cleaning',
            'slug' => 'post-construction-cleaning',
            'icon' => 'fas fa-trowel-bricks',
            'short_summary' => 'Comprehensive debris cleanup, fine silica dust extraction, paint and cement splatter removal, and move-in ready handover.',
            'overview' => '<p>Construction and renovation leave heavy cement haze, plaster splatters, paint drops on glass, and pervasive microscopic drywall dust. JMJ provides intensive post-construction deep cleaning to transform construction sites into pristine, occupant-ready spaces.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Real Estate Developers, Fit-Out Contractors, Architectural Projects, Corporate Relocations',
            'methodology' => '<p>Phase 1: Rough debris haul-away. Phase 2: Paint/grout/glue scraping with razor tools. Phase 3: HEPA dust air extraction and detail polishing.</p>',
            'compliance' => 'Green Building Handover Compliant',
            'meta_title' => 'Post Construction Cleaning Services in Delhi NCR | JMJ Enterprises',
            'meta_description' => 'Turnkey post-construction cleaning for builders, architects, and corporate fit-outs across Delhi, Noida & Gurgaon. Deep dust & paint removal.',
            'features' => [
                ['title' => 'Paint & Cement Splatter Removal', 'description' => 'Safe chemical softening and scraping from glass, frames, and tiles.'],
                ['title' => 'HVAC & Duct Vent Dusting', 'description' => 'Preventing fine dust from recirculating upon building occupancy.']
            ],
            'faqs' => [
                ['question' => 'How many cleaners are deployed for a large site handover?', 'answer' => 'We deploy teams ranging from 5 to 50+ trained cleaners with dedicated site supervisors based on your project square footage and handover deadline.']
            ]
        ],
        [
            'name' => 'Office Cleaning',
            'slug' => 'office-cleaning',
            'icon' => 'fas fa-laptop-code',
            'short_summary' => 'Daily commercial janitorial services, workstation disinfection, pantry upkeep, and executive boardroom housekeeping.',
            'overview' => '<p>A clean office boosts employee productivity and creates an impressive corporate impression. JMJ provides tailored day and night commercial cleaning contracts covering desks, keyboards, monitors, pantries, meeting rooms, and washrooms.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Corporate Offices, Co-Working Spaces, Law Firms, Consulting Agencies',
            'methodology' => '<p>Daily cleaning checklists, high-touch point sanitization, green eco-cleaning agents, and discreet non-intrusive operations.</p>',
            'compliance' => 'Indoor Air Quality (IAQ) Friendly Consumables',
            'meta_title' => 'Daily Corporate Office Cleaning Services Delhi NCR | JMJ',
            'meta_description' => 'Professional office housekeeping and commercial janitorial services in Delhi, Gurgaon, Noida. Tailored daily, weekly, and monthly packages.',
            'features' => [
                ['title' => 'Workstation & IT Hardware Sanitization', 'description' => 'Safe anti-static cleaning of laptops, phones, and monitors.'],
                ['title' => 'Pantry & Coffee Machine Hygiene', 'description' => 'Deep descaling, microwave sanitization, and continuous dish clearing.']
            ],
            'faqs' => [
                ['question' => 'Do you provide day-boy and pantry personnel?', 'answer' => 'Yes, our corporate office packages can include dedicated full-time day pantry stewards and washroom attendants.']
            ]
        ],
        [
            'name' => 'Tile and Grout Cleaning',
            'slug' => 'tile-grout-cleaning',
            'icon' => 'fas fa-border-all',
            'short_summary' => 'High-pressure chemical grout extraction, mildew removal, tile scrub polishing, and penetrating silicone grout sealing.',
            'overview' => '<p>Porous grout lines trap dirt, mold, and oils, turning dark and unsanitary over time. JMJ uses pressurized rotary extraction tools and specialized acidic/alkaline cleaners to pull dirt out of grout pores, followed by mold-resistant sealing.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Commercial Kitchens, Locker Rooms, Hotel Bathrooms, Food Courts',
            'methodology' => '<p>Chemical pre-treatment, oscillating bristle scrubbing, 1200-PSI enclosed hydro-extraction, and clear hydrophobic grout sealant.</p>',
            'compliance' => 'ANSI Tile Standards Compliant',
            'meta_title' => 'Tile and Grout Deep Cleaning Services Delhi NCR | JMJ Enterprises',
            'meta_description' => 'Restore discolored tiles and blackened grout lines. Commercial high-pressure tile cleaning and grout sealing across Delhi NCR.',
            'features' => [
                ['title' => 'Penetrating Grout Sealers', 'description' => 'Forms an invisible barrier preventing future stains and water penetration.'],
                ['title' => 'Enclosed Hydro-Extraction', 'description' => 'Captures all dirty water instantly without splashing adjoining walls.']
            ],
            'faqs' => [
                ['question' => 'Can you remove hard water calcium stains from tiles?', 'answer' => 'Yes, our specialized mineral descalers break down thick calcium, limescale, and soap scum deposits safely.']
            ]
        ],
        [
            'name' => 'Carpet Cleaning',
            'slug' => 'carpet-cleaning',
            'icon' => 'fas fa-rug',
            'short_summary' => 'Deep steam injection-extraction, encapsulation shampooing, stain spot removal, and antimicrobial deodorization.',
            'overview' => '<p>Corporate carpet tiles trap dust mites, coffee spills, allergens, and odors. JMJ’s heated hot-water extraction (HWE) systems penetrate deep into carpet fibers to extract embedded dirt while allowing rapid 2-to-4 hour drying times.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Corporate IT Floors, Auditoriums, Hotels, Executive Suites',
            'methodology' => '<p>Pile lifting vacuuming, enzyme spot pre-treatment, high-pressure steam injection with twin-vacuum extraction, and high-velocity carpet air blowers.</p>',
            'compliance' => 'CRI (Carpet & Rug Institute) Certified Methodology',
            'meta_title' => 'Commercial Carpet Cleaning & Shampooing Delhi NCR | JMJ',
            'meta_description' => 'Fast-drying commercial carpet cleaning, steam extraction, and stain removal services for offices and hotels across Delhi, Gurgaon & Noida.',
            'features' => [
                ['title' => 'Fast 2-4 Hour Rapid Dry', 'description' => 'High-CFM moisture extractors and turbo fans ensure no soggy residue.'],
                ['title' => 'Stubborn Stain Removal', 'description' => 'Targeted chemical spotters for coffee, tea, ink, and grease.']
            ],
            'faqs' => [
                ['question' => 'Will carpet shampooing disrupt our office workday?', 'answer' => 'We perform carpet cleaning over Friday night or Saturday so your carpets are 100% dry and fresh by Monday morning.']
            ]
        ],
        [
            'name' => 'Window Cleaning',
            'slug' => 'window-cleaning',
            'icon' => 'fas fa-wind',
            'short_summary' => 'High-altitude glass facade cleaning, certified rope-access abseiling, BMU cradle operations, and streak-free pure water pole systems.',
            'overview' => '<p>Architectural glass facades accumulate pollution, acid rain stains, and dust. JMJ provides certified rope-access abseilers and spider-harness teams trained to clean exterior glass, aluminum composite panels (ACP), and curtain walls safely at any height.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Commercial High-Rises, Corporate Glass Towers, Hospitals, Hotels',
            'methodology' => '<p>IRATA/SPRAT-certified anchor inspection, dual-rope suspension, deionized pure-water reverse osmosis filtration, and squeegee polishing.</p>',
            'compliance' => 'IRATA Rope Access Safety Standards, EN 361 Fall Arrest Compliant',
            'meta_title' => 'High-Rise Window & Glass Facade Cleaning Delhi NCR | JMJ',
            'meta_description' => 'Professional rope-access high-rise window cleaning and facade glass maintenance in Delhi, Gurgaon, Noida. Certified safety protocols.',
            'features' => [
                ['title' => 'Certified Rope-Access Technicians', 'description' => 'Strict double-rope safety harnesses and daily anchor point inspection.'],
                ['title' => 'Pure Water Deionization System', 'description' => 'Spotless, streak-free mineral-free water leaving zero residue on glass.']
            ],
            'faqs' => [
                ['question' => 'Are your high-altitude facade cleaners insured?', 'answer' => 'Yes, 100% of our high-altitude rope access personnel carry comprehensive Workman Compensation and third-party liability insurance.']
            ]
        ],
        [
            'name' => 'Move Out Cleaning',
            'slug' => 'move-out-cleaning',
            'icon' => 'fas fa-box-open',
            'short_summary' => 'Comprehensive lease-end deep cleaning, cabinet interior degreasing, wall mark scrubbing, and full deposit-recovery standard cleanups.',
            'overview' => '<p>Vacating a commercial property or residential home requires meeting strict handover conditions. JMJ provides top-to-bottom move-out deep cleaning covering kitchens, bathrooms, closets, windows, fixtures, and floors to ensure full security deposit refund.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Corporate Tenant Handover, Expat Residences, Rental Apartments',
            'methodology' => '<p>Systematic room-by-room checklist covering interior appliances, light fixtures, switchboards, baseboards, and deep sanitary descaling.</p>',
            'compliance' => 'Lease Handover Inspection Standards',
            'meta_title' => 'Move Out & End of Lease Deep Cleaning Delhi NCR | JMJ Enterprises',
            'meta_description' => 'Guaranteed deposit-return move-out cleaning services in Delhi, Gurgaon & Noida. Full property handover sanitization and detailing.',
            'features' => [
                ['title' => 'Complete Fixture & Cabinet Interior Detailing', 'description' => 'Vacuuming and sanitizing every drawer, shelf, and closet.'],
                ['title' => 'Bathroom Limescale & Mold Eradication', 'description' => 'Deep descaling of faucets, shower enclosures, and sanitaryware.']
            ],
            'faqs' => [
                ['question' => 'Can you handle both commercial and residential move-out cleaning?', 'answer' => 'Yes, we provide move-out cleaning for 100,000+ sq ft corporate lease terminations as well as luxury residential properties.']
            ]
        ],
        [
            'name' => 'Domestic Cleaning',
            'slug' => 'domestic-cleaning',
            'icon' => 'fas fa-house',
            'short_summary' => 'Luxury villa and residential deep cleaning, sofa sanitization, kitchen chimney degreasing, and balcony pressure washing.',
            'overview' => '<p>Enjoy a spotless, healthy home environment. JMJ delivers professional residential deep cleaning crews equipped with industrial equipment to scrub kitchens, washrooms, mattresses, sofas, balconies, and fans.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Luxury Villas, Farmhouses, Penthouses, High-End Apartments',
            'methodology' => '<p>HEPA dust extraction, steam sanitization of mattresses and sofas, kitchen tile degreasing, and balcony high-pressure washing.</p>',
            'compliance' => 'Eco-Friendly, Non-Toxic Residential Grade',
            'meta_title' => 'Home & Residential Deep Cleaning Services Delhi NCR | JMJ',
            'meta_description' => 'Trusted home deep cleaning and luxury villa sanitization services across Delhi, Gurgaon & Noida. Sofa, kitchen, and bathroom deep scrubbing.',
            'features' => [
                ['title' => 'Pet-Friendly Non-Toxic Cleaners', 'description' => 'Safe for infants, children, and household pets.'],
                ['title' => 'Steam Mattress & Upholstery Sanitization', 'description' => 'Thermal killing of dust mites, bedbugs, and bacteria.']
            ],
            'faqs' => [
                ['question' => 'How long does a home deep clean take?', 'answer' => 'A standard 3-4 BHK apartment deep clean typically takes 5 to 8 hours with a crew of 4 to 6 trained professionals.']
            ]
        ],
        [
            'name' => 'Upholstery Cleaning',
            'slug' => 'upholstery-cleaning',
            'icon' => 'fas fa-couch',
            'short_summary' => 'Fabric and leather sofa restoration, office chair injection-extraction, acoustic panel dusting, and fabric protection coating.',
            'overview' => '<p>Office mesh chairs, reception sofas, and fabric partitions absorb perspiration, food spills, and atmospheric dust. JMJ deep cleans and restores upholstery using specialized moisture-controlled extraction that prevents water ring marks and fabric shrinking.</p>',
            'hero_image' => 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?auto=format&fit=crop&q=80&w=1200',
            'target_sectors' => 'Corporate Workstations, Conference Rooms, Luxury Lounges, Theaters',
            'methodology' => '<p>Fabric fiber identification, pH-balanced pre-spray, gentle agitation with horsehair brushes, and high-vacuum moisture extraction.</p>',
            'compliance' => 'IICRC Upholstery Standards Compliant',
            'meta_title' => 'Sofa & Office Chair Upholstery Cleaning Delhi NCR | JMJ',
            'meta_description' => 'Commercial upholstery cleaning and sofa shampooing in Delhi, Gurgaon, Noida. Office chair deep extraction and leather conditioning.',
            'features' => [
                ['title' => 'Leather Conditioning & Protection', 'description' => 'Gentle cleaning with lanolin-enriched conditioners preventing leather cracking.'],
                ['title' => 'Scotchgard Stain Shielding', 'description' => 'Optional hydrophobic fabric protection repelling future liquid spills.']
            ],
            'faqs' => [
                ['question' => 'Can you clean 500+ office chairs over a weekend?', 'answer' => 'Yes, our multi-unit commercial extraction teams can clean up to 1,000+ office chairs in a single weekend turnaround.']
            ]
        ]
    ];

    foreach ($cleaningServicesData as $idx => $s) {
        $db->query(
            "INSERT INTO `services` (
                `category_id`, `name`, `slug`, `icon`, `short_summary`, `overview`, 
                `hero_image`, `target_sectors`, `methodology`, `standards_compliance`, 
                `meta_title`, `meta_description`, `is_featured`, `status`, `display_order`
            ) VALUES (
                :cat_id, :name, :slug, :icon, :short_summary, :overview, 
                :hero_image, :target_sectors, :methodology, :standards_compliance, 
                :meta_title, :meta_description, :is_featured, 'published', :display_order
            ) ON DUPLICATE KEY UPDATE 
                `name` = VALUES(`name`), `overview` = VALUES(`overview`), 
                `short_summary` = VALUES(`short_summary`), `hero_image` = VALUES(`hero_image`),
                `meta_title` = VALUES(`meta_title`), `meta_description` = VALUES(`meta_description`)",
            [
                'cat_id' => $cleanCatId,
                'name' => $s['name'],
                'slug' => $s['slug'],
                'icon' => $s['icon'],
                'short_summary' => $s['short_summary'],
                'overview' => $s['overview'],
                'hero_image' => $s['hero_image'],
                'target_sectors' => $s['target_sectors'],
                'methodology' => $s['methodology'],
                'standards_compliance' => $s['compliance'],
                'meta_title' => $s['meta_title'],
                'meta_description' => $s['meta_description'],
                'is_featured' => ($idx < 4) ? 1 : 0,
                'display_order' => $idx + 1
            ]
        );

        $srvId = (int)$db->fetchColumn("SELECT id FROM services WHERE slug = :slug", ['slug' => $s['slug']]);

        // Insert features
        $db->query("DELETE FROM service_features WHERE service_id = :sid", ['sid' => $srvId]);
        foreach ($s['features'] as $fIdx => $feat) {
            $db->query(
                "INSERT INTO service_features (service_id, title, description, display_order) VALUES (:sid, :title, :desc, :ord)",
                ['sid' => $srvId, 'title' => $feat['title'], 'desc' => $feat['description'], 'ord' => $fIdx + 1]
            );
        }

        // Insert FAQs
        $db->query("DELETE FROM service_faqs WHERE service_id = :sid", ['sid' => $srvId]);
        foreach ($s['faqs'] as $qIdx => $faq) {
            $db->query(
                "INSERT INTO service_faqs (service_id, question, answer, display_order) VALUES (:sid, :q, :a, :ord)",
                ['sid' => $srvId, 'q' => $faq['question'], 'a' => $faq['answer'], 'ord' => $qIdx + 1]
            );
        }
    }

    // 7. Seed Blog Categories & Tags
    echo "Seeding Blog Taxonomies..." . PHP_EOL;
    $blogCats = [
        ['name' => 'Corporate Security', 'slug' => 'corporate-security', 'description' => 'Strategic manned guarding, access control, and enterprise asset protection insights.'],
        ['name' => 'Commercial Cleaning', 'slug' => 'commercial-cleaning', 'description' => 'Industrial hygiene, floor restoration, and institutional sanitization best practices.'],
        ['name' => 'Compliance & PSARA', 'slug' => 'compliance-psara', 'description' => 'Legal guidelines, labor standards, and PSARA security regulations in India.'],
        ['name' => 'Facility Management', 'slug' => 'facility-management', 'description' => 'Integrated workplace operations, smart infrastructure, and green building hygiene.']
    ];
    foreach ($blogCats as $bc) {
        $db->query(
            "INSERT INTO `blog_categories` (`name`, `slug`, `description`, `status`) 
             VALUES (:name, :slug, :description, 'active') 
             ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `description` = VALUES(`description`)",
            $bc
        );
    }

    $blogTags = [
        'Security Guards Delhi', 'Corporate Security', 'Hospital Sanitization', 'Industrial Cleaning', 
        'Floor Waxing', 'PSARA Compliance', 'Access Control', 'Facility Management', 'CCTV Surveillance'
    ];
    foreach ($blogTags as $bt) {
        $db->query(
            "INSERT INTO `blog_tags` (`name`, `slug`) VALUES (:n, :s) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`)",
            ['n' => $bt, 's' => slugify($bt)]
        );
    }

    // 8. Seed Blog Posts
    echo "Seeding Initial Blog Articles..." . PHP_EOL;
    $corpSecCatId = (int)$db->fetchColumn("SELECT id FROM blog_categories WHERE slug = 'corporate-security'");
    $commCleanCatId = (int)$db->fetchColumn("SELECT id FROM blog_categories WHERE slug = 'commercial-cleaning'");
    $compCatId = (int)$db->fetchColumn("SELECT id FROM blog_categories WHERE slug = 'compliance-psara'");

    $sampleBlogs = [
        [
            'title' => 'Why Comprehensive Security Audits are Critical for Corporate Offices in 2026',
            'slug' => 'why-corporate-security-audits-are-critical',
            'category_id' => $corpSecCatId,
            'featured_image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1200',
            'short_description' => 'Discover how modern physical security gap analyses protect enterprise campuses against unauthorized intrusions, data theft, and emergency crises.',
            'content' => '<h2>The Evolving Threat Landscape in Commercial Real Estate</h2><p>In 2026, corporate facilities face a dual challenge: maintaining an open, welcoming environment for clients and top talent while rigorously insulating intellectual property, server infrastructure, and personnel from physical and cyber-physical breaches.</p><p>A modern physical security audit is not merely counting guards at turnstiles; it is an architectural gap analysis assessing line-of-sight blinds, vehicle barrier crash-ratings, digital visitor logs, and emergency egress routes.</p><h2>Key Pillars of an Enterprise Security Assessment</h2><ul><li><strong>Perimeter Defense Analysis:</strong> Verifying fence integrity, automated gate speed, and perimeter CCTV coverage.</li><li><strong>Visitor Flow Optimization:</strong> Eliminating bottleneck lines while enforcing multi-factor badge authentication.</li><li><strong>After-Hours Patrol Auditing:</strong> Ensuring guard vigilance during non-operational nighttime windows via geofenced patrol checkpoints.</li><li><strong>Fire & Disaster Readiness:</strong> Checking fire exit stairwell accessibility and first-responder team coordination.</li></ul><p>At JMJ Enterprises Solutions, our security engineers provide comprehensive site vulnerability audits before deploying trained personnel, guaranteeing tailored protection for every client.</p>',
            'meta_title' => 'Why Corporate Security Audits are Critical in 2026 | JMJ Insights',
            'meta_description' => 'Learn how comprehensive physical security audits safeguard corporate campuses, IT parks, and MNCs from modern vulnerabilities.',
            'focus_keyword' => 'corporate security audit',
            'reading_time' => 4,
            'is_featured' => 1
        ],
        [
            'title' => 'Hospital-Grade Sanitization: Infection Prevention Protocols for Commercial Spaces',
            'slug' => 'hospital-grade-sanitization-protocols',
            'category_id' => $commCleanCatId,
            'featured_image' => 'img/hospital.JPG',
            'short_description' => 'How advanced four-color microfiber protocols and EPA-certified virucides are transforming commercial hygiene standards.',
            'overview' => 'Hospital sanitization guidelines',
            'content' => '<h2>Bringing Clinical Hygiene to Commercial Workplaces</h2><p>Post-pandemic workplace standards have permanently shifted. Progressive enterprises no longer settle for basic surface dusting; they require verified pathogen-free micro-environments to curb absenteeism and protect employee health.</p><h2>The Four-Color Microfiber Protocol</h2><p>One of the most catastrophic mistakes in standard housekeeping is cross-contamination—using the same mop or cloth from a restroom on a conference table. JMJ strictly enforces the healthcare-grade color-coded matrix:</p><ul><li><strong>Red:</strong> High-risk sanitary areas, toilet fixtures, and urinals.</li><li><strong>Yellow:</strong> Washroom sinks, vanity counters, and tiled walls.</li><li><strong>Blue:</strong> General corporate desk areas, meeting tables, and executive offices.</li><li><strong>Green:</strong> Food prep counters, cafeteria surfaces, and dining tables.</li></ul><h2>Contact Time & Chemical Efficacy</h2><p>Wiping away disinfectant immediately upon spraying renders it ineffective. Hospital protocols mandate strict "wet contact dwell time" (typically 3 to 10 minutes) allowing active quaternary ammonium ions to eradicate 99.99% of bacteria and enveloped viruses.</p>',
            'meta_title' => 'Hospital-Grade Sanitization Protocols for Offices | JMJ Cleaning',
            'meta_description' => 'Explore the scientific infection prevention protocols, color-coded microfiber standards, and disinfectant dwell times used by JMJ Enterprises.',
            'focus_keyword' => 'hospital grade sanitization',
            'reading_time' => 5,
            'is_featured' => 1
        ],
        [
            'title' => 'Understanding PSARA Compliance: What Indian Enterprises Need to Know',
            'slug' => 'understanding-psara-compliance-india',
            'category_id' => $compCatId,
            'featured_image' => 'img/security.JPG',
            'short_description' => 'A comprehensive guide to the Private Security Agencies (Regulation) Act and why hiring unlicensed agencies creates severe legal liabilities.',
            'content' => '<h2>The Legal Mandate of PSARA in India</h2><p>Enacted in 2005, the Private Security Agencies (Regulation) Act (PSARA) makes it mandatory for any agency providing security personnel in India to hold a valid state-issued operational license. Despite this, many commercial complexes unknowingly contract unlicensed or fly-by-night operators.</p><h2>Severe Legal Risks of Non-Compliant Security</h2><p>Hiring an agency without active PSARA credentials exposes the enterprise to joint liability in the event of criminal incidents, theft, or labor disputes. Furthermore, untrained guards lack verified police antecedents, creating immediate internal risks.</p><h2>Mandatory Criteria for PSARA Certified Guards</h2><ol><li><strong>Mandatory 100+ Hours Classroom Training:</strong> Covering physical defense, crowd management, weapon handling, and firefighting.</li><li><strong>Criminal Background Vetting:</strong> Formal state police verification certificates.</li><li><strong>Statutory Labor Compliance:</strong> Full ESI (Employee State Insurance), EPF (Provident Fund), and minimum wage compliance.</li></ol><p>JMJ Enterprises Solutions operates under valid PSARA certifications with complete transparency across all 10 states of operation.</p>',
            'meta_title' => 'PSARA Compliance Guide for Enterprises in India | JMJ Security',
            'meta_description' => 'Why PSARA license compliance is essential when hiring security guard agencies in India. Understand legal mandates and risk prevention.',
            'focus_keyword' => 'PSARA compliance',
            'reading_time' => 4,
            'is_featured' => 0
        ],
        [
            'title' => 'Industrial Floor Maintenance: The Science of Chemical Stripping and Polymer Waxing',
            'slug' => 'industrial-floor-maintenance-stripping-waxing',
            'category_id' => $commCleanCatId,
            'featured_image' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&q=80&w=1200',
            'short_description' => 'Step-by-step technical guide on restoring vinyl, terrazzo, and linoleum floors to industrial-grade mirror luster.',
            'content' => '<h2>Why High-Traffic Commercial Floors Degrade</h2><p>Commercial Vinyl Composition Tile (VCT) and terrazzo floors are subjected to abrasive foot traffic, grit, and harsh cleaners daily. Over time, the protective seal breaks down, causing embedded yellowing, scratch lines, and dullness.</p><h2>The Professional 5-Stage Waxing Cycle</h2><ol><li><strong>Chemical Stripping:</strong> Applying alkaline floor strippers to emulsify legacy discolored wax layers.</li><li><strong>Mechanical Scrubbing:</strong> Agitating the surface with 175-RPM black abrasive stripping pads.</li><li><strong>Neutralizing Rinse:</strong> Rinsing with mild acid neutralizer to balance pH before coating.</li><li><strong>High-Solids Polymer Coating:</strong> Applying 4 to 5 thin coats of 25% solid cross-linked acrylic polymers.</li><li><strong>Thermal High-Speed Burnishing:</strong> Finishing with 2000-RPM horsehair burnishers to create a deep, durable mirror sheen.</li></ol>',
            'meta_title' => 'Floor Stripping & Waxing Maintenance Best Practices | JMJ Cleaning',
            'meta_description' => 'Technical breakdown of chemical floor stripping, polymer sealants, and high-speed burnishing for commercial complexes.',
            'focus_keyword' => 'floor stripping and waxing',
            'reading_time' => 3,
            'is_featured' => 0
        ]
    ];

    foreach ($sampleBlogs as $b) {
        $db->query(
            "INSERT INTO `blog_posts` (
                `title`, `slug`, `author_id`, `category_id`, `featured_image`, 
                `short_description`, `content`, `reading_time`, `meta_title`, 
                `meta_description`, `focus_keyword`, `status`, `publish_at`, `is_featured`
            ) VALUES (
                :title, :slug, :author_id, :category_id, :featured_image, 
                :short_description, :content, :reading_time, :meta_title, 
                :meta_description, :focus_keyword, 'published', NOW(), :is_featured
            ) ON DUPLICATE KEY UPDATE 
                `title` = VALUES(`title`), `content` = VALUES(`content`), 
                `short_description` = VALUES(`short_description`), `featured_image` = VALUES(`featured_image`)",
            [
                'title' => $b['title'],
                'slug' => $b['slug'],
                'author_id' => $adminUserId,
                'category_id' => $b['category_id'],
                'featured_image' => $b['featured_image'],
                'short_description' => $b['short_description'],
                'content' => $b['content'],
                'reading_time' => $b['reading_time'],
                'meta_title' => $b['meta_title'],
                'meta_description' => $b['meta_description'],
                'focus_keyword' => $b['focus_keyword'],
                'is_featured' => $b['is_featured']
            ]
        );
    }

    // 9. Seed Testimonials
    echo "Seeding Client Testimonials..." . PHP_EOL;
    $testimonials = [
        [
            'client_name' => 'Rajesh Malhotra',
            'company' => 'Apex Logistics Corp',
            'designation' => 'VP of Infrastructure & Logistics',
            'rating' => 5,
            'testimonial' => 'JMJ Enterprises has secured our multi-acre warehouse hubs in Delhi NCR and Haryana for over 4 years. Their weighbridge logging and material gate pass accuracy have eliminated inventory shrinkage completely.',
            'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200'
        ],
        [
            'client_name' => 'Dr. Ananya Sen',
            'company' => 'Metro Health Hospital Network',
            'designation' => 'Chief Medical Superintendent',
            'rating' => 5,
            'testimonial' => 'Their integrated hospital security guards and sanitization crews work with exceptional discipline. In our emergency wards, their de-escalation skills protect our doctors, while their OT cleaning meets NABH standards.',
            'photo' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=200'
        ],
        [
            'client_name' => 'Vikram Singhania',
            'company' => 'Vanguard Capital Real Estate',
            'designation' => 'Head of Commercial Facility Operations',
            'rating' => 5,
            'testimonial' => 'From corporate front-desk concierges to overnight high-speed floor waxing, JMJ provides turnkey reliability. Their 24/7 central dispatch control is always responsive.',
            'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200'
        ]
    ];
    $db->query("DELETE FROM testimonials");
    foreach ($testimonials as $t) {
        $db->insert('testimonials', $t);
    }

    // 10. Seed Gallery
    echo "Seeding Gallery Items..." . PHP_EOL;
    $galleryCats = [
        ['name' => 'Security Deployments', 'slug' => 'security'],
        ['name' => 'Commercial Cleaning', 'slug' => 'cleaning'],
        ['name' => 'Corporate Facilities', 'slug' => 'corporate'],
        ['name' => 'Operations Team', 'slug' => 'team']
    ];
    foreach ($galleryCats as $gc) {
        $db->query("INSERT INTO gallery_categories (name, slug) VALUES (:n, :s) ON DUPLICATE KEY UPDATE name = VALUES(name)", ['n' => $gc['name'], 's' => $gc['slug']]);
    }
    $secGalId = (int)$db->fetchColumn("SELECT id FROM gallery_categories WHERE slug = 'security'");
    $cleanGalId = (int)$db->fetchColumn("SELECT id FROM gallery_categories WHERE slug = 'cleaning'");
    $corpGalId = (int)$db->fetchColumn("SELECT id FROM gallery_categories WHERE slug = 'corporate'");

    $galleryItems = [
        ['category_id' => $secGalId, 'title' => 'Manned Security Roll Call at Corporate Campus', 'caption' => 'Morning shift tactical briefing and biometric roll call.', 'image_path' => 'img/security.JPG', 'is_featured' => 1],
        ['category_id' => $cleanGalId, 'title' => 'Hospital Sterile Ward Decontamination', 'caption' => 'NABH compliant four-color microfiber sanitization.', 'image_path' => 'img/hospital.JPG', 'is_featured' => 1],
        ['category_id' => $secGalId, 'title' => 'Bank & ATM Rapid Response Unit', 'caption' => 'Vigilant night patrol and asset safeguarding.', 'image_path' => 'https://images.unsplash.com/photo-1501167786227-4cba60f6d58f?auto=format&fit=crop&q=80&w=800', 'is_featured' => 1],
        ['category_id' => $cleanGalId, 'title' => 'Industrial Floor Waxing & Scrubbing', 'caption' => 'Polymer high-gloss mirror coating on warehouse floor.', 'image_path' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&q=80&w=800', 'is_featured' => 1],
        ['category_id' => $corpGalId, 'title' => 'Executive Front-Desk Concierge Screening', 'caption' => 'Multilingual corporate reception and access control.', 'image_path' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=800', 'is_featured' => 1],
        ['category_id' => $cleanGalId, 'title' => 'High-Altitude Facade Glass Cleaning', 'caption' => 'IRATA certified double-rope exterior curtain wall wash.', 'image_path' => 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?auto=format&fit=crop&q=80&w=800', 'is_featured' => 1]
    ];
    $db->query("DELETE FROM gallery");
    foreach ($galleryItems as $gi) {
        $db->insert('gallery', $gi);
    }

    // 11. Seed Global FAQs
    echo "Seeding Global FAQs..." . PHP_EOL;
    $globalFaqs = [
        [
            'category' => 'general',
            'question' => 'What areas and states does JMJ Enterprises Solutions cover?',
            'answer' => 'We operate across 10 strategic Indian state hubs including Delhi NCR (Central Headquarters), Haryana (Gurgaon), Uttar Pradesh (Noida), Karnataka (Bangalore), Maharashtra (Mumbai), Tamil Nadu, Telangana, West Bengal, Madhya Pradesh, and Punjab.'
        ],
        [
            'category' => 'security',
            'question' => 'What vetting process do your security guards undergo?',
            'answer' => 'Every guard undergoes a multi-tier vetting process including national criminal database background checks, local police station character verification, residential address verification, physical fitness testing, and 100+ hours of PSARA-certified training.'
        ],
        [
            'category' => 'cleaning',
            'question' => 'What machinery and chemicals do your cleaning teams use?',
            'answer' => 'We utilize world-class industrial cleaning equipment (Taski, Roots, Karcher) and eco-friendly, biodegradable, EPA-approved chemicals specifically formulated for heavy degreasing, marble crystallization, and hospital pathogen eradication.'
        ],
        [
            'category' => 'corporate',
            'question' => 'How can we request an on-site security or facility survey?',
            'answer' => 'You can request a free consultation via our online Get a Quote form or by calling our 24/7 Dispatch Control Center at 18008890832 or +91-9999381777.'
        ]
    ];
    $db->query("DELETE FROM faqs");
    foreach ($globalFaqs as $f) {
        $db->insert('faqs', $f);
    }

    // 12. Seed Global Settings
    echo "Seeding Global Settings..." . PHP_EOL;
    $settings = [
        ['setting_group' => 'general', 'key_name' => 'company_name', 'key_value' => 'JMJ Enterprises Solutions Ltd.', 'field_type' => 'text'],
        ['setting_group' => 'general', 'key_name' => 'company_tagline', 'key_value' => 'Securing Assets. Perfecting Spaces.', 'field_type' => 'text'],
        ['setting_group' => 'general', 'key_name' => 'established_year', 'key_value' => '2013', 'field_type' => 'text'],
        ['setting_group' => 'contact', 'key_name' => 'phone_primary', 'key_value' => '+91-9999381777', 'field_type' => 'text'],
        ['setting_group' => 'contact', 'key_name' => 'phone_toll_free', 'key_value' => '18008890832', 'field_type' => 'text'],
        ['setting_group' => 'contact', 'key_name' => 'phone_landline', 'key_value' => '011-41037091', 'field_type' => 'text'],
        ['setting_group' => 'contact', 'key_name' => 'email_support', 'key_value' => 'jmjsanu@gmail.com', 'field_type' => 'text'],
        ['setting_group' => 'contact', 'key_name' => 'email_corporate', 'key_value' => 'info@jmjenterprisessolutions.com', 'field_type' => 'text'],
        ['setting_group' => 'contact', 'key_name' => 'company_address', 'key_value' => '250, Sant Nagar, East of Kailash, New Delhi – 110065', 'field_type' => 'textarea'],
        ['setting_group' => 'contact', 'key_name' => 'whatsapp_number', 'key_value' => '+919999381777', 'field_type' => 'text'],
        ['setting_group' => 'contact', 'key_name' => 'business_hours', 'key_value' => 'Monday – Saturday: 8:00 AM – 6:00 PM | 24/7 Operations Control', 'field_type' => 'text'],
        ['setting_group' => 'social', 'key_name' => 'social_facebook', 'key_value' => 'https://facebook.com/jmjenterprises', 'field_type' => 'text'],
        ['setting_group' => 'social', 'key_name' => 'social_linkedin', 'key_value' => 'https://linkedin.com/company/jmj-enterprises-solutions', 'field_type' => 'text'],
        ['setting_group' => 'social', 'key_name' => 'social_instagram', 'key_value' => 'https://instagram.com/jmjenterprises', 'field_type' => 'text'],
        ['setting_group' => 'social', 'key_name' => 'social_youtube', 'key_value' => 'https://youtube.com/@jmjenterprises', 'field_type' => 'text'],
        ['setting_group' => 'stats', 'key_name' => 'stat_experience_years', 'key_value' => '13+', 'field_type' => 'text'],
        ['setting_group' => 'stats', 'key_name' => 'stat_clients_served', 'key_value' => '450+', 'field_type' => 'text'],
        ['setting_group' => 'stats', 'key_name' => 'stat_guards_deployed', 'key_value' => '2,500+', 'field_type' => 'text'],
        ['setting_group' => 'stats', 'key_name' => 'stat_states_footprint', 'key_value' => '10', 'field_type' => 'text'],
        ['setting_group' => 'stats', 'key_name' => 'stat_sqft_cleaned', 'key_value' => '15M+', 'field_type' => 'text'],
        ['setting_group' => 'compliance', 'key_name' => 'psara_compliance_text', 'key_value' => 'PSARA Compliant • ISO 9001:2015 Certified • Police Vetted Guard Personnel', 'field_type' => 'text']
    ];
    foreach ($settings as $st) {
        $db->query(
            "INSERT INTO `settings` (`setting_group`, `key_name`, `key_value`, `field_type`) 
             VALUES (:setting_group, :key_name, :key_value, :field_type) 
             ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`), `setting_group` = VALUES(`setting_group`)",
            $st
        );
    }

    // 13. Seed Global SEO Metadata
    echo "Seeding Global SEO Metadata..." . PHP_EOL;
    $seoMetadata = [
        [
            'page_route' => 'home',
            'meta_title' => 'JMJ Enterprises Solutions | Professional Security & Cleaning Services India',
            'meta_description' => 'JMJ Enterprises Solutions is a leading B2B corporate security, manned guarding, and commercial cleaning services provider with 10 state hubs across India.',
            'meta_keywords' => 'security services delhi, cleaning services delhi, corporate security guard, manned guarding india, industrial floor waxing, hospital cleaning'
        ],
        [
            'page_route' => 'about',
            'meta_title' => 'About JMJ Enterprises Solutions | 10-State Security & Facility Network',
            'meta_description' => 'Learn about JMJ Enterprises Solutions, established in 2013 in New Delhi. PSARA compliant manned security, commercial cleaning, and corporate facility management.',
            'meta_keywords' => 'about jmj enterprises, security company delhi, psara security agency, corporate facility management india'
        ],
        [
            'page_route' => 'security-services',
            'meta_title' => 'Manned Security Guard Services in Delhi NCR & India | JMJ Enterprises',
            'meta_description' => 'Explore 12 specialized manned security services: Corporate offices, industrial plants, ATMs, embassies, hospitals, hotels, and CCTV solutions.',
            'meta_keywords' => 'security services, security guard company, corporate security, atm security guards, hospital security, lady security officers'
        ],
        [
            'page_route' => 'cleaning-services',
            'meta_title' => 'Commercial & Industrial Cleaning Services | JMJ Enterprises Solutions',
            'meta_description' => 'Comprehensive commercial cleaning services: Hospital disinfection, industrial floor waxing, office housekeeping, post-construction cleanup & facade glass wash.',
            'meta_keywords' => 'cleaning services, commercial cleaning delhi, industrial cleaning, floor waxing, hospital sanitization, office housekeeping'
        ],
        [
            'page_route' => 'blog',
            'meta_title' => 'Security & Commercial Facility Insights Blog | JMJ Enterprises',
            'meta_description' => 'Expert articles on physical security risk assessments, PSARA legal compliance, infection prevention protocols, and commercial floor maintenance.',
            'meta_keywords' => 'security blog, cleaning insights, facility management tips, psara compliance blog'
        ],
        [
            'page_route' => 'gallery',
            'meta_title' => 'Operations Gallery | JMJ Enterprises Solutions Manned Guarding & Cleaning',
            'meta_description' => 'View real operational photos of JMJ Enterprises security personnel, hospital cleaning teams, and industrial floor restorations.',
            'meta_keywords' => 'jmj gallery, security guard photos, cleaning operations photos'
        ],
        [
            'page_route' => 'contact',
            'meta_title' => 'Contact JMJ Enterprises Solutions | 24/7 Operations Control Center',
            'meta_description' => 'Get in touch with JMJ Enterprises Solutions HQ in New Delhi. 24/7 Dispatch Control: 18008890832 / +91-9999381777.',
            'meta_keywords' => 'contact jmj enterprises, security guard phone number delhi, cleaning services contact'
        ],
        [
            'page_route' => 'get-a-quote',
            'meta_title' => 'Request a Free Quote & Site Survey | JMJ Enterprises Solutions',
            'meta_description' => 'Book an on-site structural security risk assessment or commercial cleaning contract consultation across India.',
            'meta_keywords' => 'security quote, cleaning contract quote, site survey request'
        ]
    ];
    foreach ($seoMetadata as $seo) {
        $db->query(
            "INSERT INTO `seo_metadata` (`page_route`, `meta_title`, `meta_description`, `meta_keywords`) 
             VALUES (:page_route, :meta_title, :meta_description, :meta_keywords) 
             ON DUPLICATE KEY UPDATE `meta_title` = VALUES(`meta_title`), `meta_description` = VALUES(`meta_description`), `meta_keywords` = VALUES(`meta_keywords`)",
            $seo
        );
    }

    echo "=== Database Setup and Seeding Completed Successfully! ===" . PHP_EOL;

} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(1);
}
