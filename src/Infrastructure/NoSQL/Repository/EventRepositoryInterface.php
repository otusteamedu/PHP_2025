<?php

declare(strict_types=1);

namespace App\Infrastructure\NoSQL\Repository;

use App\Domain\Model\AddEventModel;
use App\Domain\Model\GetEventModel;
use App\Domain\Model\SearchEventModel;

interface EventRepositoryInterface
{
    public const string EVENTS_KEY_PREFIX = 'events';
    public const string EVENT_CONDITIONS_KEY = 'event_conditions';
    public const string SEARCH_EVENT_PARAMS_KEY = 'search_event_params';

    public function addEvent(AddEventModel $addEventModel): bool;

    public function getEvent(SearchEventModel $searchEventModel): GetEventModel;

    public function deleteAllEvents(): bool;
}
