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
            'telegram_bot_token' => $_ENV['TELEGRAM_BOT_TOKEN'],
            'rabbitmq_host' => $_ENV['RABBITMQ_HOST'],
            'rabbitmq_port' => (int)$_ENV['RABBITMQ_AMQP_PORT'],
            'rabbitmq_user' => $_ENV['RABBITMQ_USER'],
            'rabbitmq_password' => $_ENV['RABBITMQ_PASSWORD'],
            'ngrok_api_url' => $_ENV['NGROK_API_URL'],
            'ngrok_port' => (int)$_ENV['NGROK_PORT'],
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
     * Возвращает токен Telegram бота
     */
    public static function getTelegramBotToken(): string
    {
        return self::$config['telegram_bot_token'];
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
     * Возвращает URL API ngrok
     */
    public static function getNgrokApiUrl(): string
    {
        return self::$config['ngrok_api_url'];
    }

    /**
     * Возвращает порт ngrok
     */
    public static function getNgrokPort(): int
    {
        return self::$config['ngrok_port'];
    }
}
