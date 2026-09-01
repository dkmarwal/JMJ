<?php
/**
 * JMJ Enterprises Solutions - Service Management Service
 */

declare(strict_types=1);

namespace Services;

use Core\Database;

class ServiceService {
    public static function create(array $data): int {
        $db = Database::getInstance();
        $slug = !empty($data['slug']) ? slugify($data['slug']) : slugify($data['name']);

        $originalSlug = $slug;
        $counter = 1;
        while ($db->fetchColumn("SELECT COUNT(*) FROM services WHERE slug = :s", ['s' => $slug]) > 0) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $serviceId = $db->insert('services', [
            'category_id'          => (int)$data['category_id'],
            'name'                 => trim($data['name']),
            'slug'                 => $slug,
            'icon'                 => $data['icon'] ?? 'fas fa-shield-halved',
            'short_summary'        => trim($data['short_summary']),
            'overview'             => $data['overview'] ?? '',
            'hero_image'           => $data['hero_image'] ?? null,
            'target_sectors'       => $data['target_sectors'] ?? '',
            'methodology'          => $data['methodology'] ?? '',
            'standards_compliance' => $data['standards_compliance'] ?? '',
            'meta_title'           => $data['meta_title'] ?? $data['name'],
            'meta_description'     => $data['meta_description'] ?? $data['short_summary'],
            'meta_keywords'        => $data['meta_keywords'] ?? '',
            'is_featured'          => isset($data['is_featured']) ? 1 : 0,
            'status'               => $data['status'] ?? 'published',
            'display_order'        => (int)($data['display_order'] ?? 0)
        ]);

        self::syncFeaturesAndFaqs($serviceId, $data['features'] ?? [], $data['faqs'] ?? []);
        AuditService::log("Created service: {$data['name']}", 'service', $serviceId, 'CREATE');

        return $serviceId;
    }

    public static function update(int $id, array $data): void {
        $db = Database::getInstance();
        $slug = !empty($data['slug']) ? slugify($data['slug']) : slugify($data['name']);

        $existing = $db->fetchColumn("SELECT id FROM services WHERE slug = :s AND id != :id", ['s' => $slug, 'id' => $id]);
        if ($existing) {
            $slug .= '-' . time();
        }

        $updateData = [
            'category_id'          => (int)$data['category_id'],
            'name'                 => trim($data['name']),
            'slug'                 => $slug,
            'icon'                 => $data['icon'] ?? 'fas fa-shield-halved',
            'short_summary'        => trim($data['short_summary']),
            'overview'             => $data['overview'] ?? '',
            'target_sectors'       => $data['target_sectors'] ?? '',
            'methodology'          => $data['methodology'] ?? '',
            'standards_compliance' => $data['standards_compliance'] ?? '',
            'meta_title'           => $data['meta_title'] ?? $data['name'],
            'meta_description'     => $data['meta_description'] ?? '',
            'meta_keywords'        => $data['meta_keywords'] ?? '',
            'is_featured'          => isset($data['is_featured']) ? 1 : 0,
            'status'               => $data['status'] ?? 'published',
            'display_order'        => (int)($data['display_order'] ?? 0)
        ];

        if (!empty($data['hero_image'])) {
            $updateData['hero_image'] = $data['hero_image'];
        }

        $db->update('services', $updateData, 'id = :id', ['id' => $id]);

        if (isset($data['features']) || isset($data['faqs'])) {
            self::syncFeaturesAndFaqs($id, $data['features'] ?? [], $data['faqs'] ?? []);
        }

        AuditService::log("Updated service #{$id}: {$data['name']}", 'service', $id, 'UPDATE');
    }

    private static function syncFeaturesAndFaqs(int $serviceId, array $features, array $faqs): void {
        $db = Database::getInstance();

        // Features
        $db->query("DELETE FROM service_features WHERE service_id = :sid", ['sid' => $serviceId]);
        foreach ($features as $idx => $f) {
            if (!empty($f['title'])) {
                $db->insert('service_features', [
                    'service_id'    => $serviceId,
                    'title'         => trim($f['title']),
                    'description'   => trim($f['description'] ?? ''),
                    'icon'          => $f['icon'] ?? 'fas fa-check-circle',
                    'display_order' => $idx + 1
                ]);
            }
        }

        // FAQs
        $db->query("DELETE FROM service_faqs WHERE service_id = :sid", ['sid' => $serviceId]);
        foreach ($faqs as $idx => $faq) {
            if (!empty($faq['question']) && !empty($faq['answer'])) {
                $db->insert('service_faqs', [
                    'service_id'    => $serviceId,
                    'question'      => trim($faq['question']),
                    'answer'        => trim($faq['answer']),
                    'display_order' => $idx + 1
                ]);
            }
        }
    }
}
