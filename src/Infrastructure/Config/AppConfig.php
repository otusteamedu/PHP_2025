<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

use Dotenv\Dotenv;

final class AppConfig
{
    private static array $config = [];

    /**
     * Загружает конфигурацию из .env файла
     */
    public static function load(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
        $dotenv->load();

        self::$config = [
            'app_name' => $_ENV['APP_NAME'] ?? 'REST API with Queues and Swagger documentation',
            'app_env' => $_ENV['APP_ENV'] ?? 'development',
            'app_debug' => filter_var($_ENV['APP_DEBUG'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
            'rabbitmq_host' => $_ENV['RABBITMQ_HOST'],
            'rabbitmq_port' => (int)($_ENV['RABBITMQ_PORT']),
            'rabbitmq_user' => $_ENV['RABBITMQ_USER'],
            'rabbitmq_password' => $_ENV['RABBITMQ_PASSWORD'],
            'storage_path' => $_ENV['STORAGE_PATH'] ?? '/var/www/html/storage',
            'log_level' => $_ENV['LOG_LEVEL'] ?? 'info',
            'log_file' => $_ENV['LOG_FILE'] ?? '/var/www/html/storage/logs/app.log',
            'api_version' => $_ENV['API_VERSION'] ?? 'v1',
            'api_base_url' => $_ENV['API_BASE_URL'] ?? 'http://localhost:8080/api',
        ];
    }

    /**
     * Возвращает значение конфигурации по ключу
     */
    public static function get(string $key, $default = null)
    {
        return self::$config[$key] ?? $default;
    }

    /**
     * Возвращает конфигурацию RabbitMQ
     */
    public static function getRabbitMQConfig(): array
    {
        return [
            'host' => self::$config['rabbitmq_host'],
            'port' => self::$config['rabbitmq_port'],
            'user' => self::$config['rabbitmq_user'],
            'password' => self::$config['rabbitmq_password'],
        ];
    }

    /**
     * Возвращает путь к хранилищу
     */
    public static function getStoragePath(): string
    {
        return self::$config['storage_path'];
    }

    /**
     * Возвращает уровень логирования
     */
    public static function getLogLevel(): string
    {
        return self::$config['log_level'];
    }

    /**
     * Возвращает путь к файлу логов
     */
    public static function getLogFile(): string
    {
        return self::$config['log_file'];
    }
}
