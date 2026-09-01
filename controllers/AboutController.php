<?php
/**
 * JMJ Enterprises Solutions - About Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\View;
use Models\Testimonial;
use Services\SeoService;

class AboutController {
    public function index(): void {
        $testimonials = Testimonial::allApproved();
        $seo = SeoService::getMetaForRoute('about');

        View::render('about/index', [
            'testimonials' => $testimonials,
            'seo'          => $seo,
            'breadcrumbs'  => [
                ['name' => 'About Us', 'url' => url('about')]
            ]
        ]);
    }
}
