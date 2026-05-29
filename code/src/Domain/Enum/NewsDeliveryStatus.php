<?php

declare(strict_types=1);

namespace MkdBot\Domain\Enum;

/**
 * Статус доставки новости: pending -> sent | failed
 */
enum NewsDeliveryStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';
}
