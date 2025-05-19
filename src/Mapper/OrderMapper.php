<?php declare(strict_types=1);

namespace App\Mapper;

use App\Database\PostgresConnection;
use App\Entity\Order;
use PDO;

class OrderMapper
{
    private PDO $pdo;
    private OrderIdentityMap $identityMap;

    public function __construct(OrderIdentityMap $identityMap)
    {
        $this->pdo = PostgresConnection::getInstance();
        $this->identityMap = $identityMap;
    }

    public function fetchById(int $id): ?Order
    {
        if ($this->identityMap->has($id)) {
            return $this->identityMap->get($id);
        }

        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $order = new Order(
            (int)$data['user_id'],
            (float)$data['total_amount'],
            new \DateTimeImmutable($data['created_at']),
            (int)$data['id']
        );

        $this->identityMap->add($order);

        return $order;
    }

    public function fetchAllByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $orders = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!$this->identityMap->has((int)$data['id'])) {
                $order = new Order(
                    (int)$data['user_id'],
                    (float)$data['total_amount'],
                    new \DateTimeImmutable($data['created_at']),
                    (int)$data['id']
                );
                $this->identityMap->add($order);
            } else {
                $order = $this->identityMap->get((int)$data['id']);
            }

            $orders[] = $order;
        }

        return $orders;
    }

    public function fetchAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders");
        $stmt->execute();
        $orders = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = (int)$data['id'];

            if ($this->identityMap->has($id)) {
                $orders[] = $this->identityMap->get($id);
                continue;
            }

            $order = new Order(
                (int)$data['user_id'],
                (float)$data['total_amount'],
                new \DateTimeImmutable($data['created_at']),
                $id
            );

            $this->identityMap->add($order);
            $orders[] = $order;
        }

        return $orders;
    }

    public function save(Order $order): void
    {
        if ($order->getId() === null) {
            $this->insert($order);
        } else {
            $this->update($order);
        }
    }

    private function insert(Order $order): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO orders (user_id, total_amount, created_at) 
            VALUES (:userId, :totalAmount, DEFAULT) 
            RETURNING id, created_at
        ");

        $stmt->execute([
            'userId' => $order->getUserId(),
            'totalAmount' => $order->getTotalAmount()
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new \RuntimeException("Insert order failed");
        }

        $reflectionClass = new \ReflectionClass($order);
        $propertyId = $reflectionClass->getProperty('id');
        $propertyId->setAccessible(true);
        $propertyId->setValue($order, (int)$result['id']);

        $propertyCreatedAt = $reflectionClass->getProperty('createdAt');
        $propertyCreatedAt->setAccessible(true);
        $propertyCreatedAt->setValue($order, new \DateTimeImmutable($result['created_at']));

        $this->identityMap->add($order);
    }

    private function update(Order $order): void
    {
        $stmt = $this->pdo->prepare("UPDATE orders SET user_id = :userId, total_amount = :totalAmount WHERE id = :id");

        $stmt->execute([
            'id' => $order->getId(),
            'userId' => $order->getUserId(),
            'totalAmount' => $order->getTotalAmount()
        ]);

        $this->identityMap->add($order);
    }
}
