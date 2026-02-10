<?php

namespace EasyCart\Controller;

use EasyCart\View\View_Admin;
use EasyCart\Services\AuthService;

/**
 * Controller_Admin — Admin Panel Controller
 */
class Controller_Admin extends Controller_Abstract
{
    public function __construct()
    {
        // Simple admin check: restrict to specific user ID or role locally
        // For now, we'll just reuse standard auth check, but in production, we need roles
        if (!AuthService::check()) {
            $this->redirect('/login');
        }

        // TODO: Add proper role-based access control here
        // if ($_SESSION['role'] !== 'admin') { $this->redirect('/'); }
    }

    public function importExport()
    {
        // For stats, let's use Resource_Product
        $productResource = new \EasyCart\Resource\Resource_Product();
        $totalProducts = \EasyCart\Database\QueryBuilder::select($productResource->getTable(), ['COUNT(*) as count'])->fetchOne()['count'] ?? 0;

        $view = new View_Admin([
            'total_products' => $totalProducts
        ], 'import_export');

        // Fetch categories for layout
        $categoryCollection = new \EasyCart\Collection\Collection_Category();
        $categories = $categoryCollection->getAll();

        $this->renderWithLayout($view, [
            'page_title' => 'Import / Export Data',
            'categories' => $categories
        ]);
    }

    public function export()
    {
        $filename = "products_export_" . date('Y-m-d') . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Headers
        fputcsv($output, ['sku', 'name', 'price', 'description', 'stock', 'created_at']);

        // Fetch all products
        $productResource = new \EasyCart\Resource\Resource_Product();
        $products = \EasyCart\Database\QueryBuilder::select($productResource->getTable(), ['sku', 'name', 'price', 'description', 'stock', 'created_at'])
            ->orderBy('entity_id', 'ASC')
            ->fetchAll();

        foreach ($products as $product) {
            fputcsv($output, $product);
        }

        fclose($output);
        exit;
    }

    public function import()
    {
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Please upload a valid CSV file.';
            $this->redirect('/admin/import-export');
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');

        if (!$handle) {
            $_SESSION['error'] = 'Could not open file.';
            $this->redirect('/admin/import-export');
        }

        $headers = fgetcsv($handle);
        $requiredKeys = ['sku', 'name', 'price']; // Minimal required
        $headerMap = array_flip(array_map('strtolower', $headers));

        // Validation
        foreach ($requiredKeys as $key) {
            if (!isset($headerMap[$key])) {
                $_SESSION['error'] = "Missing required column: $key";
                fclose($handle);
                $this->redirect('/admin/import-export');
            }
        }

        $successCount = 0;
        $errors = [];
        $line = 1;

        $productResource = new \EasyCart\Resource\Resource_Product();
        $table = $productResource->getTable();

        while (($row = fgetcsv($handle)) !== false) {
            $line++;
            $data = [];
            foreach ($headerMap as $key => $index) {
                $data[$key] = $row[$index] ?? null;
            }

            if (empty($data['sku']) || empty($data['name'])) {
                $errors[] = "Line $line: Missing SKU or Name.";
                continue;
            }

            try {
                // Generate URL Key
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name'])));
                // Check if slug exists ? Unique constant handles it, but safer to append if needed.
                // For now, rely on unique constraint, catch error if duplicate.

                // Check if SKU exists
                $existing = \EasyCart\Database\QueryBuilder::select($table, ['entity_id'])
                    ->where('sku', '=', $data['sku'])
                    ->fetchOne();

                $dbData = [
                    'sku' => $data['sku'],
                    'name' => $data['name'],
                    'price' => (float) ($data['price'] ?? 0),
                    'stock' => (int) ($data['stock'] ?? 0),
                    'description' => $data['description'] ?? '',
                    'url_key' => $slug,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $productId = null;

                if ($existing) {
                    $productId = $existing['entity_id'];
                    // Update slug only if name changed? Or always?
                    // Updating slug might break old links. Maybe keep old slug if exists?
                    // For now, update it.
                    \EasyCart\Database\QueryBuilder::update($table, $dbData)
                        ->where('entity_id', '=', $productId)
                        ->execute();
                } else {
                    $dbData['created_at'] = date('Y-m-d H:i:s');
                    $dbData['is_active'] = true; // Default
                    $dbData['is_new'] = true;
                    // Handle duplicate slug on insert
                    // Simple retry with suffix
                    try {
                        $productId = \EasyCart\Database\QueryBuilder::insert($table, $dbData)
                            ->executeInsert('catalog_product_entity_entity_id_seq');
                    } catch (\Exception $e) {
                        if (strpos($e->getMessage(), 'url_key') !== false) {
                            $dbData['url_key'] .= '-' . time();
                            $productId = \EasyCart\Database\QueryBuilder::insert($table, $dbData)
                                ->executeInsert('catalog_product_entity_entity_id_seq');
                        } else {
                            throw $e;
                        }
                    }
                }

                // Handle Image (catalog_product_image)
                if (!empty($data['image_url']) && $productId) {
                    // Check if image exists
                    $existingImg = \EasyCart\Database\QueryBuilder::select('catalog_product_image', ['image_id'])
                        ->where('product_entity_id', '=', $productId)
                        ->where('image_path', '=', $data['image_url'])
                        ->fetchOne();

                    if (!$existingImg) {
                        // Set others to not primary
                        \EasyCart\Database\QueryBuilder::update('catalog_product_image', ['is_primary' => false])
                            ->where('product_entity_id', '=', $productId)
                            ->execute();

                        \EasyCart\Database\QueryBuilder::insert('catalog_product_image', [
                            'product_entity_id' => $productId,
                            'image_path' => $data['image_url'],
                            'is_primary' => true,
                            'created_at' => date('Y-m-d H:i:s')
                        ])->execute();
                    }
                }

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Line $line: " . $e->getMessage();
            }
        }

        fclose($handle);

        $_SESSION['success'] = "Successfully processed $successCount products.";
        if (!empty($errors)) {
            $_SESSION['import_errors'] = array_slice($errors, 0, 10); // Limit errors shown
        }

        $this->redirect('/admin/import-export');
    }
}
