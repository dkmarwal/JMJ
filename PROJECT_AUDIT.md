# JMJ Enterprises Solutions - Project Audit & Inspection Report

**Date:** August 31, 2026  
**Auditor / Architect:** Senior Full-Stack PHP Developer & Security Architect  
**Project:** JMJ Enterprises Solutions Corporate Portal & CMS Rebuild (`https://jmjenterprisessolutions.com/`)

---

## 1. Executive Summary

JMJ Enterprises Solutions is a premier B2B and institutional security and facility management organization founded in 2013, headquartered in New Delhi, India. The company operates across 10 strategic Indian state hubs (Delhi NCR, Haryana/Gurgaon, UP/Noida, Karnataka/Bangalore, Maharashtra/Mumbai, Tamil Nadu, Telangana, West Bengal, Madhya Pradesh, Punjab) delivering:
1. **Manned & Tactical Security Services** (PSARA Compliant, Corporate, Embassies, Banking/ATMs, Hospitals, Industrial, Lady Officers, Educational, CCTV).
2. **Specialized Commercial & Industrial Cleaning** (Hospital Pathogen Sanitization, Industrial Floor Waxing & Scrubbing, Post-Construction, Corporate Office Housekeeping, Facade Rope Access, Carpets & Upholstery).

The existing legacy web footprint consists of a single-page prototype (`index.html`) in local storage and a WordPress/RankMath legacy installation on the live domain. This complete rebuild transforms the platform into a robust, high-performance, modular **PHP 8+ / MySQL 8+ / Tailwind CSS** enterprise portal featuring an advanced Service CMS, an executive Blog Desk CMS (inspired by Hawks Infotech Blog Desk with draft workflows, revisions, and archive recovery vault), unified Lead/Quote/Contact CRM, Gallery CMS, FAQ & Testimonial engines, and global SEO/audit controls.

---

## 2. Technical Environment & Hosting Audit

| Parameter | Detected Specification | Recommendation for Rebuild |
| :--- | :--- | :--- |
| **PHP Version** | PHP 8.4.11 (CLI & XAMPP Apache Module) | Target PHP 8.1+ strictly with typed properties, match expressions, nullsafe operators |
| **Database Engine** | MySQL 8.0 / MariaDB 10.4 (Port 3308 & 3306) | MySQL 8.0+ with InnoDB, `utf8mb4_unicode_ci`, Foreign Keys, Prepared PDO Statements |
| **Web Server** | Apache 2.4 (XAMPP Environment) | Clean `.htaccess` URL rewrite engine for SEO friendly slugs without query params |
| **CSS Framework** | Tailwind CSS (CDN / Modern Tailwind 3.4+) | Reusable Tailwind design tokens (Midnight Obsidian `#090F1C`, Deep Navy `#0F1E36`, Security Gold `#F39C12`, Slate Steel `#254E70`) |
| **Typography & Icons** | Plus Jakarta Sans / FontAwesome 6 Pro | High-performance Google Fonts preconnect + FontAwesome 6 icons |
| **Authentication** | None (Static HTML previously) | Role-Based Access Control (RBAC: Super Admin, Admin, Editor, Author) with `password_hash()` |

---

## 3. Existing Content, Assets & URL Inventory

### 3.1 Existing Branding & Assets
* **Logo:** `img/logo.jpg` - JMJ Enterprises Solutions emblem with Shield and Star crest.
* **Corporate Photography:**
  * `img/security.JPG` - Manned security patrol detail.
  * `img/hospital.JPG` - Hospital facility management and sanitization team.
* **Corporate Details:**
  * **Entity:** JMJ Enterprises Solutions Ltd.
  * **Headquarters:** 250, Sant Nagar, East of Kailash, New Delhi – 110065.
  * **Direct Line:** 011-41037091 / +91-9999381777.
  * **Toll-Free:** 18008890832.
  * **Emergency Operations Email:** `jmjsanu@gmail.com` / `info@jmjenterprisessolutions.com`.
  * **Dispatch:** 24/7 Central Operations Control.

### 3.2 Service Catalog Taxonomy to be Migrated

#### Security Services (12 Dedicated Endpoints):
1. `/security-services/corporate-security/` - Corporate Offices Security Guard Services
2. `/security-services/atm-security/` - ATM Cash Transit & Kiosk Security
3. `/security-services/lady-security-officers/` - Lady Security Officers & Executive Protection
4. `/security-services/delhi-security-guard/` - Security Guard Company in Delhi & NCR
5. `/security-services/embassy-security/` - Diplomatic & Embassy Security Details
6. `/security-services/industrial-security/` - Heavy Industrial & Factory Security Guarding
7. `/security-services/hotel-security/` - Hospitality & Hotel Manned Guarding
8. `/security-services/educational-security/` - Educational Institutions, Schools & Campus Security
9. `/security-services/hospital-security/` - Healthcare Network & Emergency Ward Security
10. `/security-services/residential-security/` - Gated Community & Luxury Home Security
11. `/security-services/mnc-security/` - Multinational Corporate Roster Security
12. `/security-services/cctv-security/` - CCTV Surveillance & Digital Integrated Solutions

#### Cleaning & Facility Services (14 Dedicated Endpoints):
1. `/cleaning-services/industrial-cleaning/` - Heavy Industrial Plant & Warehouse Sanitization
2. `/cleaning-services/hospital-cleaning/` - Hospital & Clinical Bio-Decontamination
3. `/cleaning-services/restaurant-cleaning/` - Commercial Kitchen & Restaurant Degreasing
4. `/cleaning-services/commercial-cleaning/` - Commercial High-Rise & Mall Housekeeping
5. `/cleaning-services/floor-waxing/` - Industrial Floor Stripping, Waxing & Sealants
6. `/cleaning-services/professional-floor-cleaning/` - Marble, Granite & Hard Floor Restoration
7. `/cleaning-services/post-construction-cleaning/` - Deep Post-Construction Debris & Stain Removal
8. `/cleaning-services/office-cleaning/` - Daily Corporate Office Housekeeping Services
9. `/cleaning-services/tile-grout-cleaning/` - Deep Chemical Tile & Grout Restoration
10. `/cleaning-services/carpet-cleaning/` - Commercial Extraction & Steam Carpet Sanitization
11. `/cleaning-services/window-cleaning/` - High-Altitude Facade & Window Cleaning
12. `/cleaning-services/move-out-cleaning/` - Comprehensive Move-In / Move-Out Deep Cleans
13. `/cleaning-services/domestic-cleaning/` - Luxury Villa & Residential Deep Cleaning
14. `/cleaning-services/upholstery-cleaning/` - Office Chair, Sofa & Fabric Panel Sanitization

---

## 4. Key Deficiencies in Legacy Prototype

1. **Monolithic Static HTML:** No database backend; content changes required editing raw code.
2. **No Content Management System:** Inability to add blog articles, manage services, upload gallery items, or edit FAQs.
3. **No Lead Capture Storage:** Forms in `index.html` were non-functional placeholders (`action="#"`).
4. **Missing Individual SEO Pages:** Search engines could not index individual high-intent keyword pages (e.g. `industrial security guard in delhi`).
5. **No Administration Security:** Lack of authentication, CSRF validation, audit logging, and input sanitization.

---

## 5. Rebuild Target Architecture

A modular, framework-free, highly maintainable MVC/Service-oriented PHP architecture structured for high performance, zero framework overhead, maximum security, and full CMS empowerments.
