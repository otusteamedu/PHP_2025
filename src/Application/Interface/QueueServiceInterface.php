<?php

declare(strict_types=1);

namespace App\Application\Interface;

interface QueueServiceInterface
{
    /**
     * Публикует сообщение в очередь
     */
    public function publish(string $queueName, array $message): void;

    /**
     * Получает сообщения из очереди
     */
    public function consume(string $queueName, callable $callback): void;
}
