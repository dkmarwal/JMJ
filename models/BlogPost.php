<?php
/**
 * JMJ Enterprises Solutions - BlogPost Model
 */

declare(strict_types=1);

namespace Models;

use Core\Database;

class BlogPost {
    public static function getPublishedPaginated(int $page = 1, int $limit = 9, ?string $categorySlug = null, ?string $tagSlug = null, ?string $search = null): array {
        $db = Database::getInstance();
        $offset = ($page - 1) * $limit;
        $where = "WHERE p.status = 'published' AND (p.publish_at IS NULL OR p.publish_at <= NOW()) AND p.is_archived = 0";
        $params = [];

        if (!empty($categorySlug)) {
            $where .= " AND c.slug = :cslug";
            $params['cslug'] = $categorySlug;
        }

        if (!empty($tagSlug)) {
            $where .= " AND p.id IN (SELECT post_id FROM blog_post_tags pt JOIN blog_tags t ON pt.tag_id = t.id WHERE t.slug = :tslug)";
            $params['tslug'] = $tagSlug;
        }

        if (!empty($search)) {
            $where .= " AND (p.title LIKE :search OR p.short_description LIKE :search OR p.content LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        // Total count
        $countSql = "SELECT COUNT(*) FROM blog_posts p 
                     JOIN blog_categories c ON p.category_id = c.id 
                     {$where}";
        $total = (int)$db->fetchColumn($countSql, $params);
        $totalPages = (int)ceil($total / $limit);

        // Fetch posts
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, u.name as author_name, u.avatar as author_avatar 
                FROM blog_posts p 
                JOIN blog_categories c ON p.category_id = c.id 
                JOIN users u ON p.author_id = u.id 
                {$where} 
                ORDER BY p.is_featured DESC, p.publish_at DESC, p.id DESC 
                LIMIT {$limit} OFFSET {$offset}";

        $posts = $db->fetchAll($sql, $params);

        return [
            'posts' => $posts,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages
        ];
    }

    public static function findBySlug(string $slug): ?array {
        $db = Database::getInstance();
        $post = $db->fetchOne(
            "SELECT p.*, c.name as category_name, c.slug as category_slug, u.name as author_name, u.bio as author_bio, u.avatar as author_avatar 
             FROM blog_posts p 
             JOIN blog_categories c ON p.category_id = c.id 
             JOIN users u ON p.author_id = u.id 
             WHERE p.slug = :slug AND p.is_archived = 0 
             LIMIT 1",
            ['slug' => $slug]
        );

        if (!$post) {
            return null;
        }

        // Increment views
        $db->query("UPDATE blog_posts SET views = views + 1 WHERE id = :id", ['id' => $post['id']]);

        // Attach tags
        $post['tags'] = $db->fetchAll(
            "SELECT t.* FROM blog_tags t 
             JOIN blog_post_tags pt ON t.id = pt.tag_id 
             WHERE pt.post_id = :pid",
            ['pid' => $post['id']]
        );

        // Related posts
        $post['related'] = $db->fetchAll(
            "SELECT p.id, p.title, p.slug, p.featured_image, p.short_description, p.reading_time, p.publish_at, c.name as category_name, c.slug as category_slug 
             FROM blog_posts p 
             JOIN blog_categories c ON p.category_id = c.id 
             WHERE p.category_id = :cid AND p.id != :id AND p.status = 'published' AND p.is_archived = 0 
             ORDER BY p.publish_at DESC LIMIT 3",
            ['cid' => $post['category_id'], 'id' => $post['id']]
        );

        // Previous & Next posts
        $post['prev'] = $db->fetchOne(
            "SELECT id, title, slug FROM blog_posts 
             WHERE id < :id AND status = 'published' AND is_archived = 0 
             ORDER BY id DESC LIMIT 1",
            ['id' => $post['id']]
        );
        $post['next'] = $db->fetchOne(
            "SELECT id, title, slug FROM blog_posts 
             WHERE id > :id AND status = 'published' AND is_archived = 0 
             ORDER BY id ASC LIMIT 1",
            ['id' => $post['id']]
        );

        return $post;
    }

    public static function getLatest(int $limit = 3): array {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug, u.name as author_name 
             FROM blog_posts p 
             JOIN blog_categories c ON p.category_id = c.id 
             JOIN users u ON p.author_id = u.id 
             WHERE p.status = 'published' AND (p.publish_at IS NULL OR p.publish_at <= NOW()) AND p.is_archived = 0 
             ORDER BY p.publish_at DESC LIMIT :lim",
            ['lim' => $limit]
        );
    }
}
