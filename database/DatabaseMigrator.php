<?php

namespace Database;

use PDO;
use PDOException;
use RuntimeException;

class DatabaseMigrator
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        try {
            $this->pdo = $pdo ?? DatabaseConnection::getInstance()->getPDO();
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to initialize DatabaseMigrator: " . $e->getMessage());
        }
    }

    public function runMigrations(): void
    {
        try {
            $this->pdo->beginTransaction();

            $this->createUsersTable();

            $this->pdo->commit();
            echo "Migrations completed successfully!\n";
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new RuntimeException("Migration failed: " . $e->getMessage());
        }
    }

    private function createUsersTable(): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(30) NOT NULL,
            email VARCHAR(50) NOT NULL UNIQUE,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_username (username),
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        SQL;

        $this->pdo->exec($sql);
        echo "Table 'users' created or already exists\n";
    }
}