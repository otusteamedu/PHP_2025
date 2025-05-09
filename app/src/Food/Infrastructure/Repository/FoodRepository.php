<?php

declare(strict_types=1);

namespace App\Food\Infrastructure\Repository;

use App\Food\Domain\Aggregate\FoodInterface;
use App\Food\Domain\Repository\FoodRepositoryInterface;
use App\Food\Infrastructure\Mapper\FoodMapper;
use App\Shared\Infrastructure\Database\Db;

class FoodRepository implements FoodRepositoryInterface
{
    private string $table = 'food_food';

    public function __construct(private readonly Db $db, private FoodMapper $foodMapper)
    {
    }

    public function add(FoodInterface $food): void
    {
        try {
            $this->db->connection->beginTransaction();
            $sql = "INSERT INTO $this->table (id, status, title, type, order_id, ingredients, status_created_at, status_updated_at) 
                            VALUES (:id, :status, :title, :type, :order_id, :ingredients,:status_created_at, :status_updated_at);";
            $statement = $this->db->connection->prepare($sql);
            $statement->bindValue(':id', $food->getId());
            $statement->bindValue(':status', $food->getCookingStatus()->value);
            $statement->bindValue(':title', $food->getTitle()->getValue());
            $statement->bindValue(':type', $food->getType()->value);
            $statement->bindValue(':order_id', $food->getOrderId());
            $statement->bindValue(':order_id', $food->getOrderId());
            $statement->bindValue(':ingredients', json_encode($food->getIngredients()));
            $statement->bindValue(':status_created_at', $food->getStatusCreatedAt()->format(DATE_ATOM));
            $statement->bindValue(':status_updated_at', $food->getStatusUpdatedAt()->format(DATE_ATOM));
            if (!$statement->execute()) {
                throw new \Exception('Food could not be added into database');
            }
            $this->db->connection->commit();
        } catch (\Throwable $exception) {
            $this->db->connection->rollBack();
            throw $exception;
        }
    }

    public function findById(string $foodId): ?FoodInterface
    {
        // TODO: Implement findById() method.
    }

    public function getByOrderId(string $orderId): array
    {
        $foodItems = [];
        $sql = "SELECT * FROM  $this->table WHERE order_id = :order_id;";
        $statement = $this->db->connection->prepare($sql);
        $statement->bindValue(':order_id', $orderId);
        $statement->execute();

        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $foodItems[] = $this->foodMapper->foodMap($row);
        }

        return $foodItems;
    }
}
