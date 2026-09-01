<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * RESTful API Controller for Mobile PWA, Terminals & Live Radar
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Services\DynamicQRService;
use Services\AttendanceService;
use Services\PatrolTourService;
use Services\IncidentService;

class ApiController {
    /**
     * GET /api/sites/{id}/dynamic-qr
     * Generates time-bounded HMAC-SHA256 signed QR token refreshed every 30s
     */
    public function getDynamicQR(int $siteId): void {
        $tokenData = DynamicQRService::generateToken($siteId, 30);
        wf_json_response(true, 'Dynamic QR Token Generated', $tokenData);
    }

    /**
     * POST /api/attendance/check-in
     * 4-Layer Verification Check-in
     */
    public function checkIn(): void {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true) ?? $_POST;

        $result = AttendanceService::processCheckIn($payload);
        $status = $result['success'] ? 200 : 422;
        wf_json_response($result['success'], $result['message'], $result, $status);
    }

    /**
     * POST /api/attendance/check-out
     */
    public function checkOut(): void {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true) ?? $_POST;
        $employeeId = (int)($payload['employee_id'] ?? 0);

        if (!$employeeId) {
            wf_json_response(false, 'Employee ID is required for check-out.', null, 422);
        }

        $result = AttendanceService::processCheckOut($employeeId);
        $status = $result['success'] ? 200 : 422;
        wf_json_response($result['success'], $result['message'], $result, $status);
    }

    /**
     * POST /api/patrols/scan
     */
    public function scanPatrolCheckpoint(): void {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true) ?? $_POST;

        $tourId = (int)($payload['tour_id'] ?? 0);
        $qr = $payload['qr_token'] ?? '';
        $lat = (float)($payload['latitude'] ?? 0);
        $lng = (float)($payload['longitude'] ?? 0);

        $result = PatrolTourService::scanCheckpoint($tourId, $qr, $lat, $lng);
        $status = $result['success'] ? 200 : 422;
        wf_json_response($result['success'], $result['message'], $result, $status);
    }

    /**
     * POST /api/sos/trigger
     */
    public function triggerSOS(): void {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true) ?? $_POST;

        $employeeId = (int)($payload['employee_id'] ?? 0);
        $siteId = (int)($payload['site_id'] ?? 1);
        $lat = (float)($payload['latitude'] ?? 28.6139);
        $lng = (float)($payload['longitude'] ?? 77.2090);

        $result = IncidentService::triggerSOS($employeeId, $siteId, $lat, $lng);
        wf_json_response(true, $result['message'], $result, 200);
    }

    /**
     * GET /api/radar/live-sites
     * Real-time GeoJSON feed of client sites and active staffing
     */
    public function getRadarSites(): void {
        $db = Database::getInstance();
        $cid = Auth::companyId();
        $today = date('Y-m-d');

        $sites = $db->fetchAll(
            "SELECT s.id, s.site_name, s.site_code, s.site_type, s.address, s.latitude, s.longitude, s.geofence_radius,
                    c.company_name as client_name,
                    (SELECT COUNT(*) FROM attendance a WHERE a.site_id = s.id AND a.attendance_date = :tdate AND a.status IN ('CHECKED_IN', 'VERIFIED')) as live_present_count,
                    (SELECT COUNT(*) FROM employee_deployments ed WHERE ed.site_id = s.id AND ed.status = 'active') as required_guards_count
             FROM sites s
             JOIN clients c ON s.client_id = c.id
             WHERE s.company_id = :cid AND s.status = 'active'",
            ['cid' => $cid, 'tdate' => $today]
        );

        wf_json_response(true, 'Live Radar Sites Feed', ['sites' => $sites]);
    }
}
