<?php

namespace EasyCart\Services;

use EasyCart\Core\Database;
use EasyCart\Database\Queries;
use EasyCart\Repositories\CategoryRepository;
use PDO;
use Exception;

class ImportExportService
{
    private $pdo;
    private $categoryRepo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->categoryRepo = new CategoryRepository();
    }

    /**
     * Parse CSV file and return data
     */
    public function parseCsv($filePath)
    {
        $data = [];
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $headers = fgetcsv($handle, 1000, ",");

            // Normalize headers
            $headers = array_map(function ($h) {
                return strtolower(trim(str_replace(' ', '_', $h)));
            }, $headers);

            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) === count($headers)) {
                    $data[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }

    /**
     * Import products from array data
     */
    public function importProducts($products)
    {
        $successCount = 0;
        $skipCount = 0;
        $errors = [];

        foreach ($products as $index => $row) {
            try {
                if (empty($row['sku']) || empty($row['name']) || empty($row['price'])) {
                    $errors[] = "Row " . ($index + 1) . ": Missing required fields (sku, name, price)";
                    $skipCount++;
                    continue;
                }

                $this->pdo->beginTransaction();

                // 1. Insert Product
                $stmt = $this->pdo->prepare(Queries::PRODUCT_INSERT_RETURNING_ID);
                $stmt->execute([
                    ':sku' => $row['sku'],
                    ':name' => $row['name'],
                    ':price' => (float) $row['price'],
                    ':stock' => (int) ($row['stock'] ?? 0),
                    ':description' => $row['description'] ?? '',
                    ':is_active' => (isset($row['is_active']) && $row['is_active'] == '0') ? 'false' : 'true'
                ]);

                $entityId = $stmt->fetchColumn();

                if (!$entityId) {
                    // SKU already exists (ON CONFLICT DO NOTHING)
                    $this->pdo->rollBack();
                    $skipCount++;
                    continue;
                }

                // 2. Insert Attributes (Brand, Color, etc.)
                $attributes = ['brand', 'color', 'size', 'material'];
                foreach ($attributes as $attr) {
                    if (!empty($row[$attr])) {
                        $attrStmt = $this->pdo->prepare(Queries::PRODUCT_ATTRIBUTE_INSERT);
                        $attrStmt->execute([
                            ':entity_id' => $entityId,
                            ':code' => $attr,
                            ':value' => $row[$attr]
                        ]);
                    }
                }

                // 3. Insert Image
                if (!empty($row['image_url'])) {
                    $imgStmt = $this->pdo->prepare(Queries::PRODUCT_IMAGE_INSERT);
                    $imgStmt->execute([
                        ':entity_id' => $entityId,
                        ':image_path' => $row['image_url'],
                        ':is_primary' => TRUE
                    ]);
                }

                // 4. Link Category
                if (!empty($row['category'])) {
                    $category = $this->categoryRepo->findByName($row['category']);
                    // If category doesn't exist, maybe create it? For now, skip linking.
                    if ($category) {
                        $catStmt = $this->pdo->prepare(Queries::CATEGORY_PRODUCT_INSERT);
                        $catStmt->execute([
                            ':category_id' => $category['entity_id'],
                            ':product_id' => $entityId
                        ]);
                    }
                }

                $this->pdo->commit();
                $successCount++;

            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                $errors[] = "Row " . ($index + 1) . " Error: " . $e->getMessage();
                $skipCount++;
            }
        }

        return [
            'total' => count($products),
            'success' => $successCount,
            'skipped' => $skipCount,
            'errors' => $errors
        ];
    }

    /**
     * Export products to CSV
     */
    public function exportCsv()
    {
        $stmt = $this->pdo->prepare(Queries::PRODUCT_EXPORT_SELECT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($products)) {
            return null;
        }

        // Output buffer to file
        $filename = "products_export_" . date('Y-m-d_H-i') . ".csv";
        $fp = fopen('php://temp', 'w'); // Use temp stream

        // Headers
        fputcsv($fp, array_keys($products[0]));

        // Rows
        foreach ($products as $row) {
            fputcsv($fp, $row);
        }

        rewind($fp);
        $csvContent = stream_get_contents($fp);
        fclose($fp);

        return [
            'filename' => $filename,
            'content' => $csvContent
        ];
    }
}
