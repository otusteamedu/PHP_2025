<?php

namespace App\Core;

class Config
{
    private static array $config = [];

    public static function load(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        self::$config = [
            'telegram' => [
                'bot_token' => $_ENV['TELEGRAM_BOT_TOKEN'],
                'webhook_url' => $_ENV['TELEGRAM_WEBHOOK_URL'],
            ],
            'database' => [
                'host' => $_ENV['DB_HOST'],
                'port' => $_ENV['DB_PORT'],
                'database' => $_ENV['DB_NAME'],
                'username' => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASSWORD'],
            ],
            'app' => [
                'env' => $_ENV['APP_ENV'],
                'debug' => $_ENV['APP_DEBUG'] === 'true',
            ],
        ];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }
}