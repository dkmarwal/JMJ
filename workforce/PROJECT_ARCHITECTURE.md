# JMJ Enterprise Solutions - Workforce Management & Field Operations Architecture

## 1. Executive Summary
The JMJ Workforce Management & Field Operations SaaS Platform (`admin.jmjenterprisessolutions.com`) is a dedicated multi-tenant application providing real-time command, scheduling, biometric verification, guard tour monitoring, facility hygiene tracking, field audits, incident response, payroll calculation, and client billing for JMJ Enterprise Solutions.

## 2. Layered Architecture
- **Presentation Layer:**
  - Desktop SaaS Dashboard (Tailwind CSS, high information density, Leaflet radar map).
  - Field Worker Mobile PWA (`/mobile/`, ServiceWorker, offline sync queue, Web Camera & Geolocation APIs).
  - Client Portal (`/client/`, strict tenant scoping for live muster, patrols, SLA, and invoices).
- **Controller & API Layer:**
  - MVC Controllers with granular RBAC middleware.
  - RESTful API Endpoints (`/api/*`) returning structured, error-shielded JSON.
- **Service Layer (Domain Logic):**
  - `AttendanceService`: 4-layer verification (Geofence + Dynamic QR + Selfie + Risk score).
  - `GeofenceEngine`: Circular Haversine and Ray-Casting Polygon math.
  - `DynamicQRService`: HMAC-SHA256 time-bounded signed token generation.
  - `PatrolTourService`: Route sequencing, checkpoint scans, and real-time deviation alerts.
  - `RosterService`: Shift assignment, auto no-show triggers, and reliever matching.
  - `IncidentService`: Severity escalation and emergency SOS panic queue.
  - `PayrollEngine`: Verifiable attendance-driven salary calculations.
  - `BillingService`: Contract rate calculation, muster roll attachments, and invoice generation.
  - `NotificationDispatcher`: In-app, Email, SMS, and WhatsApp Cloud API abstraction.
- **Data Layer:**
  - MariaDB / MySQL 8.0+ (`jmj_workforce_db`) with 50 normalized, multi-tenant tables.
