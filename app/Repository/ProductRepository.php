<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

class ProductRepository extends BaseRepository
{
    /**
     * @param int $id
     * @return array
     */
    public function getProductById(int $id): array
    {
        $sql = "SELECT * FROM `products` WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        return $product ?: [];
    }
}