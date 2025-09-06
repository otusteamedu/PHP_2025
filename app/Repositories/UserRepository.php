<?php

namespace App\Repositories;

use \PDO;

class UserRepository
{
    public function __construct(private PDO $pdo) {}

    public function saveUser(string $email, string $passwordHash): int
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