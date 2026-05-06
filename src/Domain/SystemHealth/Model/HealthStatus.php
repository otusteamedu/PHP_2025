<?php

declare(strict_types=1);

namespace App\Domain\SystemHealth\Model;

class HealthStatus
{
    public function __construct(
        public readonly bool $isHealthy,
        public readonly string $message = '',
        public readonly array $details = [],
    ) {
    }
}
