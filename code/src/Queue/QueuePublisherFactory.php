<?php

declare(strict_types=1);

namespace App\Queue;

/**
 * Фабрика сервиса отправки сообщений в RabbitMQ.
 */
final class QueuePublisherFactory
{
    /**
     * Создает сервис отправки сообщений по настройкам окружения.
     *
     * @return QueuePublisherInterface
     */
    public function create(): QueuePublisherInterface
    {
        return new RabbitMqPublisher(
            getenv('RABBITMQ_HOST') ?: 'rabbitmq',
            (int) (getenv('RABBITMQ_PORT') ?: '5672'),
            getenv('RABBITMQ_USER') ?: '',
            getenv('RABBITMQ_PASSWORD') ?: '',
            getenv('RABBITMQ_QUEUE') ?: 'statement_requests',
        );
    }
}
