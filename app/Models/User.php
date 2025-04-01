<?php

namespace App\Models;

use InvalidArgumentException;
use PDO;
use Database\DatabaseConnection;
use PDOException;

class User
{
    private ?int $id = null;
    private string $username;
    private string $email;
    private bool $isActive;
    private string $createdAt;

    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? DatabaseConnection::getInstance()->getPDO();
    }

    public function save(): bool
    {
        try {
            if ($this->id === null) {
                return $this->insert();
            }

            return $this->update();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    private function insert(): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, is_active, created_at) 
             VALUES (:username, :email, :is_active, NOW())"
        );

        $success = $stmt->execute([
            ':username' => $this->username,
            ':email' => $this->email,
            ':is_active' => $this->isActive
        ]);

        if ($success) {
            $this->id = (int)$this->db->lastInsertId();
        }

        return $success;
    }

    private function update(): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET username = :username, email = :email, is_active = :is_active WHERE id = :id"
        );

        $currentUser = self::find($this->id);

        $params = [
            ':id' => $this->id
        ];

        if ($this->username !== $currentUser->getUsername()) {
            $params[':username'] = $this->username;
        }

        if ($this->email !== $currentUser->getEmail()) {
            $params[':email'] = $this->email;
        }

        if ($this->isActive !== $currentUser->isActive()) {
            $params[':is_active'] = $this->isActive;
        }

        $columnsToUpdate = [];
        foreach ($params as $key => $value) {
            if ($key !== ':id') {
                $columnsToUpdate[] = "$key = $key";
            }
        }

        if (empty($columnsToUpdate)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(", ", $columnsToUpdate) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public static function all(): array
    {
        $db = DatabaseConnection::getInstance()->getPDO();
        $stmt = $db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function find(int $id): ?self
    {
        $db = DatabaseConnection::getInstance()->getPDO();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $user = new self($db);
        $user->id = (int)$data['id'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->isActive = (bool)$data['is_active'];
        $user->createdAt = $data['created_at'];

        return $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = trim($username);
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format");
        }
        $this->email = trim($email);
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}