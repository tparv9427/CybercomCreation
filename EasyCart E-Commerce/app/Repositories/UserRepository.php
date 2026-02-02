<?php

namespace EasyCart\Repositories;

use EasyCart\Core\Database;
use PDO;

/**
 * UserRepository
 * 
 * Migrated to PostgreSQL
 */
class UserRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id");
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function create($data)
    {
        // Hash password if not provided hashed (defensive programming)
        // Checks if it looks like a hash (bcrypt starts with $2y$)
        $password = $data['password'];
        if (substr($password, 0, 4) !== '$2y$') {
            $password = password_hash($password, PASSWORD_DEFAULT);
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password, created_at) 
            VALUES (:name, :email, :password, NOW()) 
            RETURNING id, name, email, created_at
        ");

        try {
            $stmt->execute([
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':password' => $password
            ]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            // Likely duplicate email
            return false;
        }
    }
}
