<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use PDO;

class PostgresUserRepository implements UserRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findBy(array $criteria): ?User
    {
        $query = 'SELECT u.id, u.name, u.surname, u.role, p.title as post_title
                  FROM users u
                  LEFT JOIN post p ON u.id_post = p.id_post';

        if (!empty($criteria)) {
            $query .= ' WHERE ';
            $whereConditions = [];
            foreach (array_keys($criteria) as $key) {
                $whereConditions[] = "u.{$key} = :{$key}";
            }
            $query .= implode(' AND ', $whereConditions);
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($criteria);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapToUser($data);
    }

    public function findById(int $id): ?User
    {
        return $this->findBy(['id' => $id]);
    }

    /**
     * Преобразует массив данных из БД в объект User.
     */
    private function mapToUser(array $data): User
    {
        return new User(
            id: (int)$data['id'],
            name: $data['name'],
            surname: $data['surname'],
            role: $data['role'],
            postTitle: $data['post_title'] ?? null
        );
    }
}
