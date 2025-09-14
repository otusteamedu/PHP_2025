<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistense\Mapping;

use App\Domain\User;
use App\Domain\UserIdentityMap;
use DateTimeImmutable;
use PDO;

final readonly class UserMapper
{
    public function __construct(private PDO $db, private UserIdentityMap $identityMap)
    {

    }

    public function save(User $user): void
    {
        $sql = 'INSERT INTO "user" (name, email, password, role, created_at, updated_at) VALUES (:name, :email, :password, :role, :created_at, :updated_at)';
        $statement = $this->db->prepare($sql);
        $statement->bindParam(":name", $user->name);
        $statement->bindParam(":email", $user->email);
        $statement->bindParam(":password", $user->password);
        $statement->bindParam(":role", $user->role);
        $createdAt = $user->createdAt->format('Y-m-d H:i:s');
        $updatedAt = $user->updatedAt->format('Y-m-d H:i:s');
        $statement->bindParam(":created_at", $createdAt);
        $statement->bindParam(":updated_at", $updatedAt);
        $statement->execute();
        $user->id = (int)$this->db->lastInsertId();

        $this->identityMap->add($user);
    }

    public function findByEmail(string $email): ?User
    {
        $sql = 'SELECT * FROM "user" WHERE email = :email';
        $statement = $this->db->prepare($sql);
        $statement->bindParam(":email", $email);
        $statement->execute();
        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $user = new User(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            role: $data['role'],
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at']),
        );

        $this->identityMap->add($user);

        return $user;
    }

    public function deleteById(int $id): void
    {
        $query = 'DELETE FROM "user" WHERE id = :id';
        $statement = $this->db->prepare($query);
        $statement->bindParam(":id", $id);
        $statement->execute();
    }

    public function find(int $id): ?User
    {
        $user = $this->identityMap->get($id);
        if ($user !== null) {
            return $user;
        }

        $sql = 'SELECT * FROM "user" WHERE id = :id';
        $statement = $this->db->prepare($sql);
        $statement->bindParam(":id", $id);
        $statement->execute();

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $user = new User(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            role: $data['role'],
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at']),
        );

        $this->identityMap->add($user);

        return $user;
    }

    public function findById($id): void
    {
        var_dump($this->identityMap->get($id));
    }
}
