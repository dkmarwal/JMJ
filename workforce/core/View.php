<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * View Rendering Engine with Layouts and Partials
 */

declare(strict_types=1);

namespace Core;

use RuntimeException;

class View {
    public static function render(string $view, array $data = [], string $layout = 'main'): void {
        $viewFile = WF_ROOT_PATH . '/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new RuntimeException("View not found: {$view} at {$viewFile}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        if ($layout === '' || $layout === 'none') {
            echo $content;
            return;
        }

        $layoutFile = WF_ROOT_PATH . '/views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            throw new RuntimeException("Layout not found: {$layout} at {$layoutFile}");
        }

        include $layoutFile;
    }

    public static function partial(string $partial, array $data = []): void {
        $clean = ltrim(str_replace(['partials.', 'partials/'], '', $partial), '/');
        $partialFile = WF_ROOT_PATH . '/views/partials/' . str_replace('.', '/', $clean) . '.php';
        
        if (!file_exists($partialFile)) {
            $partialFile = WF_ROOT_PATH . '/views/' . str_replace('.', '/', $partial) . '.php';
        }

        if (file_exists($partialFile)) {
            extract($data, EXTR_SKIP);
            include $partialFile;
        } elseif (WF_APP_DEBUG) {
            echo "<!-- Partial not found: {$partial} ({$partialFile}) -->";
        }
    }
}
