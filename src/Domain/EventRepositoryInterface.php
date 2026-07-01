<?php

declare(strict_types=1);

namespace App\Domain;


interface EventRepositoryInterface
{
    public function nextId(): int;
    public function save(Event $event): void;

    public function clear(): void;

    public function findByParams(array $params): ?Event;
}