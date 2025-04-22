<?php declare(strict_types=1);

namespace App\Mapper;

use App\Database\PostgresConnection;
use App\Entity\Order;
use App\Entity\User;
use PDO;

class OrderMapper
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PostgresConnection::getInstance();
    }

    public function fetchById(int $id): ?Order
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $order = new Order();
        $order->setId($data['id']);
        $order->setUserId($data['user_id']);
        $order->setTotalAmount($data['total_amount']);
        $order->setCreatedAt($data['created_at']);

        return $order;
    }

    public function fetchAllByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $orders = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $order = new Order();
            $order->setId((int)$data['id']);
            $order->setUserId((int)$data['user_id']);
            $order->setTotalAmount((float)$data['total_amount']);
            $order->setCreatedAt(new \DateTimeImmutable($data['created_at']));
            $orders[] = $order;
        }

        return $orders;
    }

    public function fetchAllWithUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT o.id AS order_id,
            o.user_id AS order_user_id,
            o.total_amount AS order_total_amount,
            o.created_at AS order_created_at,
            u.id AS user_id,
            u.name AS user_name,
            u.email AS user_email
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.user_id = :user_id
        ");

        $stmt->execute(['user_id' => $userId]);
        $orders = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User();
            $user->setId((int)$data['user_name']);
            $user->setName($data['user_name']);
            $user->setEmail($data['user_email']);

            $order = new Order();
            $order->setId((int)$data['order_id']);
            $order->setUserId((int)$data['order_user_id']);
            $order->setTotalAmount((float)$data['order_total_amount']);
            $order->setCreatedAt(new \DateTimeImmutable($data['order_created_at']));
            $order->setUser($user);

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
            $order = new Order();
            $order->setId((int)$data['id']);
            $order->setUserId((int)$data['user_id']);
            $order->setTotalAmount((float)$data['total_amount']);
            $order->setCreatedAt(new \DateTimeImmutable($data['created_at']));
            $orders[] = $order;
        }

        return $orders;
    }

    public function save(Order $order): void
    {
        $this->insert($order);
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

        $order->setId((int)$result['id']);
        $order->setCreatedAt(new \DateTimeImmutable($result['created_at']));
    }
}
