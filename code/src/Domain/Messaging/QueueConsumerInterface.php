<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\Messaging;

interface QueueConsumerInterface {
    /**
     * @param string $queueName
     * @param callable $callback
     */
    public function consume(string $queueName, callable $callback): void;

    public function close(): void;
}