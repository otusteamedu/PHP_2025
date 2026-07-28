<?php

declare(strict_types=1);

namespace App\Infrastructure\Queue\Config;


final readonly class RabbitMqConfigLoader
{
    public static function load(): RabbitMqConfig
    {
        return new RabbitMqConfig(
            host: getenv('RABBITMQ_HOST') ?: 'rabbitmq',
            port: (int) (getenv('RABBITMQ_PORT') ?: 5672),
            user: getenv('RABBITMQ_USER') ?: 'guest',
            password: getenv('RABBITMQ_PASSWORD') ?: 'guest',
        );
    }
}
