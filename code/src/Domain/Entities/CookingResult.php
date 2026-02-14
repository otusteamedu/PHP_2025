<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Entities\Order;

class CookingResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly Order $order,
        public readonly array $logs = []
    ) {}
}
