<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Infrastructure\Queue\RabbitMqTaskQueue;

class RabbitMqTaskQueueFactory
{
    /**
     * @throws \Exception
     */
    public function create(): RabbitMqTaskQueue
    {
        return new RabbitMqTaskQueue(
            host: getenv('RABBITMQ_HOST') ?: 'rabbitmq',
            port: (int) (getenv('RABBITMQ_PORT') ?: 5672),
            user: getenv('RABBITMQ_USER') ?: 'guest',
            password: getenv('RABBITMQ_PASSWORD') ?: 'guest',
        );
    }
}