<?php
/**
 * JMJ Enterprises Solutions - Gallery Model
 */

declare(strict_types=1);

namespace Models;

use Core\Database;

class Gallery {
    public static function allWithCategories(): array {
        $db = Database::getInstance();
        $items = $db->fetchAll(
            "SELECT g.*, c.name as category_name, c.slug as category_slug 
             FROM gallery g 
             JOIN gallery_categories c ON g.category_id = c.id 
             WHERE g.is_archived = 0 
             ORDER BY g.is_featured DESC, g.display_order ASC, g.id DESC"
        );
        $categories = $db->fetchAll(
            "SELECT * FROM gallery_categories WHERE status = 'active' ORDER BY id ASC"
        );
        return [
            'items' => $items,
            'categories' => $categories
        ];
    }
}
