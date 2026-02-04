<?php

namespace EasyCart\Repositories;

use EasyCart\Core\Database;
use EasyCart\Database\Queries;

class WishlistRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function get($userId)
    {
        $stmt = $this->pdo->prepare(Queries::WISHLIST_GET_BY_USER);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function add($userId, $productId)
    {
        $stmt = $this->pdo->prepare(Queries::WISHLIST_ADD);
        $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
    }

    public function remove($userId, $productId)
    {
        $stmt = $this->pdo->prepare(Queries::WISHLIST_REMOVE);
        $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
    }
}
