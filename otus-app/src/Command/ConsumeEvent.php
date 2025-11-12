<?php

namespace App\Command;

use App\Service\EventService;
use Exception;
use JsonException;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumeEvent
{
    public function __construct(
        private EventService $eventService,
    ) {
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function process(AMQPMessage $queueMessage): void
    {
        $decodedData = json_decode($queueMessage->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $eventId = $decodedData['id'] ?? null;

        if (empty($eventId)) {
            throw new Exception('Invalid queue data');
        }

        $this->eventService->finishEvent($eventId);
    }
}
