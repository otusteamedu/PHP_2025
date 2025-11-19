<?php

namespace App\Repositories;

use App\Models\UserState;
use App\Core\Database\Database;
use PDO;

class UserStateRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        
        // Создаем таблицу для состояний если не существует
        $this->createTableIfNotExists();
    }

    public function save(UserState $state): bool
    {
        $sql = "INSERT INTO user_states (user_id, state, temp_data, updated_at) 
                VALUES (:user_id, :state, :temp_data, NOW())
                ON DUPLICATE KEY UPDATE state = :state_update, temp_data = :temp_data_update, updated_at = NOW()";

        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'user_id' => $state->getUserId(),
            'state' => $state->getState(),
            'temp_data' => json_encode($state->getTempData(), JSON_UNESCAPED_UNICODE),
            'state_update' => $state->getState(),
            'temp_data_update' => json_encode($state->getTempData(), JSON_UNESCAPED_UNICODE)
        ]);
    }

    public function findByUserId(int $userId): ?UserState
    {
        $stmt = $this->db->prepare("SELECT * FROM user_states WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        $tempData = [];
        if (!empty($data['temp_data'])) {
            $tempData = json_decode($data['temp_data'], true) ?? [];
        }

        return new UserState(
            (int)$data['user_id'],
            $data['state'],
            $tempData
        );
    }

    public function delete(int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM user_states WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }

    private function createTableIfNotExists(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS user_states (
                user_id BIGINT PRIMARY KEY,
                state VARCHAR(50) NOT NULL DEFAULT 'none',
                temp_data TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_state (state)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";

        try {
            $this->db->exec($sql);
        } catch (\Exception $e) {
            error_log("Error creating user_states table: " . $e->getMessage());
        }
    }
}