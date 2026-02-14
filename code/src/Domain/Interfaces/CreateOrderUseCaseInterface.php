<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Application\DTO\CreateOrderRequest;
use App\Application\DTO\OrderResponse;

interface CreateOrderUseCaseInterface
{
    public function execute(CreateOrderRequest $request): OrderResponse;
}
