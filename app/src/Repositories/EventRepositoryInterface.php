<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Forms\EventSearch;
use App\Models\Event;

/**
 * Interface EventRepositoryInterface
 * @package App\Repositories
 */
interface EventRepositoryInterface
{
    /**
     * @param Event $event
     * @return void
     */
    public function create(Event $event): void;

    /**
     * @param EventSearch $eventSearch
     * @return Event|null
     */
    public function search(EventSearch $eventSearch): ?Event;
}
