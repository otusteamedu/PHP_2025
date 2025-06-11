<?php
declare(strict_types=1);

namespace App;

use PDO;

class ProductGateway
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // метод массового получения информации из таблицы, результат которого возвращается в виде коллекции
    public function findAll(): ProductCollection
    {
        // данные будут загружаться по необходимости
        return new ProductCollection($this->pdo, "SELECT id, name, price, category_id FROM products");
    }
}
