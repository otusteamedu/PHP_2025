<?php

declare(strict_types=1);

namespace MyTestApp\DataMapper;

use PDO;
use PDOStatement;

class UserMapper
{
    private PDO          $pdo;

    private PDOStatement $selectAllStatement;
    
    private PDOStatement $selectStatement;

    private PDOStatement $insertStatement;

    private PDOStatement $updateStatement;

    private PDOStatement $deleteStatement;

    private $user_array = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->selectAllStatement = $pdo->prepare(
            'SELECT * FROM users'
        );
        $this->selectStatement = $pdo->prepare(
            'SELECT * FROM users WHERE id = ?'
        );
        $this->insertStatement = $pdo->prepare(
            'INSERT INTO users (`name`, email) VALUES (?, ?)'
        );
        $this->updateStatement = $pdo->prepare(
            'UPDATE users SET `name` = ?, email = ? WHERE id = ?'
        );
        $this->deleteStatement = $pdo->prepare(
            'DELETE FROM users WHERE id = ?'
        );
    }

    public function findAll(): array
    {
        $this->selectAllStatement->setFetchMode(PDO::FETCH_ASSOC);
        $this->selectAllStatement->execute();

        while ($result = $this->selectAllStatement->fetch()) {
            $this->user_array[$result['id']] = new User(
                $result['id'],
                $result['name'],
                $result['email'],
            );
        };

        return $this->user_array;
    }

    public function findById(int $id): User
    {
        $this->selectStatement->setFetchMode(PDO::FETCH_ASSOC);
        $this->selectStatement->execute([$id]);

        $result = $this->selectStatement->fetch();

        return new User(
            $result['id'],
            $result['name'],
            $result['email'],
        );
    }

    public function insert(array $rawUserData): User
    {
        $this->insertStatement->execute([
            $rawUserData['first_name'],
            $rawUserData['last_name'],
            $rawUserData['email'],
        ]);

        return new User(
            (int)$this->pdo->lastInsertId(),
            $rawUserData['first_name'],
            $rawUserData['last_name'],
            $rawUserData['email'],
        );
    }

    public function update(User $user): bool
    {
        return $this->updateStatement->execute([
            $user->getName(),
            $user->getEmail(),
            $user->getId(),
        ]);
    }

    public function delete(User $user): bool
    {
        return $this->deleteStatement->execute([$user->getId()]);
    }
}
