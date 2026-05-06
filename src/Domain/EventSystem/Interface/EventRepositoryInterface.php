<?php

declare(strict_types=1);

namespace App\Domain\EventSystem\Interface;

use App\Domain\EventSystem\Model\AddEventModel;
use App\Domain\EventSystem\Model\GetEventModel;
use App\Domain\EventSystem\Model\SearchEventModel;

interface EventRepositoryInterface
{
    public const string EVENTS_KEY_PREFIX = 'events';
    public const string EVENT_CONDITIONS_KEY = 'event_conditions';
    public const string SEARCH_EVENT_PARAMS_KEY = 'search_event_params';

    public function addEvent(AddEventModel $addEventModel): bool;

    public function getEvent(SearchEventModel $searchEventModel): GetEventModel;

    public function deleteAllEvents(): bool;
}
