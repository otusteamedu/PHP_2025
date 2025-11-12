<?php

declare(strict_types=1);

namespace App\Service;

use App\Interface\DataServiceInterface;
use Exception;
use JsonException;
use Redis;

class RedisService implements DataServiceInterface
{
    private Redis $redis;

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect(getenv('REDIS_HOST'), (int) getenv('REDIS_PORT'));
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function addJsonEvent(string $eventId, array $data, int $secAmount = 3600): void
    {
        $encodedData = json_encode($data, JSON_THROW_ON_ERROR);
        $this->addEvent($eventId, $encodedData, $secAmount);
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function updateJsonEvent(string $eventId, array $data, int $secAmount = 3600): void
    {
        $encodedData = json_encode($data, JSON_THROW_ON_ERROR);
        $this->addEvent($eventId, $encodedData, $secAmount);
    }

    /**
     * @throws Exception
     */
    public function addEvent(string $eventId, mixed $data, int $secAmount = 3600): void
    {
        $this->redis->set($eventId, $data, $secAmount);
    }

    public function getEventById(string $eventId): ?string
    {
        return $this->redis->get($eventId);
    }
}
