<?php

declare(strict_types=1);

namespace MkdBot\Domain\Enum;

/**
 * Тип сообщения для дублирования Max->Telegram
 */
enum MessageType: string
{
    case Text = 'text';
    case Photo = 'photo';
    case Document = 'document';
}
