-- JMJ Enterprise Solutions - Workforce Management & Field Operations Database
-- Engine: MySQL 8.0+ / MariaDB 10.4+ with InnoDB and UTF8MB4 Unicode
-- Multi-Tenant Ready Schema with Strict Relational Integrity

CREATE DATABASE IF NOT EXISTS `jmj_workforce_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `jmj_workforce_db`;

-- ============================================================================
-- 1. TENANCY, BRANCHES & RBAC SECURITY
-- ============================================================================

CREATE TABLE IF NOT EXISTS `companies` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(150) NOT NULL,
  `legal_name` VARCHAR(200) NULL,
  `logo` VARCHAR(255) NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `address` TEXT NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `state` VARCHAR(100) NOT NULL,
  `pincode` VARCHAR(20) NOT NULL,
  `gst_number` VARCHAR(50) NULL,
  `pan_number` VARCHAR(50) NULL,
  `psara_license_no` VARCHAR(100) NULL,
  `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `state` VARCHAR(100) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `address` TEXT NOT NULL,
  `contact_person` VARCHAR(150) NULL,
  `contact_phone` VARCHAR(50) NULL,
  `contact_email` VARCHAR(150) NULL,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `uk_company_branch_code` (`company_id`, `code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `label` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `is_system` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `label` VARCHAR(150) NOT NULL,
  `module_group` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `branch_id` INT UNSIGNED NULL,
  `role_id` INT UNSIGNED NOT NULL,
  `employee_id` INT UNSIGNED NULL,
  `client_id` INT UNSIGNED NULL,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(50) NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `avatar` VARCHAR(255) NULL,
  `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
  `is_mfa_enabled` TINYINT(1) DEFAULT 0,
  `mfa_secret` VARCHAR(100) NULL,
  `last_login_at` DATETIME NULL,
  `last_login_ip` VARCHAR(45) NULL,
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. CLIENTS, SITES, CONTRACTS & GEOFENCES
-- ============================================================================

CREATE TABLE IF NOT EXISTS `clients` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `client_code` VARCHAR(50) NOT NULL,
  `company_name` VARCHAR(200) NOT NULL,
  `industry` VARCHAR(100) NULL,
  `contact_person` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `billing_address` TEXT NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `state` VARCHAR(100) NOT NULL,
  `pincode` VARCHAR(20) NOT NULL,
  `gst_number` VARCHAR(50) NULL,
  `pan_number` VARCHAR(50) NULL,
  `billing_cycle` ENUM('monthly', 'bimonthly', 'quarterly', 'custom') DEFAULT 'monthly',
  `status` ENUM('active', 'inactive', 'suspended', 'expired') DEFAULT 'active',
  `notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `uk_client_code` (`company_id`, `client_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `client_contacts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `client_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `designation` VARCHAR(100) NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `is_primary` TINYINT(1) DEFAULT 0,
  `can_login` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `contracts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `client_id` INT UNSIGNED NOT NULL,
  `contract_number` VARCHAR(100) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `contract_value` DECIMAL(14, 2) DEFAULT 0.00,
  `billing_frequency` VARCHAR(50) DEFAULT 'monthly',
  `terms_conditions` LONGTEXT NULL,
  `document_path` VARCHAR(255) NULL,
  `status` ENUM('draft', 'active', 'expired', 'terminated', 'renewed') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `uk_contract_num` (`company_id`, `contract_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sites` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `client_id` INT UNSIGNED NOT NULL,
  `branch_id` INT UNSIGNED NULL,
  `site_code` VARCHAR(50) NOT NULL,
  `site_name` VARCHAR(200) NOT NULL,
  `site_type` ENUM('corporate_office', 'industrial_plant', 'hospital', 'hotel', 'bank', 'warehouse', 'school_college', 'residential', 'embassy', 'retail_mall', 'other') DEFAULT 'corporate_office',
  `address` TEXT NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `state` VARCHAR(100) NOT NULL,
  `pincode` VARCHAR(20) NOT NULL,
  `latitude` DECIMAL(10, 8) NOT NULL,
  `longitude` DECIMAL(11, 8) NOT NULL,
  `geofence_type` ENUM('circle', 'polygon') DEFAULT 'circle',
  `geofence_radius` INT UNSIGNED DEFAULT 75, -- in meters
  `geofence_polygon` JSON NULL, -- Array of [lat, lng] coordinates
  `contact_person` VARCHAR(150) NULL,
  `contact_phone` VARCHAR(50) NULL,
  `emergency_contact` VARCHAR(100) NULL,
  `instructions` TEXT NULL,
  `status` ENUM('active', 'inactive', 'temporary_closed') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  UNIQUE KEY `uk_site_code` (`company_id`, `site_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `site_zones` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `site_id` INT UNSIGNED NOT NULL,
  `zone_code` VARCHAR(50) NOT NULL,
  `zone_name` VARCHAR(150) NOT NULL,
  `floor_level` VARCHAR(50) NULL,
  `zone_type` ENUM('security_gate', 'washroom', 'pantry', 'server_room', 'parking', 'reception', 'corridor', 'office_floor', 'perimeter') DEFAULT 'office_floor',
  `qr_code_token` VARCHAR(100) NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `site_checkpoints` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `site_id` INT UNSIGNED NOT NULL,
  `zone_id` INT UNSIGNED NULL,
  `checkpoint_code` VARCHAR(50) NOT NULL,
  `checkpoint_name` VARCHAR(150) NOT NULL,
  `checkpoint_type` ENUM('qr', 'nfc', 'ble', 'gps') DEFAULT 'qr',
  `qr_token` VARCHAR(100) NOT NULL UNIQUE,
  `latitude` DECIMAL(10, 8) NULL,
  `longitude` DECIMAL(11, 8) NULL,
  `tolerance_radius` INT UNSIGNED DEFAULT 25,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`zone_id`) REFERENCES `site_zones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. WORKFORCE CATEGORIES, EMPLOYEES & COMPLIANCE
-- ============================================================================

CREATE TABLE IF NOT EXISTS `employee_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `department` ENUM('security', 'cleaning', 'pantry', 'facility', 'supervision', 'operations', 'hr_admin') DEFAULT 'security',
  `standard_uniform` TEXT NULL,
  `daily_allowance_default` DECIMAL(10, 2) DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `employees` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `branch_id` INT UNSIGNED NULL,
  `category_id` INT UNSIGNED NOT NULL,
  `employee_code` VARCHAR(50) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `gender` ENUM('male', 'female', 'other') DEFAULT 'male',
  `dob` DATE NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `emergency_phone` VARCHAR(50) NOT NULL,
  `email` VARCHAR(150) NULL,
  `photo` VARCHAR(255) NULL,
  `face_feature_token` VARCHAR(255) NULL, -- Encrypted / hashed reference token for biometric liveness
  `current_address` TEXT NOT NULL,
  `permanent_address` TEXT NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `state` VARCHAR(100) NOT NULL,
  `pincode` VARCHAR(20) NOT NULL,
  `joining_date` DATE NOT NULL,
  `employment_type` ENUM('full_time', 'contractual', 'temporary', 'daily_wager') DEFAULT 'full_time',
  `designation` VARCHAR(100) NOT NULL,
  `supervisor_id` INT UNSIGNED NULL,
  -- Financial & Statutory Parameters
  `basic_salary` DECIMAL(12, 2) DEFAULT 0.00,
  `daily_rate` DECIMAL(10, 2) DEFAULT 0.00,
  `hra_allowance` DECIMAL(10, 2) DEFAULT 0.00,
  `conveyance_allowance` DECIMAL(10, 2) DEFAULT 0.00,
  `special_allowance` DECIMAL(10, 2) DEFAULT 0.00,
  `bank_name` VARCHAR(100) NULL,
  `bank_account_no` VARCHAR(100) NULL,
  `ifsc_code` VARCHAR(50) NULL,
  `pf_uan` VARCHAR(50) NULL,
  `esic_no` VARCHAR(50) NULL,
  `police_verification_status` ENUM('verified', 'pending', 'rejected', 'exempted') DEFAULT 'pending',
  `medical_fitness_status` ENUM('fit', 'pending', 'unfit') DEFAULT 'fit',
  `status` ENUM('active', 'on_leave', 'terminated', 'resigned', 'suspended') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`category_id`) REFERENCES `employee_categories` (`id`),
  FOREIGN KEY (`supervisor_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  UNIQUE KEY `uk_employee_code` (`company_id`, `employee_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `employee_documents` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` INT UNSIGNED NOT NULL,
  `document_type` ENUM('aadhaar', 'pan_card', 'police_verification', 'voter_id', 'training_certificate', 'gun_license', 'driving_license', 'medical_report', 'contract', 'other') NOT NULL,
  `document_number` VARCHAR(100) NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `issue_date` DATE NULL,
  `expiry_date` DATE NULL,
  `verification_status` ENUM('pending', 'verified', 'rejected', 'expired') DEFAULT 'pending',
  `verified_by` INT UNSIGNED NULL,
  `verified_at` DATETIME NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `employee_devices` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` INT UNSIGNED NOT NULL,
  `device_uuid` VARCHAR(100) NOT NULL,
  `device_model` VARCHAR(100) NULL,
  `os_version` VARCHAR(50) NULL,
  `push_token` VARCHAR(255) NULL,
  `status` ENUM('active', 'blocked', 'pending_approval') DEFAULT 'active',
  `last_seen_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `uk_emp_device` (`employee_id`, `device_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. SHIFTS, ROSTERS & DEPLOYMENTS
-- ============================================================================

CREATE TABLE IF NOT EXISTS `shift_templates` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `break_duration_mins` INT UNSIGNED DEFAULT 60,
  `is_night_shift` TINYINT(1) DEFAULT 0,
  `grace_period_mins` INT UNSIGNED DEFAULT 15,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `shifts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `site_id` INT UNSIGNED NOT NULL,
  `template_id` INT UNSIGNED NULL,
  `name` VARCHAR(100) NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `is_night_shift` TINYINT(1) DEFAULT 0,
  `grace_period_mins` INT UNSIGNED DEFAULT 15,
  `required_guards` INT UNSIGNED DEFAULT 0,
  `required_cleaners` INT UNSIGNED DEFAULT 0,
  `required_pantry` INT UNSIGNED DEFAULT 0,
  `required_supervisors` INT UNSIGNED DEFAULT 0,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`template_id`) REFERENCES `shift_templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `employee_deployments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `employee_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `shift_id` INT UNSIGNED NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL,
  `assigned_role` VARCHAR(100) NOT NULL,
  `status` ENUM('active', 'completed', 'transferred', 'cancelled') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `shift_rosters` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `shift_id` INT UNSIGNED NOT NULL,
  `employee_id` INT UNSIGNED NOT NULL,
  `roster_date` DATE NOT NULL,
  `is_reliever` TINYINT(1) DEFAULT 0,
  `reliever_for_employee_id` INT UNSIGNED NULL,
  `status` ENUM('scheduled', 'present', 'absent', 'no_show', 'on_leave', 'replaced') DEFAULT 'scheduled',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`reliever_for_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  UNIQUE KEY `uk_roster_emp_shift_date` (`employee_id`, `shift_id`, `roster_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. ATTENDANCE ENGINE, DYNAMIC QR & VERIFICATIONS
-- ============================================================================

CREATE TABLE IF NOT EXISTS `qr_tokens` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `site_id` INT UNSIGNED NOT NULL,
  `token_signature` VARCHAR(255) NOT NULL UNIQUE,
  `nonce` VARCHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `is_used` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  INDEX `idx_qr_token_exp` (`expires_at`, `is_used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attendance` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `attendance_code` VARCHAR(50) NOT NULL UNIQUE,
  `company_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `shift_id` INT UNSIGNED NOT NULL,
  `employee_id` INT UNSIGNED NOT NULL,
  `roster_id` INT UNSIGNED NULL,
  `attendance_date` DATE NOT NULL,
  `check_in_time` DATETIME NOT NULL,
  `check_out_time` DATETIME NULL,
  `total_work_minutes` INT UNSIGNED DEFAULT 0,
  `overtime_minutes` INT UNSIGNED DEFAULT 0,
  `status` ENUM('PENDING', 'VERIFIED', 'CHECKED_IN', 'CHECKED_OUT', 'REJECTED', 'MANUAL_REVIEW', 'ADJUSTED', 'NO_SHOW') DEFAULT 'CHECKED_IN',
  `verification_score` TINYINT UNSIGNED DEFAULT 100, -- 0 to 100
  `risk_score` TINYINT UNSIGNED DEFAULT 0,         -- 0 to 100
  `is_manual_override` TINYINT(1) DEFAULT 0,
  `override_reason` TEXT NULL,
  `override_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`roster_id`) REFERENCES `shift_rosters` (`id`) ON DELETE SET NULL,
  INDEX `idx_att_date_site` (`attendance_date`, `site_id`),
  INDEX `idx_att_emp_date` (`employee_id`, `attendance_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attendance_verifications` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `attendance_id` INT UNSIGNED NOT NULL,
  `event_type` ENUM('check_in', 'check_out', 'break_start', 'break_end') DEFAULT 'check_in',
  -- Layer 1: Geofence
  `latitude` DECIMAL(10, 8) NOT NULL,
  `longitude` DECIMAL(11, 8) NOT NULL,
  `gps_accuracy` DECIMAL(8, 2) NOT NULL,
  `geofence_distance_meters` DECIMAL(8, 2) NOT NULL,
  `geofence_status` ENUM('PASS', 'FAIL', 'SUSPICIOUS') NOT NULL,
  -- Layer 2: QR Token
  `qr_token` VARCHAR(255) NULL,
  `qr_status` ENUM('VALID', 'EXPIRED', 'INVALID', 'REPLAYED', 'NOT_REQUIRED') DEFAULT 'NOT_REQUIRED',
  -- Layer 3: Selfie & Biometrics
  `selfie_path` VARCHAR(255) NULL,
  `face_match_status` ENUM('MATCH', 'NO_MATCH', 'REVIEW_REQUIRED', 'FAILED', 'BYPASSED') DEFAULT 'BYPASSED',
  `liveness_score` DECIMAL(5, 2) NULL,
  -- Device & Network Signal
  `device_id` VARCHAR(100) NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `is_offline_sync` TINYINT(1) DEFAULT 0,
  `client_timestamp` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`attendance_id`) REFERENCES `attendance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attendance_disputes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `employee_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `dispute_date` DATE NOT NULL,
  `shift_id` INT UNSIGNED NOT NULL,
  `reason` TEXT NOT NULL,
  `evidence_path` VARCHAR(255) NULL,
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `reviewed_by` INT UNSIGNED NULL,
  `review_notes` TEXT NULL,
  `reviewed_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. GUARD TOUR PATROL SYSTEM
-- ============================================================================

CREATE TABLE IF NOT EXISTS `patrol_routes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `site_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `estimated_minutes` INT UNSIGNED DEFAULT 30,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `patrol_route_checkpoints` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `route_id` INT UNSIGNED NOT NULL,
  `checkpoint_id` INT UNSIGNED NOT NULL,
  `sequence_order` INT UNSIGNED NOT NULL DEFAULT 1,
  `expected_interval_mins` INT UNSIGNED DEFAULT 10,
  `is_mandatory` TINYINT(1) DEFAULT 1,
  FOREIGN KEY (`route_id`) REFERENCES `patrol_routes` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`checkpoint_id`) REFERENCES `site_checkpoints` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `patrol_tours` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `route_id` INT UNSIGNED NOT NULL,
  `guard_id` INT UNSIGNED NOT NULL,
  `shift_id` INT UNSIGNED NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NULL,
  `total_checkpoints` INT UNSIGNED DEFAULT 0,
  `scanned_checkpoints` INT UNSIGNED DEFAULT 0,
  `missed_checkpoints` INT UNSIGNED DEFAULT 0,
  `status` ENUM('in_progress', 'completed', 'aborted', 'deviation_flagged') DEFAULT 'in_progress',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`route_id`) REFERENCES `patrol_routes` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`guard_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `patrol_scans` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tour_id` INT UNSIGNED NOT NULL,
  `checkpoint_id` INT UNSIGNED NOT NULL,
  `scan_time` DATETIME NOT NULL,
  `latitude` DECIMAL(10, 8) NULL,
  `longitude` DECIMAL(11, 8) NULL,
  `scan_method` ENUM('qr', 'nfc', 'ble', 'manual') DEFAULT 'qr',
  `status` ENUM('ON_TIME', 'LATE', 'EARLY', 'DEVIATED', 'MISSED') DEFAULT 'ON_TIME',
  `photo_path` VARCHAR(255) NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`tour_id`) REFERENCES `patrol_tours` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`checkpoint_id`) REFERENCES `site_checkpoints` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. CLEANING, PANTRY & TASK MANAGEMENT
-- ============================================================================

CREATE TABLE IF NOT EXISTS `task_templates` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `department` ENUM('cleaning', 'pantry', 'facility', 'security') DEFAULT 'cleaning',
  `frequency` ENUM('hourly', 'per_shift', 'daily', 'weekly', 'monthly') DEFAULT 'per_shift',
  `items_checklist` JSON NOT NULL, -- Array of string tasks
  `requires_qr_scan` TINYINT(1) DEFAULT 1,
  `requires_photo_evidence` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tasks` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `zone_id` INT UNSIGNED NULL,
  `template_id` INT UNSIGNED NULL,
  `assigned_employee_id` INT UNSIGNED NULL,
  `shift_id` INT UNSIGNED NULL,
  `title` VARCHAR(200) NOT NULL,
  `scheduled_date` DATE NOT NULL,
  `scheduled_time` TIME NULL,
  `status` ENUM('PENDING', 'STARTED', 'COMPLETED', 'OVERDUE', 'VERIFIED', 'REJECTED') DEFAULT 'PENDING',
  `started_at` DATETIME NULL,
  `completed_at` DATETIME NULL,
  `before_photo` VARCHAR(255) NULL,
  `after_photo` VARCHAR(255) NULL,
  `completion_notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`zone_id`) REFERENCES `site_zones` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`template_id`) REFERENCES `task_templates` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`assigned_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `consumable_inventory` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `item_name` VARCHAR(150) NOT NULL,
  `category` ENUM('chemicals', 'paper_products', 'tools', 'sanitizers', 'pantry_supplies', 'safety_gear') DEFAULT 'chemicals',
  `unit` VARCHAR(50) NOT NULL, -- Litres, Rolls, Packets, Pcs
  `current_stock` DECIMAL(10, 2) DEFAULT 0.00,
  `min_alert_level` DECIMAL(10, 2) DEFAULT 5.00,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `consumable_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `inventory_id` INT UNSIGNED NOT NULL,
  `employee_id` INT UNSIGNED NOT NULL,
  `log_type` ENUM('received', 'consumed', 'returned', 'waste_adjusted') DEFAULT 'consumed',
  `quantity` DECIMAL(10, 2) NOT NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`inventory_id`) REFERENCES `consumable_inventory` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. AUDITS, INCIDENTS & SOS PANIC CENTER
-- ============================================================================

CREATE TABLE IF NOT EXISTS `site_audits` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `audit_number` VARCHAR(50) NOT NULL UNIQUE,
  `company_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `auditor_id` INT UNSIGNED NOT NULL, -- Field officer
  `audit_type` ENUM('scheduled', 'surprise', 'client_complaint', 'night_inspection') DEFAULT 'scheduled',
  `audit_date` DATETIME NOT NULL,
  `latitude` DECIMAL(10, 8) NULL,
  `longitude` DECIMAL(11, 8) NULL,
  `guard_count_found` INT UNSIGNED DEFAULT 0,
  `cleaning_count_found` INT UNSIGNED DEFAULT 0,
  `uniform_score` TINYINT UNSIGNED DEFAULT 5,  -- 1 to 5
  `hygiene_score` TINYINT UNSIGNED DEFAULT 5,  -- 1 to 5
  `alertness_score` TINYINT UNSIGNED DEFAULT 5,-- 1 to 5
  `registers_score` TINYINT UNSIGNED DEFAULT 5,-- 1 to 5
  `overall_score` DECIMAL(5, 2) DEFAULT 100.00,
  `team_photo_path` VARCHAR(255) NULL,
  `findings_summary` TEXT NOT NULL,
  `supervisor_acknowledged` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`auditor_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `incidents` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `incident_number` VARCHAR(50) NOT NULL UNIQUE,
  `company_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `reported_by` INT UNSIGNED NOT NULL,
  `incident_type` ENUM('theft', 'fire', 'unauthorized_entry', 'equipment_damage', 'safety_hazard', 'misconduct', 'accident', 'security_breach', 'medical_emergency', 'cleaning_escalation', 'other') DEFAULT 'other',
  `severity` ENUM('LOW', 'MEDIUM', 'HIGH', 'CRITICAL') DEFAULT 'MEDIUM',
  `incident_time` DATETIME NOT NULL,
  `location_detail` VARCHAR(255) NOT NULL,
  `description` LONGTEXT NOT NULL,
  `immediate_action_taken` TEXT NULL,
  `witness_details` TEXT NULL,
  `status` ENUM('OPEN', 'ACKNOWLEDGED', 'ASSIGNED', 'INVESTIGATING', 'ACTION_TAKEN', 'RESOLVED', 'CLOSED') DEFAULT 'OPEN',
  `assigned_to` INT UNSIGNED NULL,
  `resolution_notes` TEXT NULL,
  `closed_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`reported_by`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `incident_attachments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `incident_id` INT UNSIGNED NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(50) DEFAULT 'image',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sos_alerts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sos_code` VARCHAR(50) NOT NULL UNIQUE,
  `company_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `employee_id` INT UNSIGNED NOT NULL,
  `trigger_time` DATETIME NOT NULL,
  `latitude` DECIMAL(10, 8) NOT NULL,
  `longitude` DECIMAL(11, 8) NOT NULL,
  `status` ENUM('TRIGGERED', 'ACKNOWLEDGED', 'RESPONDING', 'RESOLVED', 'FALSE_ALARM') DEFAULT 'TRIGGERED',
  `resolved_by` INT UNSIGNED NULL,
  `resolution_notes` TEXT NULL,
  `resolved_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `shift_handovers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `site_id` INT UNSIGNED NOT NULL,
  `outgoing_guard_id` INT UNSIGNED NOT NULL,
  `incoming_guard_id` INT UNSIGNED NOT NULL,
  `shift_id` INT UNSIGNED NOT NULL,
  `handover_time` DATETIME NOT NULL,
  `keys_status` TEXT NOT NULL,
  `equipment_status` TEXT NOT NULL,
  `visitor_register_count` INT UNSIGNED DEFAULT 0,
  `open_issues_notes` TEXT NULL,
  `outgoing_signature` VARCHAR(255) NULL,
  `incoming_signature` VARCHAR(255) NULL,
  `is_acknowledged` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`outgoing_guard_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`incoming_guard_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 9. HR, LEAVES & PAYROLL ENGINE
-- ============================================================================

CREATE TABLE IF NOT EXISTS `leave_types` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(20) NOT NULL,
  `annual_quota_days` INT UNSIGNED DEFAULT 12,
  `is_paid` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `leave_requests` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `employee_id` INT UNSIGNED NOT NULL,
  `leave_type_id` INT UNSIGNED NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `total_days` DECIMAL(4, 1) NOT NULL,
  `reason` TEXT NOT NULL,
  `status` ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
  `approved_by` INT UNSIGNED NULL,
  `decision_notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payroll_periods` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `month` TINYINT UNSIGNED NOT NULL, -- 1 to 12
  `year` SMALLINT UNSIGNED NOT NULL, -- e.g. 2026
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `status` ENUM('draft', 'calculated', 'hr_reviewed', 'approved', 'paid', 'locked') DEFAULT 'draft',
  `processed_by` INT UNSIGNED NULL,
  `approved_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `uk_pay_period` (`company_id`, `month`, `year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payroll_records` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `payroll_period_id` INT UNSIGNED NOT NULL,
  `employee_id` INT UNSIGNED NOT NULL,
  `payable_days` DECIMAL(5, 2) DEFAULT 0.00,
  `present_days` DECIMAL(5, 2) DEFAULT 0.00,
  `absent_days` DECIMAL(5, 2) DEFAULT 0.00,
  `paid_leaves` DECIMAL(5, 2) DEFAULT 0.00,
  `overtime_hours` DECIMAL(6, 2) DEFAULT 0.00,
  -- Earnings
  `basic_earned` DECIMAL(12, 2) DEFAULT 0.00,
  `hra_earned` DECIMAL(10, 2) DEFAULT 0.00,
  `overtime_pay` DECIMAL(10, 2) DEFAULT 0.00,
  `night_allowance` DECIMAL(10, 2) DEFAULT 0.00,
  `special_allowance_earned` DECIMAL(10, 2) DEFAULT 0.00,
  `gross_pay` DECIMAL(12, 2) DEFAULT 0.00,
  -- Deductions
  `pf_deduction` DECIMAL(10, 2) DEFAULT 0.00,
  `esic_deduction` DECIMAL(10, 2) DEFAULT 0.00,
  `professional_tax` DECIMAL(10, 2) DEFAULT 0.00,
  `uniform_deduction` DECIMAL(10, 2) DEFAULT 0.00,
  `advance_deduction` DECIMAL(10, 2) DEFAULT 0.00,
  `other_deductions` DECIMAL(10, 2) DEFAULT 0.00,
  `total_deductions` DECIMAL(12, 2) DEFAULT 0.00,
  -- Net
  `net_pay` DECIMAL(12, 2) DEFAULT 0.00,
  `payment_status` ENUM('unpaid', 'processed', 'paid', 'hold') DEFAULT 'unpaid',
  `payment_mode` VARCHAR(50) DEFAULT 'bank_transfer',
  `transaction_ref` VARCHAR(100) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `uk_emp_payroll_period` (`payroll_period_id`, `employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 10. CLIENT BILLING, CONTRACT SLA & PENALTIES
-- ============================================================================

CREATE TABLE IF NOT EXISTS `client_invoices` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
  `company_id` INT UNSIGNED NOT NULL,
  `client_id` INT UNSIGNED NOT NULL,
  `contract_id` INT UNSIGNED NULL,
  `billing_month` TINYINT UNSIGNED NOT NULL,
  `billing_year` SMALLINT UNSIGNED NOT NULL,
  `issue_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `subtotal` DECIMAL(14, 2) NOT NULL,
  `gst_percentage` DECIMAL(5, 2) DEFAULT 18.00,
  `gst_amount` DECIMAL(12, 2) NOT NULL,
  `penalty_deductions` DECIMAL(12, 2) DEFAULT 0.00,
  `grand_total` DECIMAL(14, 2) NOT NULL,
  `status` ENUM('draft', 'sent', 'paid', 'partially_paid', 'overdue', 'cancelled') DEFAULT 'draft',
  `payment_date` DATE NULL,
  `payment_reference` VARCHAR(100) NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `deployed_shifts_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `rate_per_shift` DECIMAL(10, 2) NOT NULL,
  `amount` DECIMAL(12, 2) NOT NULL,
  FOREIGN KEY (`invoice_id`) REFERENCES `client_invoices` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sla_rules` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `client_id` INT UNSIGNED NULL,
  `rule_name` VARCHAR(150) NOT NULL,
  `metric_type` ENUM('attendance_percentage', 'patrol_completion', 'cleaning_score', 'incident_response_mins', 'reliever_dispatch_mins') NOT NULL,
  `target_threshold` DECIMAL(6, 2) NOT NULL,
  `penalty_type` ENUM('fixed_amount', 'percentage_bill') DEFAULT 'fixed_amount',
  `penalty_value` DECIMAL(10, 2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sla_breaches` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `sla_rule_id` INT UNSIGNED NOT NULL,
  `breach_date` DATE NOT NULL,
  `actual_value` DECIMAL(8, 2) NOT NULL,
  `calculated_penalty` DECIMAL(10, 2) DEFAULT 0.00,
  `status` ENUM('flagged', 'approved_for_deduction', 'waived', 'disputed') DEFAULT 'flagged',
  `waiver_reason` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`sla_rule_id`) REFERENCES `sla_rules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 11. NOTIFICATIONS, AUDIT TRAILS & CONFIGURATIONS
-- ============================================================================

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `title` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `type` ENUM('info', 'alert', 'emergency_sos', 'incident', 'no_show', 'payroll') DEFAULT 'info',
  `channel` ENUM('in_app', 'whatsapp', 'email', 'sms') DEFAULT 'in_app',
  `action_url` VARCHAR(255) NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `read_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
  `key_name` VARCHAR(100) NOT NULL,
  `key_value` LONGTEXT NULL,
  `field_type` VARCHAR(30) DEFAULT 'text',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `uk_comp_setting_key` (`company_id`, `key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NULL,
  `user_id` INT UNSIGNED NULL,
  `user_name` VARCHAR(150) NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NULL,
  `description` TEXT NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  INDEX `idx_wf_audit_time` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
