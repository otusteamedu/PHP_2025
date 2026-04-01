<?php

declare(strict_types=1);

namespace App\Interface;

use Exception;
use JsonException;

interface DataServiceInterface
{
    /**
     * @throws JsonException
     * @throws Exception
     */
    public function addJsonEvent(string $eventId, array $data, int $secAmount = 3600): void;

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function updateJsonEvent(string $eventId, array $data, int $secAmount = 3600): void;

    /**
     * @throws Exception
     */
    public function addEvent(string $eventId, mixed $data, int $secAmount = 3600): void;

    public function getEventById(string $eventId): ?string;
}
