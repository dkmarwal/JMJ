<?php
/**
 * JMJ Enterprises Solutions - Contact Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\View;
use Services\SeoService;

class ContactController {
    public function index(): void {
        $seo = SeoService::getMetaForRoute('contact');

        View::render('contact/index', [
            'seo'         => $seo,
            'breadcrumbs' => [
                ['name' => 'Contact Us', 'url' => url('contact')]
            ]
        ]);
    }
}
