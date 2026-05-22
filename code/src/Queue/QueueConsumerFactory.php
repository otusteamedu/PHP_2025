<?php

declare(strict_types=1);

namespace App\Queue;

/**
 * Фабрика сервиса чтения сообщений из RabbitMQ
 */
final class QueueConsumerFactory
{
    /**
     * Создает сервис чтения сообщений по настройкам окружения
     *
     * @return RabbitMqConsumer
     */
    public function create(): RabbitMqConsumer
    {
        return new RabbitMqConsumer(
            getenv('RABBITMQ_HOST') ?: 'rabbitmq',
            (int) (getenv('RABBITMQ_PORT') ?: '5672'),
            getenv('RABBITMQ_USER') ?: '',
            getenv('RABBITMQ_PASSWORD') ?: '',
            getenv('RABBITMQ_QUEUE') ?: 'requests',
            getenv('RABBITMQ_FAILED_QUEUE') ?: 'requests_failed',
        );
    }
}
