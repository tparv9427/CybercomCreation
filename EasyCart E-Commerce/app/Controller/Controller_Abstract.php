<?php

namespace EasyCart\Controller;

use EasyCart\View\View_Layout;
use EasyCart\View\View_Abstract;

/**
 * Controller_Abstract — Base Controller
 * 
 * No SQL, no HTML. Handles request flow only.
 * Uses View classes for rendering (via toHtml()).
 */
abstract class Controller_Abstract
{
    /**
     * Render a content view inside the layout (header + content + footer)
     * 
     * @param View_Abstract $contentView
     * @param array $layoutData Shared data for header/footer (page_title, categories, etc.)
     */
    protected function renderWithLayout(View_Abstract $contentView, array $layoutData = []): void
    {
        $layout = new View_Layout($layoutData);
        $layout->setContentView($contentView);
        $layout->render();
    }

    /**
     * Send a JSON response
     * 
     * @param array $data
     */
    protected function jsonResponse(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Redirect to a URL
     * 
     * @param string $url
     * @param int $code HTTP status code (default 302)
     */
    protected function redirect(string $url, int $code = 302): void
    {
        header('Location: ' . $url, true, $code);
        exit;
    }
}
