<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

/**
 * Интерфейс репозитория идемпотентности webhook
 */
interface ProcessedWebhookRepositoryInterface
{
    /**
     * Атомарно проверяет и записывает webhook как обработанный.
     * Выполняет INSERT ... ON CONFLICT DO NOTHING — исключает race condition.
     *
     * @param string $messageMid Идентификатор сообщения (mid)
     * @param string $messengerType Тип мессенджера ('max', 'telegram')
     * @return bool true — строка вставлена (webhook новый, можно обрабатывать),
     *             false — конфликт (webhook уже обработан)
     */
    public function tryAcquire(string $messageMid, string $messengerType): bool;
}
