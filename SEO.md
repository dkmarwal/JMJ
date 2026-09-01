# JMJ Enterprises Solutions - SEO Architecture & Search Strategy

This document outlines the technical SEO architecture, structured data schema implementations, URL taxonomy, and sitemap infrastructure for **JMJ Enterprises Solutions Ltd.**

---

## 🎯 Target Keyword & Intent Strategy

### 1. High-Intent Commercial Service Clusters
* **Security Services:** `PSARA licensed security agency delhi`, `bank security guards india`, `armed security guard company`, `hospital security services`, `lady security guards ncr`, `corporate security guards bangalore`.
* **Cleaning Services:** `commercial floor stripping and waxing`, `hospital grade sanitization services`, `facade glass cleaning delhi ncr`, `corporate housekeeping contract`, `industrial plant deep cleaning`.

---

## 🏗️ Technical SEO & Structured Schema Markup

### 1. JSON-LD Schemas Generated (`\Services\SeoService`)
* **Organization Schema:** Rendered across all page footers:
  ```json
  {
    "@context": "https://schema.org",
    "@type": "SecurityService",
    "name": "JMJ Enterprises Solutions Ltd.",
    "telephone": "18008890832",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "250, Sant Nagar, East of Kailash",
      "addressLocality": "New Delhi",
      "postalCode": "110065",
      "addressCountry": "IN"
    }
  }
  ```
* **Article Schema:** Rendered on all individual blog publications with author, publisher, and image tags.
* **FAQPage Schema:** Automatically generated on service detail pages from database FAQs to secure Google Rich Results.
* **BreadcrumbList Schema:** Rendered on all subpages to provide clear Google search hierarchy.

---

## 🗺️ XML Sitemaps Infrastructure
* **Master Sitemap:** `https://jmjenterprisessolutions.com/sitemap.xml` (Includes static routes, service detail pages, and articles)
* **Blog Sitemap:** `https://jmjenterprisessolutions.com/sitemap.php?type=blog`
* **Services Sitemap:** `https://jmjenterprisessolutions.com/sitemap.php?type=service`

---

## ⚡ Performance Optimization & Core Web Vitals
* Responsive, modern Tailwind CSS architecture with preconnected typography.
* Zero external JavaScript runtime framework overhead.
* Asynchronous AJAX form submissions with responsive toast feedbacks.
* Clean semantic HTML5 elements (`<header>`, `<main>`, `<article>`, `<aside>`, `<footer>`).
