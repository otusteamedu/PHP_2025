<?php

declare(strict_types=1);

namespace MkdBot\Application\DTO;

/**
 * DTO сообщения из Max (из MessageCreatedUpdate)
 */
class MaxMessageDTO
{
    /**
     * @param int|null $userId ID отправителя (null в канале)
     * @param string|null $userName Имя отправителя (null в канале)
     * @param string $text Текст сообщения
     * @param string $mid ID сообщения в Max (формат: mid.[0-9a-f]+)
     * @param int $chatId ID чата
     * @param string $chatType Тип чата (channel, dialog, group)
     * @param array<array{type: string, url: string|null, token: string|null, filename: string|null, size: int|null}> $attachments Вложения
     * @param string|null $messageUrl Публичная ссылка на пост в канале
     */
    public function __construct(
        public readonly ?int $userId,
        public readonly ?string $userName,
        public readonly string $text,
        public readonly string $mid,
        public readonly int $chatId,
        public readonly string $chatType,
        public readonly array $attachments = [],
        public readonly ?string $messageUrl = null,
    ) {
    }
}
