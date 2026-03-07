<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Queue;

interface QueueInterface
{
    /**
     * @param string $connection
     * @param Payload $payload
     */
    public function push(string $connection, Payload $payload): void;

    /**
     * @param string $connection
     * @param Payload $payload
     */
    public function pull(string $connection, Payload $payload): void;
}
