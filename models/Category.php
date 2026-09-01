<?php
/**
 * JMJ Enterprises Solutions - Category Model
 */

declare(strict_types=1);

namespace Models;

use Core\Database;

class Category {
    public static function allServiceCategories(): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM service_categories WHERE is_archived = 0 ORDER BY display_order ASC");
    }

    public static function allBlogCategories(): array {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT c.*, COUNT(p.id) as post_count 
             FROM blog_categories c 
             LEFT JOIN blog_posts p ON c.id = p.category_id AND p.status = 'published' AND p.is_archived = 0 
             WHERE c.is_archived = 0 
             GROUP BY c.id 
             ORDER BY c.name ASC"
        );
    }

    public static function allBlogTags(): array {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT t.*, COUNT(pt.post_id) as post_count 
             FROM blog_tags t 
             LEFT JOIN blog_post_tags pt ON t.id = pt.tag_id 
             GROUP BY t.id 
             ORDER BY post_count DESC, t.name ASC"
        );
    }
}
