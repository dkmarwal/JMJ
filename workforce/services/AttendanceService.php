<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Central 4-Layer Attendance Verification & Lifecycle Engine
 */

declare(strict_types=1);

namespace Services;

use Core\Database;
use Core\Auth;
use RuntimeException;

class AttendanceService {
    /**
     * Submit and verify a staff check-in event using 4-layer verification
     */
    public static function processCheckIn(array $payload): array {
        $db = Database::getInstance();

        $employeeId = (int)($payload['employee_id'] ?? 0);
        $siteId     = (int)($payload['site_id'] ?? 0);
        $shiftId    = (int)($payload['shift_id'] ?? 0);
        $latitude   = (float)($payload['latitude'] ?? 0);
        $longitude  = (float)($payload['longitude'] ?? 0);
        $accuracy   = (float)($payload['accuracy'] ?? 50);
        $qrToken    = $payload['qr_token'] ?? null;
        $selfieData = $payload['selfie_base64'] ?? null;
        $deviceId   = $payload['device_id'] ?? 'WEB_PWA';
        $todayDate  = date('Y-m-d');
        $nowTime    = date('Y-m-d H:i:s');

        // 1. Fetch Employee, Site & Shift
        $employee = $db->fetch("SELECT * FROM employees WHERE id = :id AND status = 'active'", ['id' => $employeeId]);
        if (!$employee) {
            return ['success' => false, 'message' => 'Active employee record not found.'];
        }

        $site = $db->fetch("SELECT * FROM sites WHERE id = :id AND status = 'active'", ['id' => $siteId]);
        if (!$site) {
            return ['success' => false, 'message' => 'Designated client site not found or inactive.'];
        }

        $shift = $db->fetch("SELECT * FROM shifts WHERE id = :id AND status = 'active'", ['id' => $shiftId]);
        if (!$shift) {
            return ['success' => false, 'message' => 'Assigned shift schedule not found.'];
        }

        // 2. Prevent Duplicate Active Check-In
        $existing = $db->fetch(
            "SELECT * FROM attendance 
             WHERE employee_id = :eid AND attendance_date = :adate AND status IN ('CHECKED_IN', 'VERIFIED') AND check_out_time IS NULL",
            ['eid' => $employeeId, 'adate' => $todayDate]
        );
        if ($existing) {
            return [
                'success'       => false,
                'message'       => 'You are already checked in for today\'s shift at ' . wf_format_time($existing['check_in_time']),
                'attendance_id' => $existing['id']
            ];
        }

        // ====================================================================
        // LAYER 1: Geofence Verification & GPS Spoof Scoring
        // ====================================================================
        $siteLat = (float)$site['latitude'];
        $siteLng = (float)$site['longitude'];
        $allowedRadius = (int)($site['geofence_radius'] ?: 75);

        $distanceMeters = GeofenceService::calculateDistance($latitude, $longitude, $siteLat, $siteLng);
        $geofencePass = ($distanceMeters <= $allowedRadius);
        $geofenceStatus = $geofencePass ? 'PASS' : 'FAIL';

        $locationRisk = GeofenceService::evaluateLocationRisk($latitude, $longitude, $accuracy);
        if ($locationRisk['risk_score'] >= 60) {
            $geofenceStatus = 'SUSPICIOUS';
        }

        if (!$geofencePass) {
            return [
                'success' => false,
                'message' => "Geofence Check Failed: You are {$distanceMeters}m away from {$site['site_name']} (Maximum allowed: {$allowedRadius}m).",
                'distance' => $distanceMeters,
                'status'  => 'OUTSIDE_GEOFENCE'
            ];
        }

        // ====================================================================
        // LAYER 2: Signed Dynamic QR Code Token Validation
        // ====================================================================
        $qrStatus = 'NOT_REQUIRED';
        if (!empty($qrToken)) {
            $qrValidation = DynamicQRService::validateToken($qrToken, $siteId);
            if (!$qrValidation['valid']) {
                return [
                    'success' => false,
                    'message' => "QR Validation Failed: " . $qrValidation['reason'],
                    'status'  => $qrValidation['status']
                ];
            }
            $qrStatus = 'VALID';
        }

        // ====================================================================
        // LAYER 3: Live Selfie & Biometrics
        // ====================================================================
        $selfiePath = null;
        $faceMatchStatus = 'BYPASSED';
        if (!empty($selfieData)) {
            // Save selfie frame to storage
            $cleanBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $selfieData);
            $decodedImg = base64_decode($cleanBase64);
            if ($decodedImg !== false) {
                $filename = 'selfie_' . $employeeId . '_' . time() . '.jpg';
                $savePath = WF_ROOT_PATH . '/storage/uploads/selfies/' . $filename;
                file_put_contents($savePath, $decodedImg);
                $selfiePath = 'storage/uploads/selfies/' . $filename;
            }

            $faceProvider = new FaceVerificationProvider();
            $faceResult = $faceProvider->verifySelfie($selfieData, $employee['face_feature_token']);
            $faceMatchStatus = $faceResult['status'];
        }

        // ====================================================================
        // LAYER 4: Composite Verification & Risk Score
        // ====================================================================
        $verificationScore = 100;
        if ($locationRisk['risk_score'] > 20) {
            $verificationScore -= (int)($locationRisk['risk_score'] * 0.5);
        }
        if ($distanceMeters > ($allowedRadius * 0.8)) {
            $verificationScore -= 10;
        }

        $finalStatus = 'CHECKED_IN';
        if ($verificationScore < 70 || $faceMatchStatus === 'REVIEW_REQUIRED') {
            $finalStatus = 'MANUAL_REVIEW';
        }

        // Generate Human-Readable Attendance Code
        $code = 'ATT-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        // Record in Database using Transaction
        return $db->transaction(function ($db) use (
            $code, $employee, $site, $shift, $todayDate, $nowTime, $finalStatus,
            $verificationScore, $locationRisk, $latitude, $longitude, $accuracy,
            $distanceMeters, $geofenceStatus, $qrToken, $qrStatus, $selfiePath,
            $faceMatchStatus, $deviceId
        ) {
            // 1. Insert Master Attendance Record
            $attendanceId = (int)$db->insert('attendance', [
                'attendance_code'    => $code,
                'company_id'         => (int)$employee['company_id'],
                'site_id'            => (int)$site['id'],
                'shift_id'           => (int)$shift['id'],
                'employee_id'        => (int)$employee['id'],
                'attendance_date'    => $todayDate,
                'check_in_time'      => $nowTime,
                'status'             => $finalStatus,
                'verification_score' => $verificationScore,
                'risk_score'         => $locationRisk['risk_score']
            ]);

            // 2. Insert Layer Verification Telemetry
            $db->insert('attendance_verifications', [
                'attendance_id'            => $attendanceId,
                'event_type'               => 'check_in',
                'latitude'                 => $latitude,
                'longitude'                => $longitude,
                'gps_accuracy'             => $accuracy,
                'geofence_distance_meters' => $distanceMeters,
                'geofence_status'          => $geofenceStatus,
                'qr_token'                 => $qrToken ? substr($qrToken, 0, 50) : null,
                'qr_status'                => $qrStatus,
                'selfie_path'              => $selfiePath,
                'face_match_status'        => $faceMatchStatus,
                'device_id'                => $deviceId,
                'ip_address'               => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent'               => substr($_SERVER['HTTP_USER_AGENT'] ?? 'Web', 0, 200)
            ]);

            // 3. Update Shift Roster Status if scheduled
            $db->update(
                'shift_rosters',
                ['status' => 'present'],
                'employee_id = :eid AND shift_id = :sid AND roster_date = :rdate',
                ['eid' => $employee['id'], 'sid' => $shift['id'], 'rdate' => $todayDate]
            );

            AuditService::log(
                "Check-in recorded for {$employee['first_name']} {$employee['last_name']} at {$site['site_name']} (Status: {$finalStatus}, Score: {$verificationScore}%)",
                'attendance',
                $attendanceId,
                'CHECK_IN',
                (int)$employee['company_id'],
                (int)$employee['id']
            );

            return [
                'success'            => true,
                'message'            => "Check-In Verified! Welcome to {$site['site_name']}.",
                'attendance_id'      => $attendanceId,
                'attendance_code'    => $code,
                'check_in_time'      => $nowTime,
                'status'             => $finalStatus,
                'verification_score' => $verificationScore,
                'distance_meters'    => $distanceMeters
            ];
        });
    }

    /**
     * Submit and record a staff check-out event and compute hours
     */
    public static function processCheckOut(int $employeeId, ?string $notes = null): array {
        $db = Database::getInstance();
        $nowTime = date('Y-m-d H:i:s');

        $activeAtt = $db->fetch(
            "SELECT a.*, s.name as shift_name, s.start_time, s.end_time 
             FROM attendance a
             JOIN shifts s ON a.shift_id = s.id
             WHERE a.employee_id = :eid AND a.check_out_time IS NULL AND a.status IN ('CHECKED_IN', 'VERIFIED', 'MANUAL_REVIEW')
             ORDER BY a.check_in_time DESC LIMIT 1",
            ['eid' => $employeeId]
        );

        if (!$activeAtt) {
            return ['success' => false, 'message' => 'No active check-in found to check out from.'];
        }

        $checkInTs = strtotime($activeAtt['check_in_time']);
        $checkOutTs = strtotime($nowTime);
        $totalMinutes = max(0, (int)round(($checkOutTs - $checkInTs) / 60));

        // Standard shift calculation (8 hours = 480 mins)
        $standardMins = 480;
        $overtimeMins = max(0, $totalMinutes - $standardMins);

        $db->update(
            'attendance',
            [
                'check_out_time'     => $nowTime,
                'total_work_minutes' => $totalMinutes,
                'overtime_minutes'   => $overtimeMins,
                'status'             => 'CHECKED_OUT'
            ],
            'id = :id',
            ['id' => $activeAtt['id']]
        );

        AuditService::log(
            "Check-out finalized for attendance #{$activeAtt['id']} (Total: " . round($totalMinutes / 60, 2) . " hrs, OT: " . round($overtimeMins / 60, 2) . " hrs)",
            'attendance',
            (int)$activeAtt['id'],
            'CHECK_OUT'
        );

        return [
            'success'            => true,
            'message'            => 'Check-Out recorded successfully. Thank you for your service!',
            'check_out_time'     => $nowTime,
            'total_work_minutes' => $totalMinutes,
            'total_hours'        => round($totalMinutes / 60, 2),
            'overtime_hours'     => round($overtimeMins / 60, 2)
        ];
    }
}
