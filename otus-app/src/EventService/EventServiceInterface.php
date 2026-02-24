<?php

declare(strict_types=1);

namespace App\EventService;

use Exception;
use JsonException;

interface EventServiceInterface
{
    public const EVENT_NAME_LIST = [
        'event',
    ];

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function addJsonEvent(string $eventName, array $data, mixed $priority): void;

    /**
     * @throws Exception
     */
    public function addEvent(string $eventName, mixed $data, mixed $priority): void;

    public function clearAllEvents(): void;

    public function getEventListByEventName(string $eventName): ?array;
}
