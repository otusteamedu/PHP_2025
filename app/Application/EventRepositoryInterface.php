<?php

namespace App\Application;

use App\Domain\Entities\Event;

interface EventRepositoryInterface
{
    public function save(Event $event): void;
    public function fetchAll(): array;
    public function deleteAll(): void;
}