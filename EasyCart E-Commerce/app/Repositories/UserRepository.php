<?php

namespace EasyCart\Repositories;

/**
 * UserRepository
 * 
 * Migrated from: includes/config.php (lines 65-152)
 */
class UserRepository
{
    private $usersFile;

    public function __construct()
    {
        $this->usersFile = __DIR__ . '/../../data/users.json';
    }

    public function getAll()
    {
        if (file_exists($this->usersFile)) {
            $json = file_get_contents($this->usersFile);
            return json_decode($json, true);
        }
        
        // Default users
        return [
            1 => [
                'id' => 1,
                'email' => 'demo@easycart.com',
                'password' => password_hash('demo123', PASSWORD_DEFAULT),
                'name' => 'Demo User',
                'created_at' => '2026-01-01'
            ],
            2 => [
                'id' => 2,
                'email' => 'john.doe@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'name' => 'John Doe',
                'created_at' => '2026-01-05'
            ]
        ];
    }

    public function find($userId)
    {
        $users = $this->getAll();
        return isset($users[$userId]) ? $users[$userId] : null;
    }

    public function findByEmail($email)
    {
        $users = $this->getAll();
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }

    public function save($users)
    {
        $dataDir = dirname($this->usersFile);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0777, true);
        }
        file_put_contents($this->usersFile, json_encode($users, JSON_PRETTY_PRINT));
    }

    public function create($email, $password, $name)
    {
        $users = $this->getAll();
        
        // Check if email exists
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return false;
            }
        }
        
        $userId = $this->getNextId($users);
        $users[$userId] = [
            'id' => $userId,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'name' => $name,
            'created_at' => date('Y-m-d')
        ];
        
        $this->save($users);
        return $userId;
    }

    private function getNextId($users)
    {
        if (empty($users)) return 1;
        return max(array_keys($users)) + 1;
    }
}
