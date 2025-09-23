<?php

namespace App\EventSystem;

interface EventSystemRepository
{
    public function add(int $priority, array $conditions, array $event): string;
    public function get(string $id): ?array;
    public function getEventIdsByPriorityDesc(): array;
    public function clearAll();
}
