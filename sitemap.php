<?php
/**
 * JMJ Enterprises Solutions - Dynamic XML Sitemap Generator
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';

use Core\Database;

header('Content-Type: application/xml; charset=UTF-8');

$type = $_GET['type'] ?? 'main';
$db = Database::getInstance();

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;

if ($type === 'blog') {
    $posts = $db->fetchAll(
        "SELECT slug, publish_at, updated_at FROM blog_posts WHERE status = 'published' AND is_archived = 0 ORDER BY publish_at DESC"
    );
    ?>
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
        <?php foreach ($posts as $p): ?>
            <url>
                <loc><?= url('blog/' . $p['slug']) ?></loc>
                <lastmod><?= date('Y-m-d', strtotime($p['updated_at'] ?: $p['publish_at'])) ?></lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.8</priority>
            </url>
        <?php endforeach; ?>
    </urlset>
    <?php
    exit;
}

if ($type === 'service') {
    $services = $db->fetchAll(
        "SELECT s.slug, s.updated_at, c.slug as category_slug 
         FROM services s 
         JOIN service_categories c ON s.category_id = c.id 
         WHERE s.status = 'published' AND s.is_archived = 0"
    );
    ?>
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
        <url>
            <loc><?= url('security-services') ?></loc>
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
        </url>
        <url>
            <loc><?= url('cleaning-services') ?></loc>
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
        </url>
        <?php foreach ($services as $s): ?>
            <url>
                <loc><?= url($s['category_slug'] . '/' . $s['slug']) ?></loc>
                <lastmod><?= date('Y-m-d', strtotime($s['updated_at'] ?: 'now')) ?></lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.9</priority>
            </url>
        <?php endforeach; ?>
    </urlset>
    <?php
    exit;
}

// Main Primary XML Sitemap
$staticPages = [
    ['loc' => url(), 'priority' => '1.0', 'freq' => 'daily'],
    ['loc' => url('about'), 'priority' => '0.8', 'freq' => 'monthly'],
    ['loc' => url('security-services'), 'priority' => '0.9', 'freq' => 'weekly'],
    ['loc' => url('cleaning-services'), 'priority' => '0.9', 'freq' => 'weekly'],
    ['loc' => url('blog'), 'priority' => '0.8', 'freq' => 'daily'],
    ['loc' => url('gallery'), 'priority' => '0.7', 'freq' => 'monthly'],
    ['loc' => url('contact'), 'priority' => '0.8', 'freq' => 'monthly'],
    ['loc' => url('get-a-quote'), 'priority' => '0.9', 'freq' => 'monthly'],
    ['loc' => url('privacy-policy'), 'priority' => '0.3', 'freq' => 'yearly'],
    ['loc' => url('terms-conditions'), 'priority' => '0.3', 'freq' => 'yearly']
];

$allServices = $db->fetchAll("SELECT s.slug, c.slug as category_slug FROM services s JOIN service_categories c ON s.category_id = c.id WHERE s.status = 'published' AND s.is_archived = 0");
$allBlogs = $db->fetchAll("SELECT slug, publish_at FROM blog_posts WHERE status = 'published' AND is_archived = 0");
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($staticPages as $page): ?>
        <url>
            <loc><?= e($page['loc']) ?></loc>
            <changefreq><?= $page['freq'] ?></changefreq>
            <priority><?= $page['priority'] ?></priority>
        </url>
    <?php endforeach; ?>

    <?php foreach ($allServices as $srv): ?>
        <url>
            <loc><?= url($srv['category_slug'] . '/' . $srv['slug']) ?></loc>
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
        </url>
    <?php endforeach; ?>

    <?php foreach ($allBlogs as $b): ?>
        <url>
            <loc><?= url('blog/' . $b['slug']) ?></loc>
            <lastmod><?= date('Y-m-d', strtotime($b['publish_at'])) ?></lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    <?php endforeach; ?>
</urlset>
