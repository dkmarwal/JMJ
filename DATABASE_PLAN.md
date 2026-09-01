# JMJ Enterprises Solutions - Database Plan & Schema Design

**Database Name:** `jmj_enterprise_db`  
**Charset:** `utf8mb4`  
**Collation:** `utf8mb4_unicode_ci`  
**Engine:** `InnoDB`  

---

## 1. Relational Entity Overview

```
                      +-------------------+
                      |      roles        |
                      +---------+---------+
                                | 1:N
                      +---------v---------+
                      |      users        |
                      +----+---------+----+
                           |         |
                  +--------+         +--------+
                  | 1:N                       | 1:N
        +---------v---------+       +---------v---------+
        |    audit_logs     |       |    blog_posts     |
        +-------------------+       +----+----+----+----+
                                         |    |    |
           +-----------------------------+    |    +-----------------------------+
           | 1:N                              | M:N                              | 1:N
+----------v----------+             +---------v---------+             +----------v----------+
|   blog_revisions    |             |  blog_post_tags   |             |     comments        |
+---------------------+             +---------+---------+             +---------------------+
                                              |
                                    +---------v---------+
                                    |     blog_tags     |
                                    +-------------------+

+-----------------------+           +-----------------------+         +-----------------------+
|  service_categories   | 1:N       |       services        | 1:N     |    service_faqs /     |
+-----------+-----------+---------->+-----------+-----------+-------->|   service_features    |
            |                       |           |                     +-----------------------+
            |                       |           | 1:N
            |                       |       +---v-------------------+
            |                       |       |    service_images     |
            |                       |       +-----------------------+
            |                       |
            +-----------------------+

+-----------------------+           +-----------------------+         +-----------------------+
|  gallery_categories   | 1:N       |       enquiries       |         |       settings        |
+-----------+-----------+---------->| (Leads, Quotes, Form) |         | (Global Config & SEO) |
            |                       +-----------------------+         +-----------------------+
+-----------v-----------+           +-----------------------+         +-----------------------+
|        gallery        |           |     testimonials      |         |     seo_metadata      |
+-----------------------+           +-----------------------+         +-----------------------+
```

---

## 2. Table Specifications

### 2.1 RBAC & User Security

#### `roles`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `name` VARCHAR(50) NOT NULL UNIQUE (`super_admin`, `admin`, `editor`, `author`)
* `label` VARCHAR(100) NOT NULL
* `description` TEXT NULL
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

#### `permissions`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `slug` VARCHAR(100) NOT NULL UNIQUE (`posts.view`, `posts.create`, `posts.edit`, `posts.publish`, `posts.delete`, `services.manage`, `categories.manage`, `media.manage`, `leads.manage`, `users.manage`, `settings.manage`, `seo.manage`, `audit.view`)
* `label` VARCHAR(150) NOT NULL
* `group_name` VARCHAR(50) NOT NULL

#### `role_permissions`
* `role_id` INT UNSIGNED NOT NULL (FK -> `roles.id` ON DELETE CASCADE)
* `permission_id` INT UNSIGNED NOT NULL (FK -> `permissions.id` ON DELETE CASCADE)
* PRIMARY KEY (`role_id`, `permission_id`)

#### `users`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `role_id` INT UNSIGNED NOT NULL (FK -> `roles.id`)
* `name` VARCHAR(100) NOT NULL
* `email` VARCHAR(150) NOT NULL UNIQUE
* `password_hash` VARCHAR(255) NOT NULL
* `avatar` VARCHAR(255) NULL
* `bio` TEXT NULL
* `phone` VARCHAR(30) NULL
* `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active'
* `is_archived` TINYINT(1) DEFAULT 0
* `archived_at` DATETIME NULL
* `archived_by` INT UNSIGNED NULL
* `last_login_at` DATETIME NULL
* `last_login_ip` VARCHAR(45) NULL
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
* `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

---

### 2.2 Services CMS Engine

#### `service_categories`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `name` VARCHAR(100) NOT NULL
* `slug` VARCHAR(100) NOT NULL UNIQUE (`security-services`, `cleaning-services`, `facility-management`)
* `icon` VARCHAR(50) DEFAULT 'fas fa-shield-halved'
* `short_description` TEXT NULL
* `display_order` INT DEFAULT 0
* `status` ENUM('active', 'inactive') DEFAULT 'active'
* `is_archived` TINYINT(1) DEFAULT 0
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

#### `services`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `category_id` INT UNSIGNED NOT NULL (FK -> `service_categories.id`)
* `name` VARCHAR(255) NOT NULL
* `slug` VARCHAR(255) NOT NULL UNIQUE
* `short_summary` TEXT NOT NULL
* `overview` LONGTEXT NOT NULL
* `hero_image` VARCHAR(255) NULL
* `icon` VARCHAR(50) DEFAULT 'fas fa-shield-alt'
* `target_sectors` TEXT NULL (JSON array or comma list: Banking, Healthcare, MNCs, Industrial, Hotels)
* `methodology` LONGTEXT NULL
* `standards_compliance` TEXT NULL (PSARA, ISO, OSHA, Bio-sanitization)
* `meta_title` VARCHAR(255) NULL
* `meta_description` TEXT NULL
* `meta_keywords` TEXT NULL
* `canonical_url` VARCHAR(255) NULL
* `is_featured` TINYINT(1) DEFAULT 0
* `status` ENUM('draft', 'published', 'archived') DEFAULT 'published'
* `display_order` INT DEFAULT 0
* `views` INT UNSIGNED DEFAULT 0
* `is_archived` TINYINT(1) DEFAULT 0
* `archived_at` DATETIME NULL
* `archived_by` INT UNSIGNED NULL
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
* `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
* INDEX (`category_id`, `status`), INDEX (`slug`)

#### `service_features`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `service_id` INT UNSIGNED NOT NULL (FK -> `services.id` ON DELETE CASCADE)
* `title` VARCHAR(255) NOT NULL
* `description` TEXT NOT NULL
* `icon` VARCHAR(50) DEFAULT 'fas fa-check-circle'
* `display_order` INT DEFAULT 0

#### `service_faqs`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `service_id` INT UNSIGNED NOT NULL (FK -> `services.id` ON DELETE CASCADE)
* `question` VARCHAR(255) NOT NULL
* `answer` TEXT NOT NULL
* `display_order` INT DEFAULT 0

#### `service_images`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `service_id` INT UNSIGNED NOT NULL (FK -> `services.id` ON DELETE CASCADE)
* `image_path` VARCHAR(255) NOT NULL
* `caption` VARCHAR(255) NULL
* `alt_text` VARCHAR(255) NULL
* `is_primary` TINYINT(1) DEFAULT 0
* `display_order` INT DEFAULT 0

---

### 2.3 Blog CMS & Revision Engine

#### `blog_categories`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `name` VARCHAR(100) NOT NULL
* `slug` VARCHAR(100) NOT NULL UNIQUE
* `description` TEXT NULL
* `meta_title` VARCHAR(255) NULL
* `meta_description` TEXT NULL
* `status` ENUM('active', 'inactive') DEFAULT 'active'
* `is_archived` TINYINT(1) DEFAULT 0
* `archived_at` DATETIME NULL
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

#### `blog_tags`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `name` VARCHAR(100) NOT NULL
* `slug` VARCHAR(100) NOT NULL UNIQUE
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

#### `blog_posts`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `title` VARCHAR(255) NOT NULL
* `slug` VARCHAR(255) NOT NULL UNIQUE
* `author_id` INT UNSIGNED NOT NULL (FK -> `users.id`)
* `category_id` INT UNSIGNED NOT NULL (FK -> `blog_categories.id`)
* `featured_image` VARCHAR(255) NULL
* `short_description` TEXT NOT NULL
* `content` LONGTEXT NOT NULL
* `reading_time` INT DEFAULT 3
* `meta_title` VARCHAR(255) NULL
* `meta_description` TEXT NULL
* `meta_keywords` TEXT NULL
* `focus_keyword` VARCHAR(150) NULL
* `canonical_url` VARCHAR(255) NULL
* `og_image` VARCHAR(255) NULL
* `schema_markup` TEXT NULL
* `status` ENUM('draft', 'pending_review', 'scheduled', 'published', 'archived') DEFAULT 'draft'
* `publish_at` DATETIME NULL
* `views` INT UNSIGNED DEFAULT 0
* `is_featured` TINYINT(1) DEFAULT 0
* `is_archived` TINYINT(1) DEFAULT 0
* `archived_at` DATETIME NULL
* `archived_by` INT UNSIGNED NULL
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
* `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
* INDEX (`status`, `publish_at`), INDEX (`slug`), INDEX (`category_id`)

#### `blog_post_tags`
* `post_id` INT UNSIGNED NOT NULL (FK -> `blog_posts.id` ON DELETE CASCADE)
* `tag_id` INT UNSIGNED NOT NULL (FK -> `blog_tags.id` ON DELETE CASCADE)
* PRIMARY KEY (`post_id`, `tag_id`)

#### `blog_revisions`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `post_id` INT UNSIGNED NOT NULL (FK -> `blog_posts.id` ON DELETE CASCADE)
* `user_id` INT UNSIGNED NOT NULL (FK -> `users.id`)
* `title` VARCHAR(255) NOT NULL
* `short_description` TEXT NULL
* `content` LONGTEXT NOT NULL
* `revision_notes` VARCHAR(255) NULL
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

---

### 2.4 Media Library & Assets

#### `media`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `user_id` INT UNSIGNED NOT NULL (FK -> `users.id`)
* `filename` VARCHAR(255) NOT NULL
* `original_filename` VARCHAR(255) NOT NULL
* `file_path` VARCHAR(255) NOT NULL
* `file_type` VARCHAR(50) NOT NULL
* `mime_type` VARCHAR(100) NOT NULL
* `file_size` INT UNSIGNED NOT NULL
* `width` INT UNSIGNED NULL
* `height` INT UNSIGNED NULL
* `alt_text` VARCHAR(255) NULL
* `caption` VARCHAR(255) NULL
* `folder` VARCHAR(50) DEFAULT 'general'
* `is_archived` TINYINT(1) DEFAULT 0
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
* INDEX (`folder`), INDEX (`mime_type`)

---

### 2.5 Gallery, FAQs, Testimonials

#### `gallery_categories`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `name` VARCHAR(100) NOT NULL
* `slug` VARCHAR(100) NOT NULL UNIQUE
* `status` ENUM('active', 'inactive') DEFAULT 'active'
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

#### `gallery`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `category_id` INT UNSIGNED NOT NULL (FK -> `gallery_categories.id`)
* `title` VARCHAR(255) NOT NULL
* `caption` TEXT NULL
* `image_path` VARCHAR(255) NOT NULL
* `is_featured` TINYINT(1) DEFAULT 0
* `display_order` INT DEFAULT 0
* `is_archived` TINYINT(1) DEFAULT 0
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

#### `faqs`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `category` VARCHAR(50) DEFAULT 'general' (`general`, `security`, `cleaning`, `corporate`)
* `question` VARCHAR(255) NOT NULL
* `answer` TEXT NOT NULL
* `display_order` INT DEFAULT 0
* `status` ENUM('active', 'inactive') DEFAULT 'active'
* `is_archived` TINYINT(1) DEFAULT 0
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

#### `testimonials`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `client_name` VARCHAR(150) NOT NULL
* `company` VARCHAR(150) NOT NULL
* `designation` VARCHAR(150) NULL
* `rating` TINYINT UNSIGNED DEFAULT 5
* `testimonial` TEXT NOT NULL
* `photo` VARCHAR(255) NULL
* `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'approved'
* `is_featured` TINYINT(1) DEFAULT 1
* `display_order` INT DEFAULT 0
* `is_archived` TINYINT(1) DEFAULT 0
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

---

### 2.6 Enquiries, Leads & CRM

#### `enquiries`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `type` ENUM('general', 'quote', 'contact', 'survey') DEFAULT 'general'
* `name` VARCHAR(150) NOT NULL
* `company` VARCHAR(150) NULL
* `email` VARCHAR(150) NOT NULL
* `phone` VARCHAR(30) NOT NULL
* `service_required` VARCHAR(150) NULL
* `location` VARCHAR(150) NULL
* `preferred_contact` VARCHAR(50) DEFAULT 'phone'
* `message` TEXT NOT NULL
* `status` ENUM('new', 'contacted', 'in_progress', 'converted', 'closed', 'archived') DEFAULT 'new'
* `notes` TEXT NULL
* `ip_address` VARCHAR(45) NULL
* `user_agent` TEXT NULL
* `is_archived` TINYINT(1) DEFAULT 0
* `archived_at` DATETIME NULL
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
* `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
* INDEX (`type`, `status`), INDEX (`created_at`)

#### `newsletter_subscribers`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `email` VARCHAR(150) NOT NULL UNIQUE
* `name` VARCHAR(100) NULL
* `status` ENUM('active', 'unsubscribed') DEFAULT 'active'
* `ip_address` VARCHAR(45) NULL
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

---

### 2.7 Global Settings, SEO & Audit Trail

#### `settings`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general' (`general`, `contact`, `social`, `smtp`, `analytics`, `homepage`, `about`)
* `key_name` VARCHAR(100) NOT NULL UNIQUE
* `key_value` LONGTEXT NULL
* `field_type` VARCHAR(30) DEFAULT 'text' (`text`, `textarea`, `number`, `image`, `json`, `toggle`)
* `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

#### `seo_metadata`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `page_route` VARCHAR(100) NOT NULL UNIQUE (`home`, `about`, `security-services`, `cleaning-services`, `blog`, `gallery`, `contact`, `get-a-quote`, `privacy-policy`, `terms-conditions`)
* `meta_title` VARCHAR(255) NOT NULL
* `meta_description` TEXT NOT NULL
* `meta_keywords` TEXT NULL
* `og_image` VARCHAR(255) NULL
* `canonical_url` VARCHAR(255) NULL
* `robots` VARCHAR(100) DEFAULT 'index, follow'
* `structured_data` TEXT NULL
* `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

#### `audit_logs`
* `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `user_id` INT UNSIGNED NULL (FK -> `users.id` ON DELETE SET NULL)
* `user_name` VARCHAR(100) NULL
* `action` VARCHAR(100) NOT NULL
* `entity_type` VARCHAR(50) NOT NULL
* `entity_id` INT UNSIGNED NULL
* `description` TEXT NOT NULL
* `ip_address` VARCHAR(45) NULL
* `user_agent` TEXT NULL
* `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
* INDEX (`entity_type`, `entity_id`), INDEX (`created_at`)

---

## 3. Archive Vault Soft-Delete Strategy

Every critical entity (`services`, `blog_posts`, `blog_categories`, `gallery`, `testimonials`, `faqs`, `enquiries`, `users`) carries:
* `is_archived TINYINT(1) DEFAULT 0`
* `archived_at DATETIME NULL`
* `archived_by INT UNSIGNED NULL`

All normal frontend and active admin queries filter on `WHERE is_archived = 0`.  
The **Archive Vault** (`/admin/archive.php`) allows authorized administrators to view archived records with one-click **Restore** or **Permanent Purge** functionality.
