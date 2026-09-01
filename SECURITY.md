# JMJ Enterprises Solutions - Security Architecture & Hardening Guide

This document outlines the defensive measures, authentication mechanisms, and vulnerability countermeasures implemented across the JMJ Enterprises application stack.

---

## 🛡️ Core Defense Implementations

### 1. SQL Injection Prevention
* All database interactions are executed strictly through **PDO prepared statements** with bound parameters via `\Core\Database`.
* Direct string concatenation in SQL queries is prohibited across all models and services.

### 2. Cross-Site Scripting (XSS) Mitigation
* All dynamic user and database outputs are filtered using the global `e()` helper:
  ```php
  htmlspecialchars((string)$string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
  ```
* Rich HTML content is vetted and rendered strictly in designated article containers.

### 3. Cross-Site Request Forgery (CSRF)
* High-entropy cryptographic tokens (`bin2hex(random_bytes(32))`) generated via `\Core\Csrf`.
* Token verification is mandatory for all state-mutating requests (`POST`, `PUT`, `DELETE`).

### 4. Brute-Force & Rate Limiting Defense
* `\Core\RateLimiter` enforces window-based attempt limits:
  * **Admin Authentication:** 5 failed attempts per 5 minutes per IP.
  * **Inbound Quote / Lead Submissions:** 10 submissions per 5 minutes per IP.

### 5. Session Hardening & Authentication
* Passwords hashed using modern **Bcrypt (`PASSWORD_BCRYPT`)**.
* Secure session cookies:
  * `httponly = true`
  * `samesite = 'Lax'`
  * `secure = true` (in production)
* Session regeneration on login (`session_regenerate_id(true)`) to prevent session fixation.

### 6. File Upload Validation
* `\Services\MediaService` validates file extensions against a strict whitelist (`jpg`, `jpeg`, `png`, `webp`, `gif`, `pdf`, `doc`, `docx`).
* Strict MIME type inspection using `finfo_file()`.
* Files are renamed to unique UUID-based filenames to prevent path traversal and script execution in upload directories.

### 7. Immutable Security Audit Trail
* `\Services\AuditService` records all critical administrative events into the `audit_logs` table:
  * User ID
  * Event Action (`LOGIN`, `CREATE`, `UPDATE`, `ARCHIVE`, `RESTORE`, `PURGE`)
  * Entity & Entity ID
  * IP Address & User Agent
  * Timestamp
