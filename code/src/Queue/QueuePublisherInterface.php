<?php

declare(strict_types=1);

namespace App\Queue;

/**
 * Контракт сервиса отправки сообщений в очередь.
 */
interface QueuePublisherInterface
{
    /**
     * Публикует сообщение в очередь.
     *
     * @param string $message Тело сообщения.
     */
    public function publish(string $message): void;
}
