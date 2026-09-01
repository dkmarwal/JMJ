<?php
/**
 * JMJ Enterprises Solutions - Static Pages Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\View;
use Services\SeoService;

class PageController {
    public function privacy(): void {
        $seo = [
            'meta_title'       => 'Privacy Policy | JMJ Enterprises Solutions Ltd.',
            'meta_description' => 'Official privacy policy and data governance practices of JMJ Enterprises Solutions.',
            'canonical_url'    => url('privacy-policy'),
            'robots'           => 'noindex, follow'
        ];

        View::render('pages/privacy', [
            'seo'         => $seo,
            'breadcrumbs' => [
                ['name' => 'Privacy Policy', 'url' => url('privacy-policy')]
            ]
        ]);
    }

    public function terms(): void {
        $seo = [
            'meta_title'       => 'Terms & Conditions | JMJ Enterprises Solutions Ltd.',
            'meta_description' => 'Official terms of service and standard operating conditions of JMJ Enterprises Solutions.',
            'canonical_url'    => url('terms-conditions'),
            'robots'           => 'noindex, follow'
        ];

        View::render('pages/terms', [
            'seo'         => $seo,
            'breadcrumbs' => [
                ['name' => 'Terms & Conditions', 'url' => url('terms-conditions')]
            ]
        ]);
    }

    public function notFound(): void {
        http_response_code(404);
        View::render('pages/404', [
            'pageTitle' => '404 - Page Not Found | JMJ Enterprises Solutions'
        ]);
    }
}
