<?php

namespace EasyCart\View;

/**
 * View_Admin — Admin Panel View
 */
class View_Admin extends View_Abstract
{
    private $templateMap = [
        'import_export' => 'admin/import_export.php',
        'dashboard' => 'admin/dashboard.php',
    ];

    public function __construct(array $data = [], string $viewType = 'dashboard')
    {
        parent::__construct($data);

        $template = $this->templateMap[$viewType] ?? 'admin/dashboard.php';
        $this->template = __DIR__ . '/Templates/' . $template;
    }
}
