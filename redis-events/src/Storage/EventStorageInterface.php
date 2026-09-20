<?php

declare(strict_types=1);

namespace App\Storage;

use App\Domain\Event;

interface EventStorageInterface
{
    public function add(Event $event): void;

    public function clear(): void;

    /** @param array<string, scalar> $params */
    public function findBest(array $params): ?Event;
}
