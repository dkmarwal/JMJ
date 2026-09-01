<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Automated High-Frequency Scheduled Cron Engine (Every 1 Minute)
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

use Core\Database;
use Services\AuditService;

echo "[" . date('Y-m-d H:i:s') . "] Starting JMJ Workforce Background Cron Cycle...\n";

$db = Database::getInstance();
$today = date('Y-m-d');
$nowTime = date('H:i:s');
$graceMins = (int)($_ENV['NO_SHOW_GRACE_MINUTES'] ?? 30);

// 1. Check for Shift No-Shows
echo "[*] Scanning for Shift No-Shows (Grace threshold: {$graceMins} mins)...\n";
$unattendedRosters = $db->fetchAll(
    "SELECT r.*, s.site_name, sh.name as shift_name, sh.start_time, e.first_name, e.last_name
     FROM shift_rosters r
     JOIN shifts sh ON r.shift_id = sh.id
     JOIN sites s ON r.site_id = s.id
     JOIN employees e ON r.employee_id = e.id
     WHERE r.roster_date = :rdate AND r.status = 'scheduled'
       AND TIMESTAMPDIFF(MINUTE, CONCAT(:tdate, ' ', sh.start_time), NOW()) > :grace",
    ['rdate' => $today, 'tdate' => $today, 'grace' => $graceMins]
);

$noShowCount = 0;
foreach ($unattendedRosters as $roster) {
    $db->update('shift_rosters', ['status' => 'no_show'], 'id = :id', ['id' => $roster['id']]);
    
    // Log No-Show and trigger notification
    $db->insert('notifications', [
        'company_id' => 1,
        'title'      => '⚠️ SHIFT NO-SHOW DETECTED',
        'message'    => "Staff {$roster['first_name']} {$roster['last_name']} failed to check in for {$roster['shift_name']} at {$roster['site_name']}. Standby reliever recommended.",
        'type'       => 'no_show',
        'channel'    => 'in_app',
        'action_url' => 'shifts/relievers?site_id=' . $roster['site_id']
    ]);

    AuditService::log(
        "Auto-flagged No-Show for Staff #{$roster['employee_id']} on Shift #{$roster['shift_id']} at Site #{$roster['site_id']}",
        'roster',
        (int)$roster['id'],
        'AUTO_NOSHOW'
    );
    $noShowCount++;
}
echo "   [✓] {$noShowCount} unattended roster(s) updated to no-show status.\n";

// 2. Cleanup Expired Dynamic QR Tokens
echo "[*] Purging Expired Dynamic QR Tokens...\n";
$deletedTokens = $db->delete('qr_tokens', 'expires_at < DATE_SUB(NOW(), INTERVAL 2 HOUR)');
echo "   [✓] Purged {$deletedTokens} expired dynamic QR token records.\n";

echo "[" . date('Y-m-d H:i:s') . "] Cron Cycle Finished Successfully.\n";
