<?php

namespace App\Core;

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);

        // Check for layout
        // Simplistic approach: Views are wrapped in a layout
        // We will assume a main layout for now

        ob_start();
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "View not found: " . $view;
        }
        $content = ob_get_clean();

        // Render Layout
        // If it's an AJAX request (basic check), maybe return JSON or partial?
        // For now, let's keep it simple: always full page unless specified.

        require __DIR__ . '/../Views/layout/main.php';
    }

    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
