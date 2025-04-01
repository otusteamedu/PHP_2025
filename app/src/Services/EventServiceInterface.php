<?php

declare(strict_types=1);

namespace App\Services;

use App\Forms\EventSearch;
use App\Models\Event;

/**
 * Interface EventServiceInterface
 * @package App\Services
 */
interface EventServiceInterface
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
