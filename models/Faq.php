<?php
/**
 * JMJ Enterprises Solutions - FAQ Model
 */

declare(strict_types=1);

namespace Models;

use Core\Database;

class Faq {
    public static function allGrouped(): array {
        $db = Database::getInstance();
        $faqs = $db->fetchAll(
            "SELECT * FROM faqs WHERE status = 'active' AND is_archived = 0 ORDER BY category ASC, display_order ASC"
        );
        $grouped = [];
        foreach ($faqs as $f) {
            $cat = $f['category'] ?: 'general';
            $grouped[$cat][] = $f;
        }
        return $grouped;
    }

    public static function getByCategory(string $category): array {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT * FROM faqs WHERE category = :cat AND status = 'active' AND is_archived = 0 ORDER BY display_order ASC",
            ['cat' => $category]
        );
    }
}
