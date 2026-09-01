<?php
/**
 * JMJ Enterprises Solutions - Gallery Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\View;
use Models\Gallery;
use Services\SeoService;

class GalleryController {
    public function index(): void {
        $galleryData = Gallery::allWithCategories();
        $seo = SeoService::getMetaForRoute('gallery');

        View::render('gallery/index', [
            'items'       => $galleryData['items'],
            'categories'  => $galleryData['categories'],
            'seo'         => $seo,
            'breadcrumbs' => [
                ['name' => 'Operations Gallery', 'url' => url('gallery')]
            ]
        ]);
    }
}
