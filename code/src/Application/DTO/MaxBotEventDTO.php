<?php

declare(strict_types=1);

namespace MkdBot\Application\DTO;

/**
 * DTO события бота из Max (bot_started / bot_stopped)
 * Без chatType — bot_started/bot_stopped всегда в диалоге
 */
class MaxBotEventDTO
{
    /**
     * @param int $chatId ID диалога (не чата!)
     * @param int $userId ID пользователя
     * @param string|null $userName Имя пользователя
     * @param string $eventType Тип события (bot_started, bot_stopped)
     * @param string|null $payload Deep-link данные (до 128 символов, v1 не используется)
     */
    public function __construct(
        public readonly int $chatId,
        public readonly int $userId,
        public readonly ?string $userName,
        public readonly string $eventType,
        public readonly ?string $payload = null,
    ) {
    }
}
