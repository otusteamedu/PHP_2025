<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../../config/database.php';
            
            try {
                if ($config['driver'] === 'sqlite') {
                    self::$connection = new PDO(
                        'sqlite:' . $config['database'],
                        null,
                        null,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_PERSISTENT => true
                        ]
                    );
                }
                
                // Инициализация таблицы
                self::initializeDatabase();
                
            } catch (PDOException $e) {
                http_response_code(500);
                error_log($e->getMessage());
                throw new Exception('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$connection;
    }

    private static function initializeDatabase(): void
    {
        $sql = file_get_contents(__DIR__ . '/../../init.sql');
        self::$connection->exec($sql);
    }

}
