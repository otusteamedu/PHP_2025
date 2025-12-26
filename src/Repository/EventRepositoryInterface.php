<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Event;

interface EventRepositoryInterface
{
    /**
     * @param Event $event
     * @return bool
     */
    public function save(Event $event): bool;

    /**
     * @return array
     */
    public function findAll(): array;

    /**
     * @return bool
     */
    public function clear(): bool;
}
