<?php

declare(strict_types=1);

namespace App\DataMappers;

use App\DB;
use App\Entities\Order;
use App\Entities\User;
use App\GlobalIdentityMap;
use \PDO;
use \PDOStatement;

class OrderMapper
{
    private static OrderMapper $instance;

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

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self(DB::getPdo());
        }

        return self::$instance;
    }

    public function findById(int $id): null|Order
    {
        $result = GlobalIdentityMap::exists(Order::class, $id);

        if ($result !== null) {
            return $result;
        }

        $this->selectStatement->setFetchMode(PDO::FETCH_ASSOC);
        $this->selectStatement->execute([$id]);

        $result = $this->selectStatement->fetch();

        if ($result === false) {
            return null;
        }

        $order = new Order(
            $result['id'],
            $result['user_id']
        );

        GlobalIdentityMap::add($order);

        return $order;
    }

    public function fetchByUser(int $userId): array
    {
        $this->selectStatementByUser->setFetchMode(PDO::FETCH_ASSOC);
        $this->selectStatementByUser->execute([$userId]);

        $result = $this->selectStatementByUser->fetchAll();

        return \array_map(function ($item) {
            $order = new Order(
                $item['id'],
                $item['user_id']
            );

            GlobalIdentityMap::add($order);

            return $order;
        },
            $result
        );
    }

    public function insert(User $user): Order
    {
        $this->insertStatement->execute([
            $user->getId()
        ]);

        return new Order(
            (int)$this->pdo->lastInsertId(),
            $user->getId()
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
