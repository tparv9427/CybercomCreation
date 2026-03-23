<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'admin@example.com';
$pwd = 'password';

$u = User::where('email', $email)->first();

if (!$u) {
    echo "USER NOT FOUND: $email\n";
    exit(1);
}

$match = Hash::check($pwd, $u->password);
echo "CHECK $email: " . ($match ? "MATCH" : "FAIL") . "\n";
echo "HASH IN DB: " . $u->password . "\n";
