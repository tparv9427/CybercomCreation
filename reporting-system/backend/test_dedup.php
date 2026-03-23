<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$row = ['SKU' => 'TEST-001', 'Name' => 'Original Item'];
$job = new \App\Jobs\ProcessCsvBatch([], [], 'test_file');

$method = new \ReflectionMethod(\App\Jobs\ProcessCsvBatch::class, 'generateId');
$method->setAccessible(true);

$id1 = $method->invoke($job, $row, 0);
$id2 = $method->invoke($job, $row, 0);

echo "\n--- DEDUPLICATION TEST RESULTS ---\n";
echo "ID for first run:  $id1 \n";
echo "ID for second run: $id2 \n";
echo "MATCH: " . ($id1 === $id2 ? '✅ SUCCESS (Identical IDs)' : '❌ FAILURE (IDs differ)') . "\n";
