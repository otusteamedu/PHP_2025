<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Recipient;

interface RecipientInterface
{
    public function value(): string;
}
