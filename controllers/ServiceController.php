<?php
/**
 * JMJ Enterprises Solutions - Service Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\View;
use Models\Service;
use Services\SeoService;

class ServiceController {
    public function index(): void {
        $securityServices = Service::allActiveByCategory('security-services');
        $cleaningServices = Service::allActiveByCategory('cleaning-services');
        $seo = SeoService::getMetaForRoute('security-services');

        View::render('services/index', [
            'securityServices' => $securityServices,
            'cleaningServices' => $cleaningServices,
            'seo'              => $seo,
            'breadcrumbs'      => [
                ['name' => 'Services Hub', 'url' => url('services')]
            ]
        ]);
    }

    public function securityHub(): void {
        $services = Service::allActiveByCategory('security-services');
        $seo = SeoService::getMetaForRoute('security-services');

        View::render('services/security', [
            'services'    => $services,
            'seo'         => $seo,
            'breadcrumbs' => [
                ['name' => 'Security Services', 'url' => url('security-services')]
            ]
        ]);
    }

    public function cleaningHub(): void {
        $services = Service::allActiveByCategory('cleaning-services');
        $seo = SeoService::getMetaForRoute('cleaning-services');

        View::render('services/cleaning', [
            'services'    => $services,
            'seo'         => $seo,
            'breadcrumbs' => [
                ['name' => 'Cleaning Services', 'url' => url('cleaning-services')]
            ]
        ]);
    }

    public function detail(string $slug): void {
        $service = Service::findBySlug($slug);
        if (!$service) {
            http_response_code(404);
            View::render('pages/404', ['pageTitle' => 'Service Not Found']);
            return;
        }

        $hubUrl = url($service['category_slug']);
        $hubName = $service['category_name'];

        $seo = [
            'meta_title'       => $service['meta_title'] ?: $service['name'] . ' | JMJ Enterprises Solutions',
            'meta_description' => $service['meta_description'] ?: $service['short_summary'],
            'meta_keywords'    => $service['meta_keywords'] ?: $service['name'],
            'canonical_url'    => url($service['category_slug'] . '/' . $service['slug']),
            'og_image'         => upload_url($service['hero_image']),
            'robots'           => 'index, follow'
        ];

        View::render('services/detail', [
            'service'     => $service,
            'seo'         => $seo,
            'breadcrumbs' => [
                ['name' => $hubName, 'url' => $hubUrl],
                ['name' => $service['name'], 'url' => url($service['category_slug'] . '/' . $service['slug'])]
            ]
        ]);
    }
}
