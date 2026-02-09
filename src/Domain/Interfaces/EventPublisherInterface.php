<?php

namespace Restaurant\Domain\Interfaces;

interface EventPublisherInterface
{
    public function publishOrderEvent(int $orderId, string $status, string $timestamp): void;
}
