<?php
require_once __DIR__ . '/../vendor/autoload.php';

use EasyCart\Core\Database;

$pdo = Database::getInstance()->getConnection();
$usersFile = __DIR__ . '/../legacy/users.json';

if (!file_exists($usersFile)) {
    die("Users file not found.\n");
}

$users = json_decode(file_get_contents($usersFile), true);

echo "Migrating " . count($users) . " users...\n";

$stmt = $pdo->prepare("INSERT INTO users (id, email, password, name, created_at) VALUES (:id, :email, :password, :name, :created_at) ON CONFLICT (email) DO NOTHING");

foreach ($users as $user) {
    // Ensure created_at fits timestamp format
    $createdAt = isset($user['created_at']) ? $user['created_at'] : date('Y-m-d H:i:s');

    $stmt->execute([
        ':id' => $user['id'],
        ':email' => $user['email'],
        ':password' => $user['password'],
        ':name' => $user['name'],
        ':created_at' => $createdAt
    ]);
}

echo "Done migrating users.\n";
