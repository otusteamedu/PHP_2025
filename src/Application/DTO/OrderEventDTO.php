<?php

namespace Restaurant\Application\DTO;

class OrderEventDTO
{
    public function __construct(
        public readonly int $orderId,
        public readonly string $status,
        public readonly string $timestamp
    ) {
    }
}
