<?php

declare(strict_types=1);

namespace Api\Domain\Interfaces;

interface QueueInterface
{
    public function enqueue(array $message): void;

    public function dequeue(): ?array;
}
