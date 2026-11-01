<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Order;

readonly class Money
{
    public function __construct(
        private float $amount,
        private string $currency,
    ) {
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function format(): string
    {
        return number_format(
            num: $this->amount,
            decimals: 2,
            decimal_separator: ',',
            thousands_separator: ' ',
        ) . ' ' . $this->currency; // "15 499,90 RUB"
    }
}
