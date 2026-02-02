<?php

namespace EasyCart\Repositories;

use EasyCart\Core\Database;

class WishlistRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function get($userId)
    {
        $stmt = $this->pdo->prepare("SELECT product_id FROM wishlists WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function add($userId, $productId)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO wishlists (user_id, product_id) 
            VALUES (:user_id, :product_id)
            ON CONFLICT (user_id, product_id) DO NOTHING
        ");
        $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
    }

    public function remove($userId, $productId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM wishlists WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
    }
}
