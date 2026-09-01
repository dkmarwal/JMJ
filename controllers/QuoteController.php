<?php
/**
 * JMJ Enterprises Solutions - Quote Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\View;
use Models\Service;
use Services\SeoService;

class QuoteController {
    public function index(): void {
        $securityServices = Service::allActiveByCategory('security-services');
        $cleaningServices = Service::allActiveByCategory('cleaning-services');
        $seo = SeoService::getMetaForRoute('get-a-quote');

        View::render('quote/index', [
            'securityServices' => $securityServices,
            'cleaningServices' => $cleaningServices,
            'seo'              => $seo,
            'breadcrumbs'      => [
                ['name' => 'Get a Free Quote', 'url' => url('get-a-quote')]
            ]
        ]);
    }
}
