<?php

namespace EasyCart\Controllers\Admin;

use EasyCart\Services\AuthService;
use EasyCart\Services\ImportExportService;

class ImportExportController
{
    private $service;
    private $categoryRepository;

    public function __construct()
    {
        $this->service = new ImportExportService();
        $this->categoryRepository = new \EasyCart\Repositories\CategoryRepository();
    }

    /**
     * Show Import/Export Dashboard
     */
    public function index()
    {
        if (!AuthService::check()) {
            header('Location: /login');
            exit;
        }

        // Ideally check for admin role here
        // if (!AuthService::isAdmin()) ...

        $categories = $this->categoryRepository->getAll();
        $page_title = 'Product Import/Export';
        include __DIR__ . '/../../Views/layouts/header.php';
        include __DIR__ . '/../../Views/admin/import_export.php';
        include __DIR__ . '/../../Views/layouts/footer.php';
    }

    /**
     * Handle CSV Import
     */
    public function import()
    {
        if (!AuthService::check()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/import-export');
            exit;
        }

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Please select a valid CSV file.";
            header('Location: /admin/import-export');
            exit;
        }

        $file = $_FILES['csv_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($ext !== 'csv') {
            $_SESSION['error'] = "Only CSV files are allowed.";
            header('Location: /admin/import-export');
            exit;
        }

        // Parse and Import
        $data = $this->service->parseCsv($file['tmp_name']);

        if (empty($data)) {
            $_SESSION['error'] = "CSV file is empty or invalid format.";
            header('Location: /admin/import-export');
            exit;
        }

        $result = $this->service->importProducts($data);

        $_SESSION['success'] = "Import Completed: {$result['success']} imported, {$result['skipped']} skipped.";
        if (!empty($result['errors'])) {
            $_SESSION['import_errors'] = $result['errors'];
        }

        header('Location: /admin/import-export');
        exit;
    }

    /**
     * Handle CSV Export
     */
    public function export()
    {
        if (!AuthService::check()) {
            header('Location: /login');
            exit;
        }

        $result = $this->service->exportCsv();

        if (!$result) {
            $_SESSION['error'] = "No products found to export.";
            header('Location: /admin/import-export');
            exit;
        }

        // Force download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $result['content'];
        exit;
    }
}
