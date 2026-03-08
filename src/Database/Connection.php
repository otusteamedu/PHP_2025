<?php

namespace Igor\Test\Database;

use PDO;
use PDOException;

/**
 * Класс для подключения к базе данных MySQL через PDO
 */
class Connection
{
    private static ?PDO $instance = null;
    private static array $config = [];

    /**
     * Приватный конструктор для реализации Singleton
     */
    private function __construct()
    {
    }

    /**
     * Установка конфигурации подключения
     *
     * @param array $config Конфигурация БД
     */
    public static function setConfig(array $config): void
    {
        self::$config = $config;
    }

    /**
     * Получение экземпляра PDO (Singleton)
     *
     * @return PDO
     * @throws PDOException
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::createConnection();
        }

        return self::$instance;
    }

    /**
     * Создание подключения к базе данных
     *
     * @throws PDOException
     */
    private static function createConnection(): void
    {
        if (empty(self::$config)) {
            // Попытка загрузить конфигурацию из файла
            $configPath = __DIR__ . '/../../database/config.php';
            if (file_exists($configPath)) {
                self::$config = require $configPath;
            } else {
                // Значения по умолчанию
                self::$config = [
                    'host' => 'localhost',
                    'dbname' => 'test_db',
                    'username' => 'root',
                    'password' => '',
                    'charset' => 'utf8mb4',
                    'options' => [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                ];
            }
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            self::$config['host'],
            self::$config['dbname'],
            self::$config['charset']
        );

        self::$instance = new PDO(
            $dsn,
            self::$config['username'],
            self::$config['password'],
            self::$config['options'] ?? []
        );
    }

    /**
     * Закрытие подключения
     */
    public static function close(): void
    {
        self::$instance = null;
    }

    /**
     * Предотвращение клонирования
     */
    private function __clone()
    {
    }

    /**
     * Предотвращение десериализации
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
