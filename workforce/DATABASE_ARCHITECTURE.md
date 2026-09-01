# Database Architecture & Entity Specifications

## 1. Database Specifications
- **Database Name:** `jmj_workforce_db`
- **Character Set:** `utf8mb4`
- **Collation:** `utf8mb4_unicode_ci`
- **Engine:** InnoDB with Foreign Key constraints and cascading referential integrity.

## 2. Multi-Tenant Scoping Rules
Every core operational query must filter by `company_id`.
For client users: filter by `client_id`.
For field officers & supervisors: filter by assigned site list.

## 3. Table Groupings
1. **Tenancy & RBAC:** `companies`, `branches`, `roles`, `permissions`, `role_permissions`, `users`, `audit_logs`, `settings`.
2. **Clients & Infrastructure:** `clients`, `client_contacts`, `contracts`, `sites`, `site_zones`, `site_checkpoints`.
3. **Staff & Deployments:** `employee_categories`, `employees`, `employee_documents`, `employee_devices`, `employee_deployments`.
4. **Shifts & Scheduling:** `shift_templates`, `shifts`, `shift_rosters`.
5. **Attendance Engine:** `qr_tokens`, `attendance`, `attendance_verifications`, `attendance_disputes`.
6. **Guard Tour & Patrols:** `patrol_routes`, `patrol_route_checkpoints`, `patrol_tours`, `patrol_scans`.
7. **Facility Tasks & Consumables:** `task_templates`, `tasks`, `consumable_inventory`, `consumable_logs`.
8. **Audits & Incidents:** `site_audits`, `incidents`, `incident_attachments`, `sos_alerts`, `shift_handovers`.
9. **HR & Payroll:** `leave_types`, `leave_requests`, `payroll_periods`, `payroll_records`.
10. **Finance & SLA:** `client_invoices`, `invoice_items`, `sla_rules`, `sla_breaches`.
11. **Notifications:** `notifications`.
