<?php declare(strict_types=1);

namespace App\Mapper;

use App\Database\PostgresConnection;
use App\Entity\User;
use PDO;

class UserMapper
{
    private PDO $pdo;
    private UserIdentityMap $identityMap;

    public function __construct(UserIdentityMap $identityMap)
    {
        $this->pdo = PostgresConnection::getInstance();
        $this->identityMap = $identityMap;
    }

    public function fetchById(int $id): ?User
    {
        if ($this->identityMap->has($id)) {
            return $this->identityMap->get($id);
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $user = new User(
            $data['name'],
            $data['email'],
            (int)$data['id']
        );

        $this->identityMap->add($user);

        return $user;
    }

    public function save(User $user): User
    {
        if ($user->getId() === null) {
            return $this->insert($user);
        }

        $this->update($user);
        return $user;
    }

    private function insert(User $user): User
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

        $user = new User(
            $user->getName(),
            $user->getEmail(),
            (int)$result['id']
        );

        $this->identityMap->add($user);

        return $user;
    }

    private function update(User $user): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");

        $stmt->execute([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ]);

        $this->identityMap->add($user);
    }
}
