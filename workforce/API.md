# RESTful API Architecture & Endpoints

## 1. Response Standards
All API endpoints return standard JSON responses:

### Success (HTTP 200/201):
```json
{
  "success": true,
  "message": "Action completed successfully",
  "data": { ... },
  "errors": [],
  "timestamp": "2026-08-31 21:30:00"
}
```

### Error (HTTP 400/401/403/404/422/500):
```json
{
  "success": false,
  "message": "Error description",
  "data": {},
  "errors": ["Specific field validation error"],
  "timestamp": "2026-08-31 21:30:00"
}
```

## 2. Key Endpoint Groups
- `POST /api/auth/login` - Authenticate user or mobile staff.
- `GET /api/sites/{id}/dynamic-qr` - Retrieve live signed 30-sec HMAC-SHA256 QR token.
- `POST /api/attendance/check-in` - Submit 4-layer verified check-in.
- `POST /api/attendance/check-out` - Submit check-out and calculate hours.
- `POST /api/attendance/offline-batch` - Synchronize stored offline attendance events.
- `GET /api/patrols/active-tour` - Fetch active guard patrol tour sequence.
- `POST /api/patrols/scan-checkpoint` - Record checkpoint scan.
- `POST /api/tasks/{id}/complete` - Submit zone checklist with before/after photos.
- `POST /api/incidents/report` - Submit incident report ticket.
- `POST /api/sos/trigger` - Dispatch emergency panic alert.
- `GET /api/radar/live-sites` - Real-time GeoJSON feed for Leaflet operations map.
