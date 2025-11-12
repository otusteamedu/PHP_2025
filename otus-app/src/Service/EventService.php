<?php

namespace App\Service;

use App\Enum\EventStatusEnum;
use App\Enum\QueueNameEnum;
use App\Interface\DataServiceInterface;
use App\Interface\QueueServiceInterface;
use DateTimeImmutable;
use Exception;
use JsonException;

class EventService
{
    public function __construct(
        private QueueServiceInterface $queueService,
        private DataServiceInterface $dataService,
    ) {
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function addEvent(array $eventData): string
    {
        $id = time() . random_int(1, 1000);

        $eventData['id'] = $id;
        $eventData['status'] = EventStatusEnum::IN_PROGRESS;
        $eventData['updated_at'] = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $this->dataService->addJsonEvent($id, $eventData);

        $this->queueService->publish(
            QueueNameEnum::EVENT_QUEUE,
            json_encode($eventData, JSON_THROW_ON_ERROR),
        );

        return $id;
    }

    /**
     * @throws JsonException
     */
    public function getEvent(string $eventId): array
    {
        $eventData = $this->dataService->getEventById($eventId);

        return json_decode($eventData, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    public function finishEvent(string $eventId): void
    {
        $currentEvent = $this->getEvent($eventId);
        $currentEvent['status'] = EventStatusEnum::FINISHED;

        $this->dataService->updateJsonEvent($eventId, $currentEvent);
    }
}
