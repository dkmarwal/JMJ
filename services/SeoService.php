<?php
/**
 * JMJ Enterprises Solutions - Enterprise SEO & Schema Engine
 */

declare(strict_types=1);

namespace Services;

use Core\Database;

class SeoService {
    public static function getMetaForRoute(string $route): array {
        try {
            $db = Database::getInstance();
            $meta = $db->fetchOne("SELECT * FROM seo_metadata WHERE page_route = :r LIMIT 1", ['r' => $route]);
            if ($meta) {
                return $meta;
            }
        } catch (\Throwable) {}

        return [
            'meta_title' => APP_NAME . ' | Professional Security & Cleaning Services',
            'meta_description' => 'JMJ Enterprises Solutions is a leading B2B corporate security, manned guarding, and commercial cleaning services company in India.',
            'meta_keywords' => 'security guard services, commercial cleaning, corporate facility management',
            'canonical_url' => url($route === 'home' ? '' : $route),
            'og_image' => asset('img/logo.jpg'),
            'robots' => 'index, follow'
        ];
    }

    public static function renderOrganizationSchema(): string {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => url('#organization'),
            'name' => SettingService::get('company_name', 'JMJ Enterprises Solutions Ltd.'),
            'url' => url(),
            'logo' => asset('img/logo.jpg'),
            'description' => 'Premier B2B Manned Security, Guarding and Commercial Cleaning Services Provider in India.',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => SettingService::get('company_address', '250, Sant Nagar, East of Kailash'),
                'addressLocality' => 'New Delhi',
                'postalCode' => '110065',
                'addressCountry' => 'IN'
            ],
            'contactPoint' => [
                [
                    '@type' => 'ContactPoint',
                    'telephone' => SettingService::get('phone_toll_free', '18008890832'),
                    'contactType' => 'customer service',
                    'areaServed' => 'IN',
                    'availableLanguage' => ['en', 'hi']
                ],
                [
                    '@type' => 'ContactPoint',
                    'telephone' => SettingService::get('phone_primary', '+91-9999381777'),
                    'contactType' => 'emergency dispatch',
                    'areaServed' => 'IN',
                    'availableLanguage' => ['en', 'hi']
                ]
            ],
            'sameAs' => array_filter([
                SettingService::get('social_facebook'),
                SettingService::get('social_linkedin'),
                SettingService::get('social_instagram'),
                SettingService::get('social_youtube')
            ])
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }

    public static function renderBreadcrumbsSchema(array $breadcrumbs): string {
        $items = [];
        foreach ($breadcrumbs as $index => $crumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url']
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }

    public static function renderArticleSchema(array $post): string {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => url('blog/' . $post['slug'])
            ],
            'headline' => $post['title'],
            'description' => $post['short_description'],
            'image' => upload_url($post['featured_image']),
            'datePublished' => date('c', strtotime($post['publish_at'] ?? $post['created_at'])),
            'dateModified' => date('c', strtotime($post['updated_at'] ?? $post['created_at'])),
            'author' => [
                '@type' => 'Person',
                'name' => $post['author_name'] ?? 'JMJ Security Editorial'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => SettingService::get('company_name', 'JMJ Enterprises Solutions Ltd.'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('img/logo.jpg')
                ]
            ]
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }

    public static function renderFaqSchema(array $faqs): string {
        if (empty($faqs)) return '';

        $mainEntity = [];
        foreach ($faqs as $faq) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($faq['answer'])
                ]
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}
