<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Central Application Route Definitions
 */

declare(strict_types=1);

use Core\Router;

// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================
Router::get('/', 'DashboardController@index', ['auth']);
Router::get('/login', 'AuthController@showLogin', ['guest']);
Router::post('/login', 'AuthController@login', ['guest']);
Router::match(['GET', 'POST'], '/logout', 'AuthController@logout', ['auth']);

// ============================================================================
// DASHBOARD ROUTES
// ============================================================================
Router::get('/dashboard', 'DashboardController@index', ['auth']);

// ============================================================================
// CLIENTS & CRM ROUTES
// ============================================================================
Router::get('/clients', 'ClientController@index', ['auth', 'permission:clients.view']);
Router::get('/clients/view', 'ClientController@show', ['auth', 'permission:clients.view']);
Router::get('/clients/create', 'ClientController@create', ['auth', 'permission:clients.manage']);
Router::post('/clients/store', 'ClientController@store', ['auth', 'permission:clients.manage']);

// ============================================================================
// SITES, GEOFENCING & RADAR ROUTES
// ============================================================================
Router::get('/sites', 'SiteController@index', ['auth', 'permission:sites.view']);
Router::get('/sites/view', 'SiteController@show', ['auth', 'permission:sites.view']);
Router::get('/sites/create', 'SiteController@create', ['auth', 'permission:sites.manage']);
Router::post('/sites/store', 'SiteController@store', ['auth', 'permission:sites.manage']);
Router::get('/sites/radar', 'SiteController@radar', ['auth', 'permission:sites.view']);

// ============================================================================
// WORKFORCE STAFF & ONBOARDING ROUTES
// ============================================================================
Router::get('/staff', 'StaffController@index', ['auth', 'permission:staff.view']);
Router::get('/staff/view', 'StaffController@show', ['auth', 'permission:staff.view']);
Router::get('/staff/create', 'StaffController@create', ['auth', 'permission:staff.onboard']);
Router::post('/staff/store', 'StaffController@store', ['auth', 'permission:staff.onboard']);
Router::get('/staff/id-card', 'StaffController@idCard', ['auth', 'permission:staff.view']);

// ============================================================================
// SHIFTS, ROSTERS & RELIEVER MATCHING ROUTES
// ============================================================================
Router::get('/shifts', 'ShiftController@index', ['auth']);
Router::get('/shifts/roster', 'ShiftController@roster', ['auth', 'permission:roster.manage']);
Router::get('/shifts/relievers', 'ShiftController@relievers', ['auth', 'permission:relievers.dispatch']);
Router::post('/shifts/dispatch-reliever', 'ShiftController@dispatchReliever', ['auth', 'permission:relievers.dispatch']);

// ============================================================================
// ATTENDANCE, VERIFICATION & MUSTER ROLL ROUTES
// ============================================================================
Router::get('/attendance', 'AttendanceController@index', ['auth', 'permission:attendance.view']);
Router::get('/attendance/disputes', 'AttendanceController@disputes', ['auth', 'permission:attendance.disputes']);
Router::get('/attendance/muster', 'AttendanceController@musterRoll', ['auth', 'permission:attendance.view']);
Router::post('/attendance/override', 'AttendanceController@override', ['auth', 'permission:attendance.override']);

// ============================================================================
// GUARD TOUR & PATROL COMPLIANCE ROUTES
// ============================================================================
Router::get('/patrols', 'PatrolController@index', ['auth', 'permission:patrols.view']);

// ============================================================================
// TASKS, PANTRY & CONSUMABLES ROUTES
// ============================================================================
Router::get('/tasks', 'TaskController@index', ['auth', 'permission:tasks.manage']);

// ============================================================================
// INCIDENT COMMAND & EMERGENCY SOS QUEUE
// ============================================================================
Router::get('/incidents', 'IncidentController@index', ['auth']);

// ============================================================================
// FIELD OFFICER AUDITS ROUTES
// ============================================================================
Router::get('/audits', 'AuditController@index', ['auth', 'permission:audits.view']);
Router::get('/audits/create', 'AuditController@create', ['auth', 'permission:audits.conduct']);
Router::get('/audits/conduct', 'AuditController@create', ['auth', 'permission:audits.conduct']);
Router::post('/audits/store', 'AuditController@store', ['auth', 'permission:audits.conduct']);
Router::post('/audits/conduct', 'AuditController@store', ['auth', 'permission:audits.conduct']);

// ============================================================================
// PAYROLL & SALARY SHEETS ROUTES
// ============================================================================
Router::get('/payroll', 'PayrollController@index', ['auth', 'permission:payroll.calculate']);
Router::get('/payroll/period', 'PayrollController@viewPeriod', ['auth', 'permission:payroll.calculate']);
Router::post('/payroll/calculate', 'PayrollController@calculate', ['auth', 'permission:payroll.calculate']);

// ============================================================================
// CLIENT BILLING & INVOICES ROUTES
// ============================================================================
Router::get('/billing', 'BillingController@index', ['auth', 'permission:billing.invoices']);
Router::get('/billing/invoice', 'BillingController@show', ['auth', 'permission:billing.invoices']);
Router::post('/billing/generate', 'BillingController@generate', ['auth', 'permission:billing.invoices']);

// ============================================================================
// REPORTS & EXPORTS
// ============================================================================
Router::get('/reports', 'ReportController@index', ['auth', 'permission:reports.view']);
Router::get('/reports/export-attendance', 'ReportController@exportAttendance', ['auth', 'permission:reports.export']);

// ============================================================================
// FIELD WORKER MOBILE PWA ROUTES (/mobile/)
// ============================================================================
Router::get('/mobile', 'MobileController@index', ['auth']);
Router::get('/mobile/check-in', 'MobileController@checkInScreen', ['auth']);
Router::get('/mobile/patrol', 'MobileController@patrolScreen', ['auth']);

// ============================================================================
// RESTful API ENDPOINTS (/api/*)
// ============================================================================
Router::get('/api/sites/{id}/dynamic-qr', 'ApiController@getDynamicQR');
Router::post('/api/attendance/check-in', 'ApiController@checkIn');
Router::post('/api/attendance/check-out', 'ApiController@checkOut');
Router::post('/api/patrols/scan', 'ApiController@scanPatrolCheckpoint');
Router::post('/api/sos/trigger', 'ApiController@triggerSOS');
Router::get('/api/radar/live-sites', 'ApiController@getRadarSites', ['auth']);
