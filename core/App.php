<?php
/**
 * JMJ Enterprises Solutions - Application Bootstrap
 */

declare(strict_types=1);

namespace Core;

use Throwable;

class App {
    public static function run(): void {
        Session::start();

        // Process any scheduled blogs that are now due for publication
        try {
            \Services\BlogService::publishDueScheduledPosts();
        } catch (Throwable) {
            // Silently continue if database is not yet seeded
        }

        try {
            Router::dispatch();
        } catch (Throwable $e) {
            if (APP_DEBUG) {
                http_response_code(500);
                echo "<div style='font-family:sans-serif;padding:30px;background:#fff1f2;color:#881337;border:1px solid #fecdd3;border-radius:12px;margin:20px;'>";
                echo "<h2 style='margin-top:0;'>Application Exception</h2>";
                echo "<p><strong>Message:</strong> " . e($e->getMessage()) . "</p>";
                echo "<p><strong>File:</strong> " . e($e->getFile()) . " on line " . $e->getLine() . "</p>";
                echo "<pre style='background:#ffe4e6;padding:15px;border-radius:8px;overflow:auto;font-size:12px;'>" . e($e->getTraceAsString()) . "</pre>";
                echo "</div>";
            } else {
                http_response_code(500);
                View::render('pages/404', [
                    'pageTitle' => '500 - Server Error',
                    'errorHeading' => 'Unexpected Application Error',
                    'errorMessage' => 'We are experiencing technical issues. Please try again shortly.'
                ]);
            }
        }
    }
}
