<?php

declare(strict_types=1);

namespace MkdBot\Domain\Enum;

/**
 * Имена очередей RabbitMQ
 */
enum QueueNameType: string
{
    case TelegramForward = 'mkd.telegram.forward';
    case RagQuery = 'mkd.rag.query';
    case NewsDelivery = 'mkd.news.delivery';
    case Fallback = 'mkd.fallback';
}
