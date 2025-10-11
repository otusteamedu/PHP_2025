<?php

declare(strict_types=1);

namespace Dinargab\Homework11\Repositories;

use Dinargab\Homework11\Model\Event;

interface EventRepositoryInterface
{
    public function add(Event $event): bool;
    public function deleteAll(): bool;
    public function findByConditions(array $conditions): ?Event; 
}