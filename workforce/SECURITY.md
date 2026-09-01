# Security & Compliance Architecture

## 1. Security Defenses
- **IDOR Protection:** Every query must enforce tenant boundaries via `Auth::companyId()`. Cross-tenant ID access returns 403 Forbidden and generates a security violation audit log.
- **CSRF Protection:** Synchronizer Token pattern validated automatically on all state-modifying web POST requests (`Core\Csrf`).
- **SQL Injection Prevention:** 100% PHP PDO prepared statements with strict parameter binding.
- **XSS Prevention:** HTML output escaped via `wf_e()` / `htmlspecialchars(..., ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`.
- **Session Security:** `HttpOnly`, `SameSite=Lax`, user-agent fingerprint binding, and automatic session regeneration.
- **Brute Force Protection:** IP and user-based Rate Limiter (`Core\RateLimiter`) with exponential decay.
- **File Upload Security:** Server-side MIME validation, random UUID file renaming, and non-executable storage directories.

## 2. Privacy & Biometric Protections
- Explicit consent workflow for camera and geolocation capture.
- Configurable retention periods (`FACE_DATA_RETENTION_DAYS`, `LOCATION_DATA_RETENTION_DAYS`).
- Documents and selfies stored outside public webroot under authorization-protected download endpoints.
