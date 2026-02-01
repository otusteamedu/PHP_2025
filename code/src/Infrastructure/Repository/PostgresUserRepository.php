<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use PDO;

class PostgresUserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToUser($data) : null;
    }

    public function findByLogin(string $login): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE login = :login');
        $stmt->execute(['login' => $login]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToUser($data) : null;
    }

    private function mapToUser(array $data): User
    {
        return User::fromState($data);
    }

    public function findBy(array $criteria): ?User
    {
        $conditions = [];
        $params = [];
        foreach ($criteria as $key => $value) {
            $conditions[] = "$key = :$key";
            $params[$key] = $value;
        }
        $whereClause = implode(' AND ', $conditions);

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE $whereClause");
        $stmt->execute($params);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToUser($data) : null;
    }
}
