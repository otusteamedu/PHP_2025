<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Order;

use App\Domain\OrderNotification\Exception\Input\OrderIdException;

readonly class OrderId
{
    private int $value;

    /**
     * @throws OrderIdException
     */
    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw OrderIdException::lessThanZero();
        }

        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }
}
