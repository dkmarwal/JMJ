# JMJ Enterprises Solutions - Database Documentation

This document outlines the database schema, relational structure, indexing strategies, and soft-delete mechanics for `jmj_enterprise_db`.

---

## 🗄️ Database Specifications
* **DBMS:** MySQL 8.0+ / MariaDB 10.4+
* **Database Name:** `jmj_enterprise_db`
* **Default Charset:** `utf8mb4`
* **Default Collation:** `utf8mb4_unicode_ci`
* **Storage Engine:** `InnoDB` (ACID Compliant)

---

## 📊 Relational Architecture & Tables

### 1. Security & RBAC
* **`roles`**: Defines system roles (`super_admin`, `admin`, `editor`, `author`).
* **`permissions`**: Granular capabilities (`manage_blogs`, `manage_services`, `manage_settings`, `manage_users`, etc.).
* **`role_permissions`**: Many-to-many junction between roles and permissions.
* **`users`**: Administrative personnel credentials, bcrypt password hashes, and profile avatars.

### 2. Service Management
* **`service_categories`**: Core service taxonomies (`security-services`, `cleaning-services`, `facility-support`).
* **`services`**: Master service catalog with slug, overview, methodology, target sectors, standards, and hero photography.
* **`service_features`**: Specific capability highlights per service.
* **`service_faqs`**: Service-specific FAQ entries rendered with FAQPage JSON-LD schema.

### 3. Hawks Infotech Blog Desk
* **`blog_categories`**: Topic clusters for industry intelligence.
* **`blog_tags`**: Granular keywords for search filtering.
* **`blog_posts`**: Comprehensive publication records with rich HTML content, reading time, and author relationships.
* **`blog_post_tags`**: Post-to-tag relationship mappings.
* **`blog_revisions`**: Immutable revision snapshots storing post history and rollback states.

### 4. Operations Showcase & CRM
* **`gallery_categories`**: Category filters for deployment photos.
* **`gallery`**: Field photography records and captions.
* **`testimonials`**: Client reviews, company names, ratings, and avatars.
* **`faqs`**: Global accordion questions and answers.
* **`enquiries`**: Inbound lead management table capturing quote requests and survey mandates.
* **`newsletter_subscribers`**: Email subscription records.
* **`media`**: Uploaded file metadata, mime types, and storage paths.

### 5. Governance & System
* **`settings`**: Dynamic corporate parameters, contact channels, and statistics.
* **`seo_metadata`**: Static route metadata configurations.
* **`audit_logs`**: Chronological administrative security audit trail.

---

## ♻️ Soft-Delete Architecture (Archive Vault)
All primary entities incorporate:
* `is_archived` TINYINT(1) DEFAULT 0
* `archived_at` DATETIME NULL
* `archived_by` INT UNSIGNED NULL

Active application queries filter by `WHERE is_archived = 0`. The `/admin/archive.php` portal provides a unified recovery vault to restore or permanently purge archived records.

---

## 🛠️ Re-Seeding the Database
To reset and re-seed the full catalog:
```bash
php database/setup.php
```
