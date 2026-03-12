<?php

declare(strict_types=1);

namespace Queues\Application\Interfaces;

interface QueueInterface
{
    public function enqueue(string $payload): void;

    public function dequeue(): ?string;
}
