<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * High-Precision Geofence & GPS Spoof Detection Engine
 */

declare(strict_types=1);

namespace Services;

class GeofenceService {
    /**
     * Calculate Great-Circle Distance between two GPS coordinates using the Haversine formula
     * @return float Distance in meters
     */
    public static function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float {
        $earthRadius = 6371000; // Earth radius in meters

        $latFrom = deg2rad($lat1);
        $lngFrom = deg2rad($lng1);
        $latTo   = deg2rad($lat2);
        $lngTo   = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)
        ));

        return round($angle * $earthRadius, 2);
    }

    /**
     * Ray-Casting algorithm to verify if GPS point is inside a polygon perimeter
     */
    public static function isInsidePolygon(float $latitude, float $longitude, array $polygonVertices): bool {
        $inside = false;
        $numVertices = count($polygonVertices);
        if ($numVertices < 3) {
            return false;
        }

        $j = $numVertices - 1;
        for ($i = 0; $i < $numVertices; $i++) {
            $vertexI = $polygonVertices[$i];
            $vertexJ = $polygonVertices[$j];

            $latI = (float)$vertexI[0];
            $lngI = (float)$vertexI[1];
            $latJ = (float)$vertexJ[0];
            $lngJ = (float)$vertexJ[1];

            if ((($lngI > $longitude) !== ($lngJ > $longitude)) &&
                ($latitude < ($latJ - $latI) * ($longitude - $lngI) / ($lngJ - $lngI) + $latI)) {
                $inside = !$inside;
            }
            $j = $i;
        }

        return $inside;
    }

    /**
     * Best-effort GPS spoofing and anomaly risk calculation
     * @return array [risk_score (0-100), flags, status]
     */
    public static function evaluateLocationRisk(float $latitude, float $longitude, float $accuracy, ?array $previousLocation = null): array {
        $riskScore = 0;
        $flags = [];

        // 1. Check GPS accuracy radius
        if ($accuracy > 150) {
            $riskScore += 45;
            $flags[] = "High GPS uncertainty (accuracy: {$accuracy}m)";
        } elseif ($accuracy > 80) {
            $riskScore += 20;
            $flags[] = "Moderate GPS accuracy ({$accuracy}m)";
        } elseif ($accuracy <= 0) {
            $riskScore += 50;
            $flags[] = "Suspicious 0m GPS accuracy reported by device";
        }

        // 2. Velocity / Impossible jump check if previous location timestamp exists
        if ($previousLocation && !empty($previousLocation['latitude']) && !empty($previousLocation['timestamp'])) {
            $prevLat = (float)$previousLocation['latitude'];
            $prevLng = (float)$previousLocation['longitude'];
            $prevTime = (int)$previousLocation['timestamp'];

            $timeDiff = time() - $prevTime;
            if ($timeDiff > 0 && $timeDiff < 3600) { // Within 1 hour
                $distMeters = self::calculateDistance($prevLat, $prevLng, $latitude, $longitude);
                $speedKmh = ($distMeters / 1000) / ($timeDiff / 3600);

                if ($speedKmh > 120) {
                    $riskScore += 60;
                    $flags[] = "Impossible transit velocity detected ({$speedKmh} km/h jump in {$timeDiff}s)";
                }
            }
        }

        $riskScore = min(100, $riskScore);
        $status = $riskScore >= 60 ? 'SUSPICIOUS' : ($riskScore >= 30 ? 'MANUAL_REVIEW' : 'VALID');

        return [
            'risk_score' => $riskScore,
            'flags'      => $flags,
            'status'     => $status
        ];
    }
}
