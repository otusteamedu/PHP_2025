<?php

namespace Application\Event;

use DateTime;
use Domain\Entity\Event;
use Domain\Notifier\NotifierInterface;
use Domain\Repository\EventRepositoryInterface;

class EventCreateUseCase
{
    private EventRepositoryInterface $eventRepository;
    private NotifierInterface $notifier;

    public function __construct(
        EventRepositoryInterface $eventRepository,
        NotifierInterface $notifier
    ) {
        $this->eventRepository = $eventRepository;
        $this->notifier = $notifier;
    }

    public function run(Event $event): void {
        $id = $this->eventRepository->save($event);

        $message = json_encode([
            'id' => $id,
            'type' => $event->getType(),
            'title' => $event->getTitle(),
            'priority' => $event->getPriority(),
            'comment' => $event->getComment(),
            'createdAt' => (new DateTime())->format('Y-m-d H:i:s'),
            'updatedAt' => (new DateTime())->format('Y-m-d H:i:s'),
        ]);

        $this->notifier->notify($message);
    }
}