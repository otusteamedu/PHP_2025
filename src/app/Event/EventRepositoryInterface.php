<?php

namespace App\Event;

interface EventRepositoryInterface
{
    public function save(Event $event): void;
    public function findAll(): array;
    public function clear(): void;
    public function findBestMatch(array $params): ?Event;
}