<?php
/**
 * JMJ Enterprises Solutions - Service Model
 */

declare(strict_types=1);

namespace Models;

use Core\Database;

class Service {
    public static function allActiveByCategory(string $categorySlug): array {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT s.*, c.name as category_name, c.slug as category_slug 
             FROM services s 
             JOIN service_categories c ON s.category_id = c.id 
             WHERE c.slug = :cslug AND s.status = 'published' AND s.is_archived = 0 
             ORDER BY s.display_order ASC, s.name ASC",
            ['cslug' => $categorySlug]
        );
    }

    public static function allPublished(): array {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT s.*, c.name as category_name, c.slug as category_slug 
             FROM services s 
             JOIN service_categories c ON s.category_id = c.id 
             WHERE s.status = 'published' AND s.is_archived = 0 
             ORDER BY c.display_order ASC, s.display_order ASC"
        );
    }

    public static function findBySlug(string $slug): ?array {
        $db = Database::getInstance();
        $service = $db->fetchOne(
            "SELECT s.*, c.name as category_name, c.slug as category_slug 
             FROM services s 
             JOIN service_categories c ON s.category_id = c.id 
             WHERE s.slug = :slug AND s.is_archived = 0 
             LIMIT 1",
            ['slug' => $slug]
        );

        if (!$service) {
            return null;
        }

        // Increment view count
        $db->query("UPDATE services SET views = views + 1 WHERE id = :id", ['id' => $service['id']]);

        // Attach features
        $service['features'] = $db->fetchAll(
            "SELECT * FROM service_features WHERE service_id = :sid ORDER BY display_order ASC",
            ['sid' => $service['id']]
        );

        // Attach FAQs
        $service['faqs'] = $db->fetchAll(
            "SELECT * FROM service_faqs WHERE service_id = :sid ORDER BY display_order ASC",
            ['sid' => $service['id']]
        );

        // Attach related services in same category
        $service['related'] = $db->fetchAll(
            "SELECT id, name, slug, short_summary, hero_image, icon 
             FROM services 
             WHERE category_id = :cid AND id != :id AND status = 'published' AND is_archived = 0 
             ORDER BY display_order ASC LIMIT 4",
            ['cid' => $service['category_id'], 'id' => $service['id']]
        );

        return $service;
    }

    public static function getFeatured(int $limit = 6): array {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT s.*, c.name as category_name, c.slug as category_slug 
             FROM services s 
             JOIN service_categories c ON s.category_id = c.id 
             WHERE s.is_featured = 1 AND s.status = 'published' AND s.is_archived = 0 
             ORDER BY c.display_order ASC, s.display_order ASC LIMIT :lim",
            ['lim' => $limit]
        );
    }
}
