<?php

declare(strict_types=1);

namespace App\Repository;

use App\Config\DatabaseConfig;
use PDO;
use PDOException;

final class UserRepository
{
    private PDO $pdo;

    public function __construct(DatabaseConfig $config)
    {
        try {
            $this->pdo = new PDO(
                $config->getDsn(),
                $config->username,
                $config->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            // Создаём таблицы, если их нет (один раз при старте)
            $this->createTablesIfNotExists();
        } catch (PDOException $e) {
            throw new \RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }

    private function createTablesIfNotExists(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS `users_skyd` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `code` VARCHAR(100) NOT NULL,
                `fio` VARCHAR(255) DEFAULT NULL,
                `gender` VARCHAR(50) DEFAULT NULL,
                `group_id` INT DEFAULT NULL,
                `card` VARCHAR(100) DEFAULT NULL,
                `photo` VARCHAR(255) DEFAULT NULL,
                `status` VARCHAR(500) DEFAULT NULL,
                `deleted` TINYINT(1) DEFAULT 0,
                `deleted_at` DATETIME DEFAULT NULL,
                UNIQUE KEY `uniq_code` (`code`),
                INDEX `idx_fio` (`fio`),
                INDEX `idx_deleted` (`deleted`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS `skyd_visits` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` VARCHAR(100) DEFAULT NULL,
                `user_name` VARCHAR(255) DEFAULT NULL,
                `event_type` VARCHAR(100) DEFAULT NULL,
                `event_time` DATETIME NOT NULL,
                `device_name` VARCHAR(255) DEFAULT NULL,
                `raw_json` TEXT,
                INDEX `idx_user_id` (`user_id`),
                INDEX `idx_event_time` (`event_time`),
                INDEX `idx_device` (`device_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function saveUsers(array $users): bool
    {
        if (empty($users)) {
            return true;
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO `users_skyd` 
            (`code`, `fio`, `gender`, `group_id`, `card`, `photo`, `status`)
            VALUES (:code, :fio, :gender, :group_id, :card, :photo, :status)
            ON DUPLICATE KEY UPDATE 
                fio = VALUES(fio),
                gender = VALUES(gender),
                group_id = VALUES(group_id),
                card = VALUES(card),
                photo = VALUES(photo),
                status = VALUES(status),
                deleted = 0,
                deleted_at = NULL
        ");

        foreach ($users as $user) {
            $stmt->execute([
                ':code'     => $user['code'],
                ':fio'      => $user['fio'] ?? null,
                ':gender'   => $user['gender'] ?? null,
                ':group_id' => $user['groupId'] ?? null,
                ':card'     => $user['numOfCard'] ?? null,
                ':photo'    => $user['faceURL'] ?? null,
                ':status'   => $user['status'] ?? 'активен',
            ]);
        }

        return true;
    }

    public function getAllUsers(): array
    {
        $stmt = $this->pdo->query("
            SELECT code, fio, gender, group_id, card, photo, status 
            FROM users_skyd 
            WHERE deleted = 0 
            ORDER BY fio ASC
        ");

        return $stmt->fetchAll();
    }

    public function softDeleteMissingUsers(array $activeCodes): void
    {
        if (empty($activeCodes)) {
            return;
        }

        $placeholders = str_repeat('?,', count($activeCodes) - 1) . '?';
        $stmt = $this->pdo->prepare("
            UPDATE users_skyd 
            SET deleted = 1, deleted_at = NOW() 
            WHERE code NOT IN ($placeholders) AND deleted = 0
        ");

        $stmt->execute($activeCodes);
    }
}