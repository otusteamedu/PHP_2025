<?php

declare(strict_types=1);

namespace MkdBot\Domain\ValueObject;

/**
 * Результат отправки сообщения в мессенджер — Domain Value Object
 * Инкапсулирует данные о созданном сообщении без зависимости от библиотеки
 */
readonly class SendMessageResult
{
    public function __construct(
        private string $messageId = '',
        private int $timestamp = 0,
    ) {
    }

    /**
     * Идентификатор отправленного сообщения (mid в Max API)
     */
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * Временная метка отправки сообщения (Unix timestamp в секундах)
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * Создаёт пустой результат (для случаев, когда данные недоступны)
     */
    public static function empty(): self
    {
        return new self('', 0);
    }
}
