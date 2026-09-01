<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Guard Tour & Real-Time Patrol Deviation Engine
 */

declare(strict_types=1);

namespace Services;

use Core\Database;

class PatrolTourService {
    /**
     * Start a new guard patrol tour
     */
    public static function startTour(int $guardId, int $siteId, int $routeId, int $shiftId): array {
        $db = Database::getInstance();
        $route = $db->fetch("SELECT * FROM patrol_routes WHERE id = :id AND site_id = :sid AND status = 'active'", ['id' => $routeId, 'sid' => $siteId]);
        if (!$route) {
            return ['success' => false, 'message' => 'Active patrol route not found.'];
        }

        $checkpoints = $db->fetchAll("SELECT * FROM patrol_route_checkpoints WHERE route_id = :rid ORDER BY sequence_order ASC", ['rid' => $routeId]);

        $tourId = (int)$db->insert('patrol_tours', [
            'company_id'          => 1,
            'site_id'             => $siteId,
            'route_id'            => $routeId,
            'guard_id'            => $guardId,
            'shift_id'            => $shiftId,
            'start_time'          => date('Y-m-d H:i:s'),
            'total_checkpoints'   => count($checkpoints),
            'scanned_checkpoints' => 0,
            'missed_checkpoints'  => 0,
            'status'              => 'in_progress'
        ]);

        return [
            'success'     => true,
            'message'     => 'Patrol Tour started successfully. Proceed to Checkpoint 1.',
            'tour_id'     => $tourId,
            'route_name'  => $route['name'],
            'checkpoints' => $checkpoints
        ];
    }

    /**
     * Record a physical checkpoint scan with deviation evaluation
     */
    public static function scanCheckpoint(int $tourId, string $qrToken, float $lat = 0, float $lng = 0): array {
        $db = Database::getInstance();
        $tour = $db->fetch("SELECT * FROM patrol_tours WHERE id = :id AND status = 'in_progress'", ['id' => $tourId]);
        if (!$tour) {
            return ['success' => false, 'message' => 'No active patrol tour found in progress.'];
        }

        // Verify Checkpoint by QR token
        $checkpoint = $db->fetch(
            "SELECT cp.*, rcp.sequence_order, rcp.expected_interval_mins 
             FROM site_checkpoints cp
             JOIN patrol_route_checkpoints rcp ON cp.id = rcp.checkpoint_id
             WHERE cp.qr_token = :qr AND cp.site_id = :sid AND rcp.route_id = :rid",
            ['qr' => trim($qrToken), 'sid' => $tour['site_id'], 'rid' => $tour['route_id']]
        );

        if (!$checkpoint) {
            return ['success' => false, 'message' => 'Scanned QR does not match any scheduled checkpoint on this patrol route.'];
        }

        // Check if already scanned on this tour
        $alreadyScanned = $db->fetch("SELECT * FROM patrol_scans WHERE tour_id = :tid AND checkpoint_id = :cid", ['tid' => $tourId, 'cid' => $checkpoint['id']]);
        if ($alreadyScanned) {
            return ['success' => false, 'message' => "Checkpoint '{$checkpoint['checkpoint_name']}' has already been scanned on this tour."];
        }

        // Calculate timing deviation
        $tourStartTs = strtotime($tour['start_time']);
        $nowTs = time();
        $elapsedMins = (int)round(($nowTs - $tourStartTs) / 60);
        $expectedMins = (int)$checkpoint['expected_interval_mins'];
        $toleranceMins = 15;

        $status = 'ON_TIME';
        if ($elapsedMins > ($expectedMins + $toleranceMins)) {
            $status = 'LATE';
        } elseif ($elapsedMins < ($expectedMins - $toleranceMins) && $expectedMins > 0) {
            $status = 'EARLY';
        }

        $db->insert('patrol_scans', [
            'tour_id'       => $tourId,
            'checkpoint_id' => (int)$checkpoint['id'],
            'scan_time'     => date('Y-m-d H:i:s'),
            'latitude'      => $lat ?: null,
            'longitude'     => $lng ?: null,
            'scan_method'   => 'qr',
            'status'        => $status
        ]);

        $newScannedCount = $tour['scanned_checkpoints'] + 1;
        $isComplete = ($newScannedCount >= $tour['total_checkpoints']);

        $db->update(
            'patrol_tours',
            [
                'scanned_checkpoints' => $newScannedCount,
                'status'              => $isComplete ? 'completed' : 'in_progress',
                'end_time'            => $isComplete ? date('Y-m-d H:i:s') : null
            ],
            'id = :id',
            ['id' => $tourId]
        );

        return [
            'success'         => true,
            'message'         => "Checkpoint '{$checkpoint['checkpoint_name']}' verified ({$status}). Progress: {$newScannedCount}/{$tour['total_checkpoints']}",
            'checkpoint_name' => $checkpoint['checkpoint_name'],
            'status'          => $status,
            'is_completed'    => $isComplete
        ];
    }
}
