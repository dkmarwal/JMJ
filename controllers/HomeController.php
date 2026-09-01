<?php
/**
 * JMJ Enterprises Solutions - Home Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\View;
use Models\Service;
use Models\BlogPost;
use Models\Testimonial;
use Models\Faq;
use Services\SeoService;

class HomeController {
    public function index(): void {
        $securityServices = Service::allActiveByCategory('security-services');
        $cleaningServices = Service::allActiveByCategory('cleaning-services');
        $testimonials = Testimonial::allApproved();
        $latestBlogs = BlogPost::getLatest(3);
        $faqs = Faq::allGrouped();
        $seo = SeoService::getMetaForRoute('home');

        View::render('home/index', [
            'securityServices' => $securityServices,
            'cleaningServices' => $cleaningServices,
            'testimonials'     => $testimonials,
            'latestBlogs'      => $latestBlogs,
            'faqs'             => $faqs,
            'seo'              => $seo
        ]);
    }
}
