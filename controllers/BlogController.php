<?php
/**
 * JMJ Enterprises Solutions - Blog Frontend Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\View;
use Models\BlogPost;
use Models\Category;
use Services\SeoService;

class BlogController {
    public function index(): void {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $categorySlug = $_GET['category'] ?? null;
        $tagSlug = $_GET['tag'] ?? null;
        $search = $_GET['q'] ?? null;

        $blogData = BlogPost::getPublishedPaginated($page, 9, $categorySlug, $tagSlug, $search);
        $categories = Category::allBlogCategories();
        $tags = Category::allBlogTags();
        $seo = SeoService::getMetaForRoute('blog');

        $breadcrumbs = [
            ['name' => 'Blog & Insights', 'url' => url('blog')]
        ];

        if ($categorySlug) {
            $breadcrumbs[] = ['name' => ucfirst(str_replace('-', ' ', $categorySlug)), 'url' => url('blog/category/' . $categorySlug)];
        }

        View::render('blog/index', [
            'posts'        => $blogData['posts'],
            'total'        => $blogData['total'],
            'page'         => $blogData['page'],
            'totalPages'   => $blogData['totalPages'],
            'categories'   => $categories,
            'tags'         => $tags,
            'currentCat'   => $categorySlug,
            'currentTag'   => $tagSlug,
            'searchQuery'  => $search,
            'seo'          => $seo,
            'breadcrumbs'  => $breadcrumbs
        ]);
    }

    public function detail(string $slug): void {
        $post = BlogPost::findBySlug($slug);
        if (!$post) {
            http_response_code(404);
            View::render('pages/404', ['pageTitle' => 'Article Not Found']);
            return;
        }

        $categories = Category::allBlogCategories();
        $tags = Category::allBlogTags();

        $seo = [
            'meta_title'       => $post['meta_title'] ?: $post['title'] . ' | JMJ Insights',
            'meta_description' => $post['meta_description'] ?: $post['short_description'],
            'meta_keywords'    => $post['meta_keywords'] ?: $post['title'],
            'canonical_url'    => $post['canonical_url'] ?: url('blog/' . $post['slug']),
            'og_image'         => upload_url($post['featured_image']),
            'robots'           => 'index, follow'
        ];

        View::render('blog/detail', [
            'post'        => $post,
            'categories'  => $categories,
            'tags'        => $tags,
            'seo'         => $seo,
            'isArticle'   => true,
            'breadcrumbs' => [
                ['name' => 'Blog', 'url' => url('blog')],
                ['name' => $post['category_name'], 'url' => url('blog?category=' . $post['category_slug'])],
                ['name' => $post['title'], 'url' => url('blog/' . $post['slug'])]
            ]
        ]);
    }

    public function category(string $slug): void {
        $_GET['category'] = $slug;
        $this->index();
    }

    public function tag(string $slug): void {
        $_GET['tag'] = $slug;
        $this->index();
    }

    public function search(): void {
        $this->index();
    }
}
