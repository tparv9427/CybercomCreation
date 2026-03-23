<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Spatie PermissionServiceProvider: " . (class_exists('Spatie\Permission\PermissionServiceProvider') ? "FOUND" : "NOT FOUND") . "\n";
