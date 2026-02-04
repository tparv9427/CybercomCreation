<?php

namespace EasyCart\Core;

class View
{
    /**
     * Render a view file with data
     * 
     * @param string $viewPath Relative path from app/Views/ (e.g. 'components/cart_item')
     * @param array $data Associative array of variables to extract
     * @return string Rendered HTML
     */
    public static function render($viewPath, $data = [])
    {
        if (is_array($data)) {
            extract($data, EXTR_OVERWRITE);
        }

        // Sanitize path (simple check)
        $viewPath = trim($viewPath, '/');
        // Allow omitting .php extension
        if (!str_ends_with($viewPath, '.php')) {
            $viewPath .= '.php';
        }

        $fullPath = __DIR__ . '/../Views/' . $viewPath;

        if (!file_exists($fullPath)) {
            return "<!-- View not found: $viewPath -->";
        }

        ob_start();
        include $fullPath;
        return ob_get_clean();
    }
}

// Helper for endsWith since PHP < 8 doesn't have str_ends_with reliably everywhere
function str_endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if (!$length) {
        return true;
    }
    return substr($haystack, -$length) === $needle;
}
