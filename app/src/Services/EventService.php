<?php

declare(strict_types=1);

namespace App\Services;

use App\Application;
use App\Forms\EventSearch;
use App\Models\Event;
use App\Repositories\EventRepositoryInterface;
use DomainException;

/**
 * Class EventService
 * @package App\Services
 */
class EventService implements EventServiceInterface
{
    /**
     * @var EventRepositoryInterface
     */
    private EventRepositoryInterface $repository;

    /**
     * @throws DomainException
     */
    public function __construct()
    {
        $this->repository = Application::$app->getComponent('eventRepository');
    }

    /**
     * @inheritdoc
     */
    public function create(Event $event): void
    {
        $this->repository->create($event);
    }

    /**
     * @inheritdoc
     */
    public function search(EventSearch $eventSearch): ?Event
    {
        return $this->repository->search($eventSearch);
    }
}
