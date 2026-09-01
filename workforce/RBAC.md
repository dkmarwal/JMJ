# Role-Based Access Control (RBAC) Specification

## 1. 13 User Roles
1. `SUPER_ADMIN`: Full unrestricted access to all companies, branches, financial records, and system configurations.
2. `ADMIN`: Operational and HR oversight across assigned branches.
3. `HR_MANAGER`: Employee onboarding, statutory document verification, leave approvals, and payroll processing.
4. `OPERATIONS_MANAGER`: Client contract delivery, site geofencing, rosters, live radar, SLA monitoring, and incident escalation.
5. `FIELD_OFFICER`: Mobile-first site audits, surprise inspections, and team verification.
6. `SUPERVISOR`: Daily site operations, shift handovers, attendance dispute review, and patrol oversight.
7. `SECURITY_GUARD`: Mobile PWA check-in, dynamic QR scanning, patrol tour execution, and emergency SOS reporting.
8. `CLEANING_STAFF`: Mobile PWA zone checklist execution with before/after photo evidence.
9. `PANTRY_STAFF`: Pantry checklists and consumable replenishment.
10. `FACILITY_STAFF`: Equipment upkeep and utility maintenance checklists.
11. `ACCOUNTANT`: Client invoicing, payment tracking, muster roll review, and tax reporting.
12. `CLIENT_ADMIN`: Client portal access for live muster, site deployments, SLA dashboards, and invoices.
13. `CLIENT_VIEWER`: Read-only client stakeholder dashboard.

## 2. Granular Permissions List
- `company.manage`, `branch.manage`, `users.manage`, `roles.manage`, `settings.manage`, `audit.view`
- `clients.view`, `clients.manage`, `sites.view`, `sites.manage`, `contracts.manage`
- `staff.view`, `staff.onboard`, `staff.documents`, `deployments.manage`
- `shifts.manage`, `roster.manage`, `attendance.view`, `attendance.manage`, `attendance.override`, `attendance.disputes`, `relievers.dispatch`
- `patrols.manage`, `patrols.view`, `audits.conduct`, `audits.view`
- `tasks.manage`, `tasks.execute`, `consumables.manage`
- `incidents.report`, `incidents.manage`, `sos.respond`, `handovers.manage`
- `leave.manage`, `payroll.calculate`, `payroll.approve`
- `billing.invoices`, `sla.monitor`, `reports.view`, `reports.export`, `client.portal.access`
