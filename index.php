<?php
/**
 * JMJ Enterprises Solutions - Main Front Controller & Route Registry
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';

use Core\Router;
use Core\App;

// Public Static Routes
Router::get('/', 'Controllers\HomeController@index');
Router::get('/about', 'Controllers\AboutController@index');
Router::get('/contact', 'Controllers\ContactController@index');
Router::get('/get-a-quote', 'Controllers\QuoteController@index');
Router::get('/gallery', 'Controllers\GalleryController@index');
Router::get('/privacy-policy', 'Controllers\PageController@privacy');
Router::get('/terms-conditions', 'Controllers\PageController@terms');

// Services Routes
Router::get('/services', 'Controllers\ServiceController@index');
Router::get('/security-services', 'Controllers\ServiceController@securityHub');
Router::get('/security-services/{slug}', 'Controllers\ServiceController@detail');
Router::get('/cleaning-services', 'Controllers\ServiceController@cleaningHub');
Router::get('/cleaning-services/{slug}', 'Controllers\ServiceController@detail');

// Blog Routes
Router::get('/blog', 'Controllers\BlogController@index');
Router::get('/blog/search', 'Controllers\BlogController@search');
Router::get('/blog/category/{slug}', 'Controllers\BlogController@category');
Router::get('/blog/tag/{slug}', 'Controllers\BlogController@tag');
Router::get('/blog/{slug}', 'Controllers\BlogController@detail');

// AJAX API Routes
Router::post('/api/lead', 'Controllers\ApiController@submitLead');
Router::post('/api/quote', 'Controllers\ApiController@submitQuote');
Router::post('/api/newsletter', 'Controllers\ApiController@subscribe');

// 404 Fallback
Router::notFound('Controllers\PageController@notFound');

// Run Application
App::run();
