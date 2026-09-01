<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Incident Management & Emergency SOS Dispatcher
 */

declare(strict_types=1);

namespace Services;

use Core\Database;

class IncidentService {
    /**
     * Report a new operational incident
     */
    public static function createIncident(array $data): array {
        $db = Database::getInstance();
        $incNumber = 'INC-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $incidentId = (int)$db->insert('incidents', [
            'incident_number'        => $incNumber,
            'company_id'             => (int)($data['company_id'] ?? 1),
            'site_id'                => (int)$data['site_id'],
            'reported_by'            => (int)$data['reported_by'],
            'incident_type'          => $data['incident_type'] ?? 'other',
            'severity'               => $data['severity'] ?? 'MEDIUM',
            'incident_time'          => $data['incident_time'] ?? date('Y-m-d H:i:s'),
            'location_detail'        => $data['location_detail'] ?? 'Site premises',
            'description'            => $data['description'],
            'immediate_action_taken' => $data['immediate_action_taken'] ?? null,
            'witness_details'        => $data['witness_details'] ?? null,
            'status'                 => 'OPEN'
        ]);

        AuditService::log(
            "New Incident {$incNumber} ({$data['severity']}) reported at Site #{$data['site_id']}",
            'incident',
            $incidentId,
            'INCIDENT_CREATED'
        );

        return [
            'success'         => true,
            'message'         => "Incident #{$incNumber} logged successfully. Operations command alerted.",
            'incident_id'     => $incidentId,
            'incident_number' => $incNumber
        ];
    }

    /**
     * Trigger an Emergency SOS Panic Alert
     */
    public static function triggerSOS(int $employeeId, int $siteId, float $lat, float $lng): array {
        $db = Database::getInstance();
        $sosCode = 'SOS-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(2)));

        $sosId = (int)$db->insert('sos_alerts', [
            'sos_code'     => $sosCode,
            'company_id'   => 1,
            'site_id'      => $siteId,
            'employee_id'  => $employeeId,
            'trigger_time' => date('Y-m-d H:i:s'),
            'latitude'     => $lat,
            'longitude'    => $lng,
            'status'       => 'TRIGGERED'
        ]);

        // Insert Urgent Emergency Notification
        $db->insert('notifications', [
            'company_id' => 1,
            'title'      => '🚨 CRITICAL SOS PANIC TRIGGERED',
            'message'    => "Emergency SOS triggered by Staff #{$employeeId} at Site #{$siteId}. Live GPS: ({$lat}, {$lng}). Immediate field response required!",
            'type'       => 'emergency_sos',
            'channel'    => 'in_app',
            'action_url' => "incidents/sos-command?id={$sosId}"
        ]);

        AuditService::log(
            "🚨 CRITICAL SOS ALERT #{$sosCode} triggered by Employee #{$employeeId} at Site #{$siteId}",
            'sos',
            $sosId,
            'SOS_TRIGGERED'
        );

        return [
            'success'      => true,
            'message'      => 'SOS Panic Alert Broadcasted to Operations Command Center and Regional Marshals.',
            'sos_code'     => $sosCode,
            'sos_id'       => $sosId,
            'hotline'      => '18008890832',
            'trigger_time' => date('Y-m-d H:i:s')
        ];
    }
}
