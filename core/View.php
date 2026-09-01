<?php
/**
 * JMJ Enterprises Solutions - Template View Renderer
 */

declare(strict_types=1);

namespace Core;

use Exception;

class View {
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/main'): void {
        // Extract variables to local scope
        extract($data);

        // Normalize view path
        $viewFile = VIEWS_PATH . '/' . trim($view, '/') . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View template not found: {$viewFile}");
        }

        // Global data available in all views
        $settings = \Services\SettingService::getAll();
        $flashes = Session::getFlashes();
        $currentUser = Auth::user();

        // Render view content into buffer
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // If a layout is specified, render within layout wrapper
        if ($layout !== null && $layout !== '') {
            $layoutFile = VIEWS_PATH . '/' . trim($layout, '/') . '.php';
            if (!file_exists($layoutFile)) {
                throw new Exception("Layout file not found: {$layoutFile}");
            }
            include $layoutFile;
        } else {
            echo $content;
        }
    }

    public static function partial(string $partial, array $data = []): void {
        extract($data);
        $partialFile = VIEWS_PATH . '/' . trim($partial, '/') . '.php';
        if (file_exists($partialFile)) {
            include $partialFile;
        }
    }
}
