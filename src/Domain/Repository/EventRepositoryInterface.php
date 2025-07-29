<?php

declare(strict_types=1);

namespace Kamalo\EventsService\Domain\Repository;

use Kamalo\EventsService\Domain\Entity\Event;

interface EventRepositoryInterface
{
    public function findAll(): array;
    public function findByParams(array $params): ?Event;
    public function add(Event $event): void;
    public function clear(): void;
}
