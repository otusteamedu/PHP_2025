<?php

declare(strict_types=1);

namespace App\Food\Infrastructure\Repository;

use App\Food\Domain\Aggregate\Order\FoodOrder;
use App\Food\Domain\Repository\FoodOrderRepositoryInterface;
use App\Food\Infrastructure\Mapper\OrderMapper;
use App\Shared\Infrastructure\Database\Db;

class FoodOrderRepository implements FoodOrderRepositoryInterface
{
    private string $table = 'food_food_order';

    public function __construct(
        private readonly Db $db,
        private readonly OrderMapper $orderMapper,
    ) {
    }

    public function add(FoodOrder $order): void
    {
        try {
            $this->db->connection->beginTransaction();
            $exist = $this->findById($order->getId());
            if ($exist) {
                $sql = "UPDATE $this->table SET status = :status, status_created_at = :status_created_at, 
                 status_updated_at = :status_updated_at  WHERE id = :id;";
            } else {
                $sql = "INSERT INTO $this->table (id, status, status_created_at, status_updated_at) 
                            VALUES (:id, :status, :status_created_at, :status_updated_at);";
            }
            $statement = $this->db->connection->prepare($sql);
            $statement->bindValue(':id', $order->getId());
            $statement->bindValue(':status', $order->getStatus()->value);
            $statement->bindValue(':status_created_at', $order->getStatusCreatedAt()->format(DATE_ATOM));
            $statement->bindValue(':status_updated_at', $order->getStatusUpdatedAt()->format(DATE_ATOM));
            if (!$statement->execute()) {
                throw new \Exception('Order could not be added into database');
            }
            $this->db->connection->commit();
        } catch (\Throwable $exception) {
            $this->db->connection->rollBack();
            throw $exception;
        }
    }

    public function findById(string $orderId): ?FoodOrder
    {
        $sql = "SELECT * FROM $this->table WHERE id = :id;";
        $statement = $this->db->connection->prepare($sql);
        $statement->bindValue(':id', $orderId);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }

        return $this->orderMapper->orderMap($result);
    }

    private function checkUserExists(string $orderId): bool
    {
        $sql = "SELECT * FROM $this->table WHERE id = :id;";
        $statement = $this->db->connection->prepare($sql);
        $statement->bindValue(':id', $orderId);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        return false !== $result;
    }
}
