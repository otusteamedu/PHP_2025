<?php

namespace App\Repositories;

use \PDO;

class UserRepository
{
    public function __construct(private PDO $pdo) {}

    public function getByEmail(string $email): ?array
    {
        $sql = "SELECT id, email, password FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        return $user;
    }

    public function save(string $email, string $passwordHash): int
    {
        $sql = "INSERT INTO users (email, password) 
                VALUES (:email, :password)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'password' => $passwordHash,
        ]);

        return $this->pdo->lastInsertId();
    }
}