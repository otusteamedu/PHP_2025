<?php

declare(strict_types=1);

namespace App\Application\Order;

final readonly class CreateOrderCommand
{
    /**
     * @param array<string> $ingredients
     */
    public function __construct(
        public string $name,
        public int  $quantity,
        public array $ingredients,
    ) {
    }
}
