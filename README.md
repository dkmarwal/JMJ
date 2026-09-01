# JMJ Enterprises Solutions Ltd. — Enterprise Portal & CMS

A high-performance, custom-architected corporate web application and content management system for **JMJ Enterprises Solutions Ltd.** (Est. 2013), India's premier B2B Security and Commercial Cleaning contractor.

Built with **PHP 8+**, **MySQL 8+**, **Tailwind CSS**, **HTML5**, and zero-framework MVC/Service architecture.

---

## 🏢 Corporate Profile
* **Company:** JMJ Enterprises Solutions Ltd.
* **Headquarters:** 250, Sant Nagar, East of Kailash, New Delhi – 110065
* **Toll-Free Helpline:** 18008890832
* **Primary Phone:** +91-9999381777 | **Landline:** 011-41037091
* **Emails:** `jmjsanu@gmail.com` | `info@jmjenterprisessolutions.com`
* **Compliance:** PSARA Act Compliant • ISO 9001:2015 • Police Vetted Personnel
* **National Footprint:** 10 Strategic State Regional Hubs (Delhi NCR, Haryana, UP, Karnataka, Maharashtra, Tamil Nadu, Telangana, West Bengal, MP, Punjab)

---

## 🚀 Key Highlights & Architectural Features

### 1. Zero-Framework Modular MVC & Services Architecture
* **Fast & Lightweight:** Zero bloated dependencies; instantaneous TTFB under 50ms on standard PHP-FPM.
* **Service Layer:** Decoupled business logic (`BlogService`, `ServiceService`, `LeadService`, `SeoService`, `MediaService`, `AuditService`, `SettingService`).
* **PDO Wrapper:** Singleton database connection with automatic failover between MySQL ports (`3308`, `3306`, `3307`).

### 2. Comprehensive 26-Service Catalog
* **12 Security Services:** Bank Security Guards, Corporate Security, Hospital Security, Industrial Security, Commercial Security, Armed Guards, Event Security, Residential Security, Bouncer Services, Lady Security Guards, CCTV Surveillance, Escort Guards.
* **14 Cleaning Services:** Office Cleaning, Hospital Cleaning, Hotel Cleaning, Floor Stripping & Waxing, Deep Cleaning, School Cleaning, Industrial Cleaning, Facade Cleaning, Post Construction Cleaning, Restaurant Cleaning, Warehouse Cleaning, Carpet & Upholstery Cleaning, Washroom Hygiene, Kitchen Cleaning.

### 3. Hawks Infotech Inspired Admin Blog Desk & CMS
* **Blog Studio:** Rich WYSIWYG editor with live Google SERP snippet preview, reading time calculator, and featured image uploader.
* **Revision Vault & Rollback:** Automatic version snapshotting on every article update with one-click restore.
* **Unified Archive & Recovery Vault:** Central soft-delete management with recovery and permanent purge across all entities.
* **CRM & Leads Management:** Inbound quote submissions, survey requests, CSV data exports, and status workflow.

### 4. Advanced Technical SEO & Structured Schema Markup
* **Dynamic XML Sitemaps:** `sitemap.xml`, `blog-sitemap.xml`, `service-sitemap.xml`.
* **JSON-LD Schema Markup:** Organization Schema, Service Schema, Article Schema, FAQPage Schema, and BreadcrumbList Schema.
* **OpenGraph & Twitter Cards:** Preconfigured social sharing headers on every public page.

---

## 📁 Directory Structure

```
c:\xampp\htdocs\jmj\
├── config/
│   ├── config.php          # Autoloading, Constants & Global Bootstrap
│   ├── .env                # Local Database & App Environment Keys
│   └── .env.example        # Production Environment Blueprint
├── core/
│   ├── App.php             # Application Lifecycle Controller
│   ├── Auth.php            # RBAC & Session Authentication Engine
│   ├── Csrf.php            # CSRF Token Generator & Validator
│   ├── Database.php        # PDO Database Singleton with Port Auto-Discovery
│   ├── Helpers.php         # Global Utilities (e(), url(), slugify(), asset())
│   ├── RateLimiter.php     # Rate Limiter for Inbound Forms & Auth
│   ├── Router.php          # Clean Regex URI Router
│   ├── Session.php         # Secure Session Manager & Flash Messaging
│   ├── Validator.php       # Server-Side Input Validator
│   └── View.php            # Layout & View Template Renderer
├── controllers/
│   ├── HomeController.php      # Homepage Controller
│   ├── AboutController.php     # Corporate Profile & PSARA Heritage
│   ├── ServiceController.php   # Security & Cleaning Hubs + Dynamic Detail Pages
│   ├── BlogController.php      # Blog Magazine & Article Reader
│   ├── GalleryController.php   # Operations Portfolio Showcase
│   ├── ContactController.php   # Contact Points & Google Map
│   ├── QuoteController.php     # Interactive Quotation Engine
│   ├── PageController.php      # Privacy, Terms & Custom 404
│   └── ApiController.php       # AJAX Endpoints for Quotes, Leads, Newsletter
├── models/
│   ├── Service.php         # Service Data Model & Queries
│   ├── BlogPost.php        # Blog Post Model & Taxonomy
│   ├── Category.php        # Category & Tag Model
│   ├── User.php            # User & Role Model
│   ├── Enquiry.php         # Inbound Leads Model
│   ├── Media.php           # File & Asset Model
│   ├── Gallery.php         # Showcase Item Model
│   ├── Faq.php             # Global FAQ Model
│   └── Testimonial.php     # Client Reviews Model
├── services/
│   ├── BlogService.php     # Blog Business Logic
│   ├── ServiceService.php  # Service Catalog Logic
│   ├── LeadService.php     # Lead Processing & Notification
│   ├── SeoService.php      # Schema.org & Meta Engine
│   ├── MediaService.php    # Upload Processing & Mime Vetting
│   ├── AuditService.php    # Security Audit Trail Logger
│   ├── SettingService.php  # Global Dynamic Settings Engine
│   └── MailService.php     # Email Notification Handler
├── views/
│   ├── layouts/
│   │   ├── main.php        # Master HTML5 Shell & Tailwind Setup
│   │   ├── header.php      # Sticky Navigation & Mega Menus
│   │   └── footer.php      # 10-State Footprint & Structured Footer
│   ├── partials/
│   │   ├── breadcrumb.php  # Breadcrumb Navigation with JSON-LD
│   │   ├── cta_banner.php  # Reusable Conversion Banner
│   │   ├── quote_modal.php # Global AJAX Quick Quote Modal
│   │   └── toast.php       # Dynamic Toast Alert Dispatcher
│   ├── home/index.php      # 12-Section Homepage
│   ├── about/index.php     # About Us View
│   ├── services/           # Services Hubs & Detail Template
│   ├── blog/               # Blog Listing & Article View
│   ├── gallery/            # Showcase View
│   ├── contact/            # Contact View
│   ├── quote/              # Dedicated Quote Request View
│   └── pages/              # Privacy, Terms & 404 Views
├── admin/
│   ├── login.php           # Secure Admin Authentication
│   ├── logout.php          # Session Destruction
│   ├── dashboard.php       # Executive Metric Dashboard
│   ├── blogs.php           # Blog Desk Publications List
│   ├── blog-editor.php     # WYSIWYG Blog Editor & SEO Preview
│   ├── categories.php      # Category Manager
│   ├── tags.php            # Tag Manager
│   ├── services.php        # Service Catalog Manager
│   ├── service-editor.php  # Service Capability Editor
│   ├── enquiries.php       # Leads & CRM Desk + CSV Export
│   ├── gallery.php         # Portfolio Manager
│   ├── faqs.php            # FAQs Content Engine
│   ├── testimonials.php    # Client Reviews Manager
│   ├── media.php           # Media Library Uploader
│   ├── users.php           # Staff RBAC Management
│   ├── seo.php             # Global Route Metadata Manager
│   ├── settings.php        # System Configurations
│   ├── audit-logs.php      # Security Audit Trail
│   ├── archive.php         # Archive & Recovery Vault
│   └── partials/           # Admin Header, Sidebar, Footer
├── database/
│   ├── schema.sql          # Full MySQL 8+ Schema DDL
│   └── setup.php           # Automatic Migration & Seeder Script
├── assets/
│   ├── css/custom.css      # Brand Stylesheet & Typography
│   ├── css/admin.css       # Admin Dashboard Stylesheet
│   ├── js/main.js          # Public JavaScript & Modal Engine
│   ├── js/admin.js         # Admin JavaScript & Slug Generator
│   └── js/editor.js        # WYSIWYG Editor Script
├── uploads/                # Dynamic Media Storage Directory
├── index.php               # Front Controller & Route Registry
├── sitemap.php             # Dynamic XML Sitemap Generator
├── robots.txt              # Search Engine Indexing Directives
└── .htaccess               # Apache URL Rewriting & Security Headers
```

---

## ⚙️ Installation & Setup Instructions

### 1. Database Setup
Ensure MySQL / MariaDB is active on your server (Port 3308 or 3306).
Execute the automated database setup and seeder script:
```bash
php database/setup.php
```

### 2. Environment Configuration
Verify your `config/.env` file:
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/jmj

DB_HOST=127.0.0.1
DB_PORT=3308
DB_NAME=jmj_enterprise_db
DB_USER=root
DB_PASS=
```

### 3. Default Administrative Credentials
Access the Administration Portal at `http://localhost/jmj/admin/login.php`:
* **Super Admin Email:** `admin@jmjenterprises.com`
* **Super Admin Password:** `Admin@123456`
* **Editor Email:** `editor@jmjenterprises.com`
* **Editor Password:** `Editor@123456`

---

## 🔒 Security Posture
* **CSRF Token Verification** on every POST mutation.
* **Strict Parameterized Queries** via PDO prepared statements.
* **Role-Based Access Control (RBAC)** across administrative routes.
* **Brute-Force Rate Limiting** on authentication and inbound contact forms.
* **Audit Trail** capturing timestamp, user ID, IP address, action, and entity.
