<?php

declare(strict_types=1);

namespace MkdBot\Domain\Enum;

/**
 * Статус новости: pending -> delivering -> delivered
 */
enum NewsStatus: string
{
    case Pending = 'pending';
    case Delivering = 'delivering';
    case Delivered = 'delivered';
}
