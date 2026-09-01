<?php
/**
 * JMJ Enterprises Solutions - Blog Management & Revisions Service
 * Hawks Infotech Blog Desk Architecture
 */

declare(strict_types=1);

namespace Services;

use Core\Database;
use Core\Auth;

class BlogService {
    public static function create(array $data): int {
        $db = Database::getInstance();
        $userId = Auth::id() ?? 1;
        $slug = !empty($data['slug']) ? slugify($data['slug']) : slugify($data['title']);

        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while ($db->fetchColumn("SELECT COUNT(*) FROM blog_posts WHERE slug = :s", ['s' => $slug]) > 0) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $readingTime = calculate_reading_time($data['content'] ?? '');
        $status = $data['status'] ?? 'draft';
        $publishAt = ($status === 'published' && empty($data['publish_at'])) ? date('Y-m-d H:i:s') : (!empty($data['publish_at']) ? $data['publish_at'] : null);

        $postId = $db->insert('blog_posts', [
            'title'             => trim($data['title']),
            'slug'              => $slug,
            'author_id'         => $userId,
            'category_id'       => (int)$data['category_id'],
            'featured_image'    => $data['featured_image'] ?? null,
            'short_description' => trim($data['short_description'] ?? ''),
            'content'           => $data['content'] ?? '',
            'reading_time'      => $readingTime,
            'meta_title'        => $data['meta_title'] ?? $data['title'],
            'meta_description'  => $data['meta_description'] ?? ($data['short_description'] ?? ''),
            'meta_keywords'     => $data['meta_keywords'] ?? '',
            'focus_keyword'     => $data['focus_keyword'] ?? '',
            'canonical_url'     => $data['canonical_url'] ?? '',
            'og_image'          => $data['og_image'] ?? $data['featured_image'] ?? null,
            'status'            => $status,
            'publish_at'        => $publishAt,
            'is_featured'       => isset($data['is_featured']) ? 1 : 0
        ]);

        // Attach tags
        if (!empty($data['tags'])) {
            self::syncTags($postId, (array)$data['tags']);
        }

        AuditService::log("Created blog post: {$data['title']}", 'blog', $postId, 'CREATE');

        return $postId;
    }

    public static function update(int $id, array $data): void {
        $db = Database::getInstance();
        $current = $db->fetchOne("SELECT * FROM blog_posts WHERE id = :id", ['id' => $id]);
        if (!$current) {
            return;
        }

        $userId = Auth::id() ?? 1;

        // Create revision snapshot before modifying
        $db->insert('blog_revisions', [
            'post_id'           => $id,
            'user_id'           => $userId,
            'title'             => $current['title'],
            'short_description' => $current['short_description'],
            'content'           => $current['content'],
            'revision_notes'    => 'Snapshot before update on ' . date('Y-m-d H:i:s')
        ]);

        $slug = !empty($data['slug']) ? slugify($data['slug']) : slugify($data['title']);
        $existing = $db->fetchColumn("SELECT id FROM blog_posts WHERE slug = :s AND id != :id", ['s' => $slug, 'id' => $id]);
        if ($existing) {
            $slug .= '-' . time();
        }

        $readingTime = calculate_reading_time($data['content'] ?? '');
        $status = $data['status'] ?? $current['status'];
        $publishAt = $current['publish_at'];
        if ($status === 'published' && empty($publishAt)) {
            $publishAt = date('Y-m-d H:i:s');
        } elseif (!empty($data['publish_at'])) {
            $publishAt = $data['publish_at'];
        }

        $updateData = [
            'title'             => trim($data['title']),
            'slug'              => $slug,
            'category_id'       => (int)$data['category_id'],
            'short_description' => trim($data['short_description'] ?? ''),
            'content'           => $data['content'] ?? '',
            'reading_time'      => $readingTime,
            'meta_title'        => $data['meta_title'] ?? $data['title'],
            'meta_description'  => $data['meta_description'] ?? '',
            'meta_keywords'     => $data['meta_keywords'] ?? '',
            'focus_keyword'     => $data['focus_keyword'] ?? '',
            'canonical_url'     => $data['canonical_url'] ?? '',
            'status'            => $status,
            'publish_at'        => $publishAt,
            'is_featured'       => isset($data['is_featured']) ? 1 : 0
        ];

        if (!empty($data['featured_image'])) {
            $updateData['featured_image'] = $data['featured_image'];
        }
        if (!empty($data['og_image'])) {
            $updateData['og_image'] = $data['og_image'];
        }

        $db->update('blog_posts', $updateData, 'id = :id', ['id' => $id]);

        if (isset($data['tags'])) {
            self::syncTags($id, (array)$data['tags']);
        }

        AuditService::log("Updated blog post #{$id}: {$data['title']}", 'blog', $id, 'UPDATE');
    }

    public static function syncTags(int $postId, array $tagNamesOrIds): void {
        $db = Database::getInstance();
        $db->query("DELETE FROM blog_post_tags WHERE post_id = :pid", ['pid' => $postId]);

        foreach ($tagNamesOrIds as $tag) {
            $tag = trim((string)$tag);
            if (empty($tag)) continue;

            if (is_numeric($tag)) {
                $tagId = (int)$tag;
            } else {
                $slug = slugify($tag);
                $tagId = $db->fetchColumn("SELECT id FROM blog_tags WHERE slug = :s", ['s' => $slug]);
                if (!$tagId) {
                    $tagId = $db->insert('blog_tags', ['name' => $tag, 'slug' => $slug]);
                }
            }

            if ($tagId) {
                $db->query("INSERT IGNORE INTO blog_post_tags (post_id, tag_id) VALUES (:p, :t)", ['p' => $postId, 't' => $tagId]);
            }
        }
    }

    public static function publishDueScheduledPosts(): void {
        $db = Database::getInstance();
        $db->query(
            "UPDATE blog_posts 
             SET status = 'published' 
             WHERE status = 'scheduled' AND publish_at <= NOW() AND is_archived = 0"
        );
    }
}
