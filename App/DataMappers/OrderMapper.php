<?php

declare(strict_types=1);

namespace App\DataMappers;

use App\Entities\Order;
use \PDO;
use \PDOStatement;

class OrderMapper
{
    private PDO $pdo;

    private PDOStatement $selectStatement;
    private PDOStatement $selectStatementByUser;

    private PDOStatement $insertStatement;

    private PDOStatement $updateStatement;

    private PDOStatement $deleteStatement;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        $this->selectStatement = $pdo->prepare(
            'SELECT * FROM orders WHERE id = ?'
        );
        $this->selectStatementByUser = $pdo->prepare(
            'SELECT * FROM orders WHERE user_id = ?'
        );
        $this->insertStatement = $pdo->prepare(
            'INSERT INTO orders (user_id) VALUES (?, ?, ?)'
        );
        $this->updateStatement = $pdo->prepare(
            'UPDATE orders SET user_id = ? WHERE id = ?'
        );
        $this->deleteStatement = $pdo->prepare(
            'DELETE FROM orders WHERE id = ?'
        );
    }

    public function findById(int $id): Order
    {
        $this->selectStatement->setFetchMode(PDO::FETCH_ASSOC);
        $this->selectStatement->execute([$id]);

        $result = $this->selectStatement->fetch();

        return new Order(
            $result['id'],
            $result['user_id']
        );
    }

    public function findByUser(int $UserId): array
    {
        $this->selectStatementByUser->setFetchMode(PDO::FETCH_ASSOC);
        $this->selectStatementByUser->execute([$UserId]);

        $result = $this->selectStatementByUser->fetchAll();

        return \array_map(function ($item) {
            return new Order(
                $item['id'],
                $item['user_id']
            );
        },
            $result
        );
    }

    public function insert(array $rawUserData): Order
    {
        $this->insertStatement->execute([
            $rawUserData['user_id']
        ]);

        return new Order(
            (int)$this->pdo->lastInsertId(),
            (int)$rawUserData['user_id']
        );
    }

    public function update(Order $order): bool
    {
        return $this->updateStatement->execute([
            $order->getUserId(),
            $order->getId(),
        ]);
    }

    public function delete(Order $order): bool
    {
        return $this->deleteStatement->execute([$order->getId()]);
    }
}
