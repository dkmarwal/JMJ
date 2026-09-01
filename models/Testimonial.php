<?php
/**
 * JMJ Enterprises Solutions - Testimonial Model
 */

declare(strict_types=1);

namespace Models;

use Core\Database;

class Testimonial {
    public static function allApproved(): array {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT * FROM testimonials WHERE status = 'approved' AND is_archived = 0 ORDER BY is_featured DESC, display_order ASC, id DESC"
        );
    }
}
