# JMJ Enterprises Solutions - Site Architecture & Engineering Blueprint

**Framework Strategy:** Zero-bloat Modular PHP 8+ Architecture  
**Design System:** Tailwind CSS with Indian B2B Corporate Theme (Midnight Obsidian, Deep Navy, Amber Gold, Clean Slate)  
**Security Standard:** Enterprise Hardened (PDO, CSRF, XSS filter, CSP/Security Headers, Session Fingerprinting, RBAC, Audit Vault)

---

## 1. Directory Structure

```
c:\xampp\htdocs\jmj\
├── .htaccess                 # Apache URL rewriting & security headers
├── index.php                 # Public Application Front-Controller & Router
├── sitemap.php               # Dynamic XML Sitemap Generator (main, blog, service)
├── robots.txt                # Search engine crawlers directive
│
├── config/
│   ├── .env                  # Environment secrets (DB, APP_KEY, SMTP, Mail)
│   ├── .env.example          # Sample environment template
│   ├── config.php            # Core config loader, constants, autoloading
│   ├── database.php          # PDO singleton factory with reconnection logic
│   └── permissions.php       # RBAC role-to-permission mapping definitions
│
├── core/
│   ├── App.php               # Application bootstrap & Request dispatcher
│   ├── Router.php            # Clean regex URL router
│   ├── Database.php          # PDO database wrapper with helper query methods
│   ├── View.php              # Template renderer with layout inheritance
│   ├── Auth.php              # Authentication & Session verification
│   ├── Csrf.php              # Cryptographic CSRF token generation & validation
│   ├── Session.php           # Secure session management with flash messaging
│   ├── RateLimiter.php       # Anti-bruteforce & form spam prevention
│   ├── Validator.php         # Clean input data validation & sanitization
│   └── Helpers.php           # Global helper functions (slugify, e, asset, url, etc.)
│
├── services/
│   ├── ServiceService.php    # Business logic for Security & Cleaning Services
│   ├── BlogService.php       # Blog publishing, scheduling, search, revisions
│   ├── LeadService.php       # Enquiries, Quote requests, Email triggers
│   ├── MediaService.php      # File upload validation, WebP optimization, storage
│   ├── SeoService.php        # Meta tags, Canonical URLs, JSON-LD Schema generation
│   ├── SettingService.php    # Global settings and theme configuration
│   ├── AuditService.php      # Admin action security audit logging
│   └── MailService.php       # PHPMailer / Native SMTP delivery engine
│
├── models/
│   ├── Service.php           # Service entity & query builder
│   ├── BlogPost.php          # Blog post entity & query builder
│   ├── Category.php          # Taxonomy models
│   ├── User.php              # Staff & admin accounts
│   ├── Enquiry.php           # Lead submissions
│   ├── Media.php             # Media records
│   ├── Gallery.php           # Portfolio / Gallery items
│   ├── Faq.php               # FAQs repository
│   └── Testimonial.php       # Client testimonials
│
├── controllers/
│   ├── HomeController.php    # Homepage presentation
│   ├── AboutController.php   # Corporate About Us page
│   ├── ServiceController.php # Landing & Individual service endpoints
│   ├── BlogController.php    # Frontend blog index, category, tags, post, search
│   ├── GalleryController.php # Corporate gallery
│   ├── ContactController.php # Contact page & AJAX message submissions
│   ├── QuoteController.php   # Instant Quote request form & calculator
│   ├── PageController.php    # Privacy, Terms, Custom 404
│   └── ApiController.php     # AJAX endpoints (leads, search, newsletter)
│
├── views/
│   ├── layouts/
│   │   ├── main.php          # Main public layout (Header, Nav, Footer, Modals)
│   │   ├── header.php        # Sticky responsive navigation with Mega Menus
│   │   └── footer.php        # Corporate B2B footer with schema, contact, links
│   ├── home/
│   │   └── index.php         # Cinematic Hero, Stats, 10-State Footprint, Roster Matrix
│   ├── about/
│   │   └── index.php         # Vision, Mission, PSARA Compliance, Leadership, Sectors
│   ├── services/
│   │   ├── index.php         # All services landing hub
│   │   ├── security.php      # Security services landing hub
│   │   ├── cleaning.php      # Cleaning services landing hub
│   │   └── detail.php        # Dynamic, rich SEO single service detail view
│   ├── blog/
│   │   ├── index.php         # Blog magazine layout, featured, categories, search
│   │   └── detail.php        # Rich article layout, TOC, schema, author bio, related
│   ├── gallery/
│   │   └── index.php         # Interactive categorized grid with lightbox
│   ├── contact/
│   │   └── index.php         # Interactive contact form, WhatsApp CTA, Google Maps
│   ├── quote/
│   │   └── index.php         # Detailed site survey & multi-service quote builder
│   ├── pages/
│   │   ├── privacy.php       # Privacy Policy
│   │   ├── terms.php         # Terms of Service
│   │   └── 404.php           # Premium customized 404 error page
│   └── partials/
│       ├── breadcrumb.php    # SEO BreadcrumbList
│       ├── quote_modal.php   # Quick Quote popup modal
│       ├── toast.php         # Flash notifications
│       └── cta_banner.php    # Reusable conversion CTA
│
├── admin/
│   ├── index.php             # Admin router or dashboard entry point
│   ├── login.php             # High-security administrative login
│   ├── logout.php            # Secure session termination
│   ├── dashboard.php         # Hawks Infotech inspired metrics dashboard
│   ├── blogs.php             # Blog post management (live, drafts, scheduled)
│   ├── blog-editor.php       # Modern rich-text blog composer (Revisions, SEO preview)
│   ├── categories.php        # Blog category taxonomy manager
│   ├── tags.php              # Blog tags manager
│   ├── services.php          # Services CMS (Security & Cleaning categories + pages)
│   ├── service-editor.php    # Add/Edit service details, features, FAQs, images
│   ├── enquiries.php         # Leads, Quotes & Inbound Communications CRM
│   ├── gallery.php           # Gallery management & image uploader
│   ├── faqs.php              # FAQ CMS engine
│   ├── testimonials.php      # Testimonial management & status moderation
│   ├── media.php             # Media library (Upload, Filter, Copy URL, Alt text)
│   ├── users.php             # Staff & RBAC administrator management
│   ├── seo.php               # Global SEO meta tags & sitemap manager
│   ├── settings.php          # Corporate profile, SMTP, social, phone, address
│   ├── audit-logs.php        # Security audit trail
│   ├── archive.php           # Archive & Recovery Vault (Restore / Purge)
│   └── partials/
│       ├── header.php        # Admin top bar & notifications
│       ├── sidebar.php       # Hawks Infotech style collapsible admin navigation
│       └── footer.php        # Admin footer & scripts
│
├── assets/
│   ├── css/
│   │   ├── custom.css        # Fine-tuned styles, typography & animations
│   │   └── admin.css         # Admin custom dashboard styling
│   ├── js/
│   │   ├── main.js           # Navigation, Mega menu, AJAX forms, Lazyload
│   │   ├── admin.js          # Admin modals, table filters, image selectors
│   │   └── editor.js         # Rich text editor initialization & live preview
│   └── img/
│       ├── logo.jpg          # Original brand logo
│       ├── security.JPG      # Security guarding asset
│       └── hospital.JPG      # Facility asset
│
└── uploads/                  # Managed user uploads directory
    ├── media/                # Media library uploads
    ├── blogs/                # Blog featured images
    ├── services/             # Service hero and gallery images
    ├── gallery/              # Gallery uploads
    └── testimonials/         # Client avatars
```

---

## 2. Public URL Routing Matrix

| Public Clean URL | Controller & Method | Description |
| :--- | :--- | :--- |
| `/` | `HomeController@index` | Homepage with Hero, Stats, Services, Presence, Leads |
| `/about/` | `AboutController@index` | About Us, PSARA certification, 10-State Footprint |
| `/security-services/` | `ServiceController@securityHub` | All 12 Security Services Catalog Hub |
| `/security-services/{slug}/` | `ServiceController@detail` | Dedicated SEO page for each Security Service |
| `/cleaning-services/` | `ServiceController@cleaningHub` | All 14 Cleaning Services Catalog Hub |
| `/cleaning-services/{slug}/` | `ServiceController@detail` | Dedicated SEO page for each Cleaning Service |
| `/blog/` | `BlogController@index` | Blog magazine, featured post, category tabs, search |
| `/blog/{slug}/` | `BlogController@detail` | Full blog post with TOC, Schema, Social Share, Bio |
| `/blog/category/{slug}/` | `BlogController@category` | Filtered posts by category |
| `/blog/tag/{slug}/` | `BlogController@tag` | Filtered posts by tag |
| `/gallery/` | `GalleryController@index` | Gallery showcase with category filter & modal |
| `/contact/` | `ContactController@index` | Contact details, Google Map, Contact form |
| `/get-a-quote/` | `QuoteController@index` | Dedicated detailed Quote Request form |
| `/privacy-policy/` | `PageController@privacy` | Privacy Policy page |
| `/terms-conditions/` | `PageController@terms` | Terms & Conditions page |
| `/sitemap.xml` | `sitemap.php` | Main XML Sitemap index |
| `/blog-sitemap.xml` | `sitemap.php?type=blog` | Dynamic Blog XML Sitemap |
| `/service-sitemap.xml`| `sitemap.php?type=service` | Dynamic Services XML Sitemap |
| `/api/lead/` | `ApiController@submitLead` | AJAX Lead & Contact handler |
| `/api/quote/` | `ApiController@submitQuote` | AJAX Quote submission handler |
| `/api/newsletter/` | `ApiController@subscribe` | Newsletter subscription |

---

## 3. SEO & Structured Data Architecture

1. **Organization Schema:** Name, alternate names, logo, address, phone numbers, contactPoints.
2. **LocalBusiness / SecurityService / HousekeepingService Schema:** Geocoordinates, coverage areas (Delhi NCR, Gurgaon, Noida, Bangalore, etc.), operating hours.
3. **Article / BlogPosting Schema:** Headline, author, datePublished, dateModified, publisher, image, description.
4. **BreadcrumbList Schema:** Hierarchical breadcrumbs on every inner service and blog page.
5. **FAQPage Schema:** Interactive collapsible FAQs rendered with Google FAQPage rich snippet markup.
6. **OpenGraph & Twitter Cards:** Dynamic OG title, description, image, and canonical URL on all public pages.
