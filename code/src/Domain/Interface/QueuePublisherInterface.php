<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

use MkdBot\Domain\Enum\QueueNameType;

/**
 * Интерфейс публикации сообщений в очередь (абстракция для v2 миграции на Managed Queue)
 */
interface QueuePublisherInterface
{
    /**
     * Публикует сообщение в указанную очередь
     */
    public function publish(QueueNameType $queue, array $message): void;
}
