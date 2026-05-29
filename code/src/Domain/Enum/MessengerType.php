<?php

declare(strict_types=1);

namespace MkdBot\Domain\Enum;

/**
 * Тип мессенджера: Max или Telegram
 */
enum MessengerType: string
{
    case Max = 'max';
    case Telegram = 'telegram';
}
