<?php

declare(strict_types=1);

namespace App\Infrastructure\Queue\Config;

final readonly class RabbitMqConfig
{
    public function __construct(
        public string $host,
        public int $port,
        public string $user,
        public string $password,
    )
    {
    }
}
