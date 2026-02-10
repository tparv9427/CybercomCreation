<?php

namespace EasyCart\View;

/**
 * View_Admin_ImportExport — Admin Import/Export Page
 */
class View_Admin_ImportExport extends View_Abstract
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->template = __DIR__ . '/Templates/admin/import_export.php';
    }
}
