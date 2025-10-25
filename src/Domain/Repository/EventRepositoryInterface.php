<?php

namespace Domain\Repository;

use Domain\Entity\Event;

interface EventRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?Event;

    public function save(Event $event): int;

    public function delete(Event $event): void;
}