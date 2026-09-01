# JMJ Enterprises Solutions - Production Deployment Guide

This guide covers deployment procedures for Apache / Nginx web servers, PHP 8+ configurations, environment variables, and directory permissions.

---

## 🛠️ Server Prerequisites
* **Web Server:** Apache 2.4+ (with `mod_rewrite`, `mod_headers`, `mod_deflate`) or Nginx 1.20+
* **PHP:** Version 8.1, 8.2, 8.3, or 8.4
* **Required PHP Extensions:**
  * `pdo_mysql`
  * `mbstring`
  * `gd` / `imagick` (for photo resizing)
  * `fileinfo` (for MIME verification)
  * `curl`
  * `json`
* **MySQL:** Version 8.0+ or MariaDB 10.4+
* **SSL Certificate:** TLS 1.3 / Let's Encrypt

---

## 🚀 Apache Configuration (.htaccess)
The project includes a production-ready `.htaccess` file handling:
1. Routing all requests through `index.php`
2. Security headers (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`)
3. Blocking direct access to sensitive directories (`/config/`, `/core/`, `/database/`)
4. GZIP compression on text, css, and javascript assets
5. Browser caching on images, fonts, and stylesheets

---

## 🌐 Nginx Server Block Example
For Nginx environments:
```nginx
server {
    listen 443 ssl http2;
    server_name jmjenterprisessolutions.com www.jmjenterprisessolutions.com;
    root /var/www/jmj;
    index index.php;

    # SSL Certificates
    ssl_certificate /etc/letsencrypt/live/jmjenterprisessolutions.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/jmjenterprisessolutions.com/privkey.pem;

    # Protect Sensitive Directories
    location ~ ^/(config|core|database) {
        deny all;
        return 404;
    }

    # Static Assets & Uploads
    location ~* \.(jpg|jpeg|png|gif|svg|ico|css|js|woff2|webp)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }

    # Clean Front Controller Routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM Execution
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 🔒 Directory Permissions
```bash
# Set owner and group
chown -R www-data:www-data /var/www/jmj

# Directory permissions
find /var/www/jmj -type d -exec chmod 755 {} \;

# File permissions
find /var/www/jmj -type f -exec chmod 644 {} \;

# Writable Uploads
chmod -R 775 /var/www/jmj/uploads
```

---

## 🔐 Environment Variables (`.env`)
In production, update `config/.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://jmjenterprisessolutions.com

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=jmj_enterprise_db
DB_USER=jmj_user
DB_PASS=YourStrongProductionPassword!

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USER=info@jmjenterprisessolutions.com
MAIL_PASS=AppPasswordHere
```
