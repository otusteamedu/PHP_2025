<?php

namespace App\Model;

use App\Database\Database;
use PDO;
use PDOException;

class User
{
    private int $id;
    private int $bitrixId;
    private ?string $FIO;
    private ?string $username;

    public static function createOrUpdate(array $userData): self
    {
        $db = Database::getConnection();       
        // Проверяем существование пользователя
        $stmt = $db->prepare(
            "SELECT id FROM users WHERE bitrix_id = :bitrix_id"
        );
        $stmt->execute([':bitrix_id' => $userData['bitrix_id']]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Обновляем существующего пользователя
            $stmt = $db->prepare("
                UPDATE users SET                   
                    fio = :fio,
                    username = :username,
                WHERE bitrix_id = :bitrix_id
            ");
            
            $stmt->execute([
                ':bitrix_id' => $userData['bitrix_id'] ?? null,
                ':fio' => $userData['fio'] ?? null,
                ':username' => $userData['username'] ?? null,
            ]);
            
            return self::findByBitrixId($userData['bitrix_id']);
        } else {
            // Создаем нового пользователя
            file_put_contents('./logs.log', print_r($userData, true), FILE_APPEND);
            $stmt = $db->prepare("
                INSERT INTO users (bitrix_id, fio, username)
                VALUES (:bitrix_id, :fio, :username)
            ");
            try {
                $stmt->execute([
                    ':bitrix_id' => $userData['bitrix_id'],
                    ':fio' => $userData['fio'] ?? null,
                    ':username' => $userData['username'] ?? null,
                ]);
                file_put_contents('./logs.log', print_r( $userData, true), FILE_APPEND);
            } catch (PDOException $e) {
                http_response_code(500);
                error_log($e->getMessage());
                throw new Exception('Database connection failed: ' . $e->getMessage());
            }
            return self::findByBitrixId($userData['bitrix_id']);
        }
    }

    public static function findByBitrixId(int $bitrixId): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE bitrix_id = :bitrix_id");
        $stmt->execute([':bitrix_id' => $bitrixId]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return self::fromArray($data);
    }

    public static function findByUsername(string $username): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => '@'.$username]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return self::fromArray($data);
    }

    public static function deleteByBitrixId(int $bitrixId): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE bitrix_id = :bitrix_id");
        $stmt->execute([':bitrix_id' => $bitrixId]);
        
        return $stmt->rowCount() > 0;
    }

    public static function getAllUsers(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = [];

        while ($data = $stmt->fetch()) {
            $users[] = self::fromArray($data);
        }

        return $users;
    }

    public static function countUsers(): int
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        
        return (int) $result['count'];
    }

    private static function fromArray(array $data): self
    {
        $user = new self();
        $user->id = (int) $data['id'];
        $user->bitrixId = (int) $data['bitrix_id'];
        $user->FIO = $data['fio'];
        $user->username = $data['username'];

        return $user;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getBitrixId(): int { return $this->bitrixId; }
    public function getFIO(): ?string { return $this->FIO; }
    public function getUsername(): ?string { return $this->username; }

}
