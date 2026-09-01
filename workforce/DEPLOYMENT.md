# Production Deployment & Subdomain Setup

## 1. Domain & DNS Configuration
- **Subdomain:** `admin.jmjenterprisessolutions.com`
- **DNS Record:** `CNAME` or `A` record pointing to the host server IP.
- **SSL Certificate:** Let's Encrypt / Cloudflare SSL (HTTPS Strict Transport Security).

## 2. Apache VirtualHost Configuration
```apache
<VirtualHost *:80>
    ServerName admin.jmjenterprisessolutions.com
    Redirect permanent / https://admin.jmjenterprisessolutions.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName admin.jmjenterprisessolutions.com
    DocumentRoot "C:/xampp/htdocs/jmj/workforce"

    SSLEngine on
    SSLCertificateFile "path/to/cert.crt"
    SSLCertificateKeyFile "path/to/key.key"

    <Directory "C:/xampp/htdocs/jmj/workforce">
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>

    ErrorLog "logs/workforce_error.log"
    CustomLog "logs/workforce_access.log" combined
</VirtualHost>
```

## 3. Scheduled Cron Tasks
```bash
# Every 1 minute: Check for No-Shows, Overdue Patrols, and Expired QR tokens
* * * * * php C:/xampp/htdocs/jmj/workforce/cron/runner.php >> C:/xampp/htdocs/jmj/workforce/storage/logs/cron.log 2>&1

# Daily at midnight: Contract expiry & Document expiry alerts
0 0 * * * php C:/xampp/htdocs/jmj/workforce/cron/daily_compliance.php >> C:/xampp/htdocs/jmj/workforce/storage/logs/daily_cron.log 2>&1
```
