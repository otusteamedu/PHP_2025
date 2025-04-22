<?php declare(strict_types=1);

namespace App\Mapper;

use App\Database\PostgresConnection;
use App\Entity\User;
use PDO;

class UserMapper
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PostgresConnection::getInstance();
    }

    public function fetchById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $user = new User();
        $user->setId((int)$data['id']);
        $user->setName($data['name']);
        $user->setEmail($data['email']);

        return $user;
    }

    public function save(User $user): void
    {
        if ($user->getId() === null) {
            $this->insert($user);
        } else {
            $this->update($user);
        }
    }

    private function insert(User $user): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (name, email) VALUES (:name, :email) RETURNING id"
        );

        $stmt->execute([
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new \RuntimeException("Insert user failed");
        }

        $user->setId((int)$result['id']);
    }

    private function update(User $user): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");

        $stmt->execute([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ]);
    }
}
