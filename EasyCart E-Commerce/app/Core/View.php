<?php

namespace EasyCart\Core;

/**
 * View Core Class
 * 
 * Handles rendering of partial views and components.
 * Used primarily for AJAX responses needing HTML fragments.
 */
class View
{
    /**
     * Render a view file with data and return as string
     * 
     * @param string $viewPath Path relative to app/Views (e.g. "components/cart_item")
     * @param array $data Data to make available in the view
     * @return string Rendered HTML
     * @throws \Exception If view file not found
     */
    public static function render(string $viewPath, array $data = []): string
    {
        $viewPath = str_replace('.php', '', $viewPath);
        $file = __DIR__ . '/../View/Templates/' . $viewPath . '.php';

        if (!file_exists($file)) {
            // Absolute path fallback
            if (file_exists($viewPath)) {
                $file = $viewPath;
            } else {
                error_log("View file not found: {$file}");
                return "<!-- View not found: {$viewPath} -->";
            }
        }

        extract($data);
        ob_start();

        try {
            include $file;
        } catch (\Throwable $e) {
            ob_end_clean();
            error_log("Error rendering view {$viewPath}: " . $e->getMessage());
            return "<!-- Error rendering view: {$viewPath} -->";
        }

        return ob_get_clean();
    }
}
