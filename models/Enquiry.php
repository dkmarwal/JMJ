<?php
/**
 * JMJ Enterprises Solutions - Enquiry & Lead Model
 */

declare(strict_types=1);

namespace Models;

use Core\Database;

class Enquiry {
    public static function create(array $data): int {
        $db = Database::getInstance();
        return $db->insert('enquiries', [
            'type' => $data['type'] ?? 'general',
            'name' => trim($data['name']),
            'company' => trim($data['company'] ?? ''),
            'email' => trim($data['email']),
            'phone' => trim($data['phone']),
            'service_required' => trim($data['service_required'] ?? ''),
            'location' => trim($data['location'] ?? ''),
            'preferred_contact' => trim($data['preferred_contact'] ?? 'phone'),
            'message' => trim($data['message']),
            'status' => 'new',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    }

    public static function getRecent(int $limit = 5): array {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT * FROM enquiries WHERE is_archived = 0 ORDER BY id DESC LIMIT :lim",
            ['lim' => $limit]
        );
    }
}
