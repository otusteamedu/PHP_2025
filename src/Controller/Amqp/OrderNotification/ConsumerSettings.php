<?php

declare(strict_types=1);

namespace App\Controller\Amqp\OrderNotification;

readonly class ConsumerSettings
{
    public function __construct(
        public string $queueName,
        public int $maxAttempts,
        public int $prefetchCount,
    ) {
    }
}
