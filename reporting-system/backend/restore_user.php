<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::first();
if ($user) {
    $user->tenant_id = null;
    $user->save();
    echo "User tenant_id cleared. Original data should be visible now.\n";
}
