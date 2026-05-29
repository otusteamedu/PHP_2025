<?php

declare(strict_types=1);

namespace MkdBot\Application\DTO;

/**
 * DTO callback из Max (из MessageCallbackUpdate)
 */
class MaxCallbackDTO
{
    /**
     * @param array $payload Распарсенный JSON payload (пример: {"action":"confirm","type":"feature","step":"awaiting_subject"})
     * @param int $userId ID пользователя
     * @param int $chatId ID чата
     * @param string|null $callbackId ID callback для ответа (может быть пустым в Max API)
     * @param string|null $userName Имя пользователя (из callback->user->getFullName())
     */
    public function __construct(
        public readonly array $payload,
        public readonly int $userId,
        public readonly int $chatId,
        public readonly ?string $callbackId = null,
        public readonly ?string $userName = null,
    ) {
    }
}
