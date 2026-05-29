<?php

declare(strict_types=1);

namespace MkdBot\Application\DTO;

/**
 * DTO сообщения для дублирования Max->Telegram
 */
class ForwardMessageDTO
{
    /**
     * @param string $text Текст сообщения
     * @param array<array{type: string, url: string|null, token: string|null, filename: string|null, size: int|null}> $attachments Вложения
     * @param string $sourceMessageMid ID исходного сообщения в Max
     * @param int $chatId ID чата/канала в Max
     * @param string $chatType Тип чата (channel, dialog, group)
     * @param string|null $messageUrl Публичная ссылка на пост в канале (для «🔗 Оригинал»)
     */
    public function __construct(
        public readonly string $text,
        public readonly array $attachments = [],
        public readonly string $sourceMessageMid = '',
        public readonly int $chatId = 0,
        public readonly string $chatType = '',
        public readonly ?string $messageUrl = null,
    ) {
    }
}
