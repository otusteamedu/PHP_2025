<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Recipient;

enum MagicPhone: string
{
    case AlwaysDeliver = '+40700000001';
    case AlwaysFail    = '+40700000002';
    case SlowDelivery  = '+40700000003';
    case InvalidNumber = '+40700000004';
    case RateLimited   = '+40700000005';
    case Timeout       = '+40700000006';

    public function number(): string
    {
        return $this->value;
    }
}
